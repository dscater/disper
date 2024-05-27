<?php

namespace App\Http\Controllers;

use App\Models\DetalleEntrenamiento;
use App\Models\Entrenamiento;
use App\Models\HistorialAccion;
use App\Models\Lugar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class EntrenamientoController extends Controller
{
    public $validacion = [
        "archivo" => "required|mimes:csv,xlsx,xls",
    ];

    public $mensajes = [];

    public function index()
    {
        return Inertia::render("Entrenamientos/Index");
    }

    public function listado()
    {
        $entrenamientos = Entrenamiento::select("entrenamientos.*")->where("status", 1)->get();
        return response()->JSON([
            "entrenamientos" => $entrenamientos
        ]);
    }

    public function byTipo(Request $request)
    {
        $entrenamientos = Entrenamiento::select("entrenamientos.*")->where("status", 1);
        if (isset($request->tipo) && trim($request->tipo) != "") {
            $entrenamientos = $entrenamientos->where("tipo", $request->tipo);
        }

        if ($request->order && $request->order == "desc") {
            $entrenamientos->orderBy("entrenamientos.id", "desc");
        }

        $entrenamientos = $entrenamientos->get();

        return response()->JSON([
            "entrenamientos" => $entrenamientos
        ]);
    }

    public function paginado(Request $request)
    {
        $search = $request->search;
        $entrenamientos = Entrenamiento::select("entrenamientos.*")->where("status", 1);

        if (trim($search) != "") {
            $entrenamientos->where("Entrenamiento", "LIKE", "%$search%");
            $entrenamientos->orWhereRaw("CONCAT(nombre,' ', paterno,' ', materno) LIKE ?", ["%$search%"]);
            $entrenamientos->orWhere("ci", "LIKE", "%$search%");
        }

        $entrenamientos = $entrenamientos->paginate($request->itemsPerPage);
        return response()->JSON([
            "entrenamientos" => $entrenamientos
        ]);
    }

    public function store(Request $request)
    {
        set_time_limit(0);
        $request->validate($this->validacion, $this->mensajes);
        $request['fecha_registro'] = date('Y-m-d');
        DB::beginTransaction();
        try {
            $archivo = $request->archivo;
            $extension = $archivo->getClientOriginalExtension();
            // recorrer archivo
            switch ($extension) {
                case 'xlsx':
                    $reader = new Xlsx();
                    break;
                case 'xls':
                    $reader = new Xls();
                    break;
                case 'csv':
                    $reader = new Csv();
                    break;
                default:
                    throw ValidationException::withMessages([
                        'error' =>  "Archivo no soportado"
                    ]);
            }
            $spreadsheet = $reader->load($archivo);
            $hoja = $spreadsheet->getActiveSheet();

            // crear el registro de entrenamiento
            $a_cod = Entrenamiento::getNuevoCodigo();
            $nuevo_entrenamiento = Entrenamiento::create([
                "cod" => $a_cod[0],
                "nro" => $a_cod[1],
                "total" => 0,
                "fecha_registro" => date("Y-m-d")
            ]);

            // recorrer las filas y celdas
            foreach ($hoja->getRowIterator() as $key_fila => $fila) {
                $celdaIterator = $fila->getCellIterator();
                $celdaIterator->setIterateOnlyExistingCells(false); // Esto es opcional para iterar sobre todas las celdas
                // registrar el detalle 
                $nuevos_datos = [];
                if ($key_fila > 1) {
                    foreach ($celdaIterator as $key_celda => $celda) {
                        // Log::debug("Celda $key_celda: " . $celda->getValue() . ' ');
                        //preparar los datos
                        if ($key_celda == 'D')
                            $nuevos_datos["capacidad"]  = $celda;

                        if ($key_celda == 'G')
                            $nuevos_datos["lugar"]  = $celda;

                        if ($key_celda == 'H')
                            $nuevos_datos["lat"]  = $celda;

                        if ($key_celda == 'I')
                            $nuevos_datos["lng"]  = $celda;

                        if ($key_celda == 'J')
                            $nuevos_datos["descripcion"]  = $celda != 'NO' ? $celda : '';

                        if ($key_celda == 'K') {
                            $fecha = date("Y-m-d", strtotime($celda));
                            $hora = date("H:i", strtotime($celda));
                            $nuevos_datos["fecha"]  = $fecha;
                            $nuevos_datos["hora"]  = $hora;
                        }

                        if ($key_celda == 'O') {
                            $nuevos_datos["ocupados"] = 0;
                            if (trim($celda)) {
                                $ocupados = json_decode($celda);
                                $nuevos_datos["ocupados"] = count($ocupados);
                            }
                        }
                    }

                    // verificar lugar
                    $lugar = Lugar::where("nombre", $nuevos_datos["lugar"])
                        ->where("lat", $nuevos_datos["lat"])
                        ->where("lng", $nuevos_datos["lng"])
                        ->get()->first();
                    if (!$lugar) {
                        $lugar = Lugar::create([
                            "nombre" => mb_strtoupper($nuevos_datos["lugar"]),
                            "descripcion" => mb_strtoupper($nuevos_datos["descripcion"]),
                            "capacidad" => $nuevos_datos["capacidad"],
                            "lat" => $nuevos_datos["lat"],
                            "lng" => $nuevos_datos["lng"],
                        ]);
                    }

                    // verificar detalle lugar, lat, lng, fecha y hora
                    $detalle_entrenamiento = DetalleEntrenamiento::where("lugar_id", $lugar->id)
                        ->where("fecha", $nuevos_datos["fecha"])
                        ->where("hora", $nuevos_datos["hora"])
                        ->get()->first();

                    if (!$detalle_entrenamiento) {
                        $nuevo_entrenamiento->detalle_entrenamientos()->create([
                            "lugar_id" => $lugar->id,
                            "ocupados" => $nuevos_datos["ocupados"],
                            "fecha" => $nuevos_datos["fecha"],
                            "hora" => $nuevos_datos["hora"],
                        ]);
                    }
                }
                echo PHP_EOL;
                sleep(1);
            }

            DB::commit();
            return redirect()->route("entrenamientos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(Entrenamiento $entrenamiento)
    {
    }

    public function update(Entrenamiento $entrenamiento, Request $request)
    {
        $this->validacion['ci'] = 'required|min:4|numeric|unique:entrenamientos,ci,' . $entrenamiento->id;
        if ($request->hasFile('foto')) {
            $this->validacion['foto'] = 'image|mimes:jpeg,jpg,png|max:2048';
        }
        $request->validate($this->validacion, $this->mensajes);
        DB::beginTransaction();
        try {
            $datos_original = HistorialAccion::getDetalleRegistro($entrenamiento, "entrenamientos");
            $entrenamiento->update(array_map('mb_strtoupper', $request->except('foto')));
            if ($request->hasFile('foto')) {
                $antiguo = $entrenamiento->foto;
                if ($antiguo != 'default.png') {
                    \File::delete(public_path() . '/imgs/entrenamientos/' . $antiguo);
                }
                $file = $request->foto;
                $nom_foto = time() . '_' . $entrenamiento->id . '.' . $file->getClientOriginalExtension();
                $entrenamiento->foto = $nom_foto;
                $file->move(public_path() . '/imgs/entrenamientos/', $nom_foto);
            }
            $entrenamiento->save();

            $datos_nuevo = HistorialAccion::getDetalleRegistro($entrenamiento, "entrenamientos");
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'MODIFICACIÓN',
                'descripcion' => 'EL PERSONAL ' . Auth::user()->Entrenamiento . ' MODIFICÓ UN PERSONAL',
                'datos_original' => $datos_original,
                'datos_nuevo' => $datos_nuevo,
                'modulo' => 'PERSONAL',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);


            DB::commit();
            return redirect()->route("entrenamientos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function destroy(Entrenamiento $entrenamiento)
    {
        DB::beginTransaction();
        try {
            $datos_original = HistorialAccion::getDetalleRegistro($entrenamiento, "entrenamientos");
            $entrenamiento->status = 0;
            $entrenamiento->save();
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'ELIMINACIÓN',
                'descripcion' => 'EL PERSONAL ' . Auth::user()->Entrenamiento . ' ELIMINÓ UN PERSONAL',
                'datos_original' => $datos_original,
                'modulo' => 'PERSONAL',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->JSON([
                'sw' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
