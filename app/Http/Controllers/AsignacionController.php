<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\DetalleEntrenamiento;
use App\Models\HistorialAccion;
use App\Models\Lugar;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AsignacionController extends Controller
{
    public $validacion = [
        "archivo" => "required|mimes:csv,xlsx,xls",
    ];

    public $mensajes = [];

    public function index()
    {
        return Inertia::render("Asignacions/Index");
    }

    public function listado()
    {
        $asignacions = Asignacion::select("asignacions.*")->where("status", 1)->orderBy("id", "desc")->get();
        return response()->JSON([
            "asignacions" => $asignacions
        ]);
    }
    public function paginado(Request $request)
    {
        $search = $request->search;
        $asignacions = Asignacion::select("asignacions.*")->where("status", 1);

        if (trim($search) != "") {
            $asignacions->where("Asignacion", "LIKE", "%$search%");
            $asignacions->orWhereRaw("CONCAT(nombre,' ', paterno,' ', materno) LIKE ?", ["%$search%"]);
            $asignacions->orWhere("ci", "LIKE", "%$search%");
        }

        $asignacions = $asignacions->paginate($request->itemsPerPage);
        return response()->JSON([
            "asignacions" => $asignacions
        ]);
    }

    public function create()
    {
        return Inertia::render("Asignacions/Create");
    }

    public function store(Request $request)
    {
        set_time_limit(0);
        // $request->validate($this->validacion, $this->mensajes);
        DB::beginTransaction();
        try {

            $a_cod = Asignacion::getNuevoCodigo();
            // crear la Asignacion
            $fecha_actual = date("Y-m-d");
            $nueva_asignacion = Asignacion::create([
                "cod" => $a_cod[0],
                "nro" => $a_cod[1],
                "fecha_registro" => $fecha_actual,
            ]);

            // buscar segun fecha y hora
            $hora_actual = date("H:i");

            $lugars = Lugar::all();

            $mes_dia = date("m-d", strtotime($fecha_actual));
            $hora = date("H", strtotime($hora_actual));

            $ids_personal = Personal::where("estado", "ACTIVO")->pluck("id");
            $ids_personal = $ids_personal->toArray();
            foreach ($lugars as $item) {
                // buscar en los entrenamientos segun fecha y hora
                $entrenamientos = DetalleEntrenamiento::where("lugar_id", $item->id)
                    ->where("fecha", "LIKE", "%$mes_dia")
                    ->where("hora", "LIKE", "$hora%")
                    ->get();
                // verificar nro. espacios usados y personal necesario
                $capacidad = $item->capacidad;
                $total_requerido = 1;
                if ($capacidad <= 5) {
                    $total_requerido = 1;
                } elseif ($capacidad <= 10) {
                    $total_requerido = 2;
                } elseif ($capacidad <= 15) {
                    $total_requerido = 3;
                } elseif ($capacidad <= 20) {
                    $total_requerido = 4;
                } elseif ($capacidad <= 25) {
                    $total_requerido = 5;
                }

                // aumentar cantidad segun númro de registros en fecha y hora
                if (count($entrenamientos) >= 10 && count($entrenamientos) < 20) {
                    $total_requerido++;
                } elseif (count($entrenamientos) >= 20 && count($entrenamientos) < 35) {
                    $total_requerido++;
                } elseif (count($entrenamientos) > 35) {
                    $total_requerido = $total_requerido + 2;
                }

                // obtener los IDS del personal
                $ids_personal_asignado = self::getIdsPersonal($ids_personal, $total_requerido);
                $restante = $total_requerido - (int)count($ids_personal_asignado);
                $nueva_asignacion_detalle = $nueva_asignacion->asignacion_detalles()->create([
                    "lugar_id" => $item->id,
                    "lat" => $item->lat,
                    "lng" => $item->lng,
                    "fecha" => $fecha_actual,
                    "hora" => $hora_actual,
                    "requerido" => $total_requerido,
                    "total_personal" => count($ids_personal_asignado),
                    "restante" => $restante,
                    "fecha_registro" => $fecha_actual
                ]);

                if (count($ids_personal_asignado) > 0) {
                    foreach ($ids_personal_asignado as $value) {
                        $nueva_asignacion_detalle->asignacion_personals()->create([
                            "personal_id" => $value,
                        ]);
                    }
                }
                usleep(500000);
            }

            $datos_original = HistorialAccion::getDetalleRegistro($nueva_asignacion, "asignacions");
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'CREACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->Asignacion . ' REGISTRO UNA ASIGNACIÓN',
                'datos_original' => $datos_original,
                'modulo' => 'ASIGNACIÓN',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'La asignación se registro correctamente',
                "asignacion" => $nueva_asignacion
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public static function getIdsPersonal(&$array, $cantidad)
    {
        $valoresAleatorios = [];
        if (!empty($array)) {
            // Asegurarse de no pedir más elementos de los que hay en el array
            if ($cantidad > count($array)) {
                $cantidad = count($array);
            }

            // Obtener claves aleatorias
            $clavesAleatorias = array_rand($array, $cantidad);

            // Convertir a array si solo se selecciona un elemento (array_rand devuelve un valor escalar en ese caso)
            if (!is_array($clavesAleatorias)) {
                $clavesAleatorias = [$clavesAleatorias];
            }

            // Recoger los valores correspondientes a las claves aleatorias y eliminarlos del array original
            foreach ($clavesAleatorias as $clave) {
                $valoresAleatorios[] = $array[$clave];
                unset($array[$clave]);  // Eliminar el elemento del array original
            }

            // Reindexar el array original para mantener el orden de los índices
            $array = array_values($array);
        }

        return $valoresAleatorios;
    }

    public function show(Asignacion $asignacion)
    {
        $asignacion = $asignacion->load(["asignacion_detalles.asignacion_personals.personal", "asignacion_detalles.lugar"]);
        return Inertia::render("Asignacions/Show", compact("asignacion"));
    }
    public function update(Asignacion $asignacion, Request $request)
    {
        $this->validacion['ci'] = 'required|min:4|numeric|unique:asignacions,ci,' . $asignacion->id;
        if ($request->hasFile('foto')) {
            $this->validacion['foto'] = 'image|mimes:jpeg,jpg,png|max:2048';
        }
        $request->validate($this->validacion, $this->mensajes);
        DB::beginTransaction();
        try {
            $datos_original = HistorialAccion::getDetalleRegistro($asignacion, "asignacions");
            $asignacion->update(array_map('mb_strtoupper', $request->all()));
            if ($request->hasFile('foto')) {
                $antiguo = $asignacion->foto;
                if ($antiguo != 'default.png') {
                    \File::delete(public_path() . '/imgs/asignacions/' . $antiguo);
                }
                $file = $request->foto;
                $nom_foto = time() . '_' . $asignacion->id . '.' . $file->getClientOriginalExtension();
                $asignacion->foto = $nom_foto;
                $file->move(public_path() . '/imgs/asignacions/', $nom_foto);
            }
            $asignacion->save();

            $datos_nuevo = HistorialAccion::getDetalleRegistro($asignacion, "asignacions");
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'MODIFICACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->Asignacion . ' MODIFICÓ UNA ASIGNACIÓN',
                'datos_original' => $datos_original,
                'datos_nuevo' => $datos_nuevo,
                'modulo' => 'ASIGNACIÓN',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);


            DB::commit();
            return redirect()->route("asignacions.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function destroy(Asignacion $asignacion)
    {
        DB::beginTransaction();
        try {
            $datos_original = HistorialAccion::getDetalleRegistro($asignacion, "asignacions");

            if ($asignacion->asignacion_detalles()->count() > 0) {
                $asignacion->status = 0;
                $asignacion->save();
            } else {
                $asignacion->delete();
            }

            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'ELIMINACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->Asignacion . ' ELIMINÓ UNA ASIGNACIÓN',
                'datos_original' => $datos_original,
                'modulo' => 'ASIGNACIÓN',
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
