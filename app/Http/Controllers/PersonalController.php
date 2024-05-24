<?php

namespace App\Http\Controllers;

use App\Models\AsignacionDetalle;
use App\Models\AsignacionPersonal;
use App\Models\HistorialAccion;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PersonalController extends Controller
{
    public $validacion = [
        "nombre" => "required|min:1",
        "estado_civil" => "required",
        "genero" => "required",
        "ci" => "required|min:1",
        "ci_exp" => "required",
        "dir" => "required|min:1",
        "estado" => "required",
    ];

    public $mensajes = [
        "nombre.required" => "Este campo es obligatorio",
        "nombre.min" => "Debes ingresar al menos :min caracteres",
        "estado_civil.required" => "Este campo es obligatorio",
        "genero.required" => "Este campo es obligatorio",
        "paterno.required" => "Este campo es obligatorio",
        "paterno.min" => "Debes ingresar al menos :min caracteres",
        "ci.required" => "Este campo es obligatorio",
        "ci.unique" => "Este C.I. ya fue registrado",
        "ci.min" => "Debes ingresar al menos :min caracteres",
        "ci_exp.required" => "Este campo es obligatorio",
        "dir.required" => "Este campo es obligatorio",
        "dir.min" => "Debes ingresar al menos :min caracteres",
        "cel.required" => "Este campo es obligatorio",
        "cel.min" => "Debes ingresar al menos :min caracteres",
        "estado.required" => "Este campo es obligatorio",
    ];

    public function index()
    {
        return Inertia::render("Personals/Index");
    }

    public function listado()
    {
        $personals = Personal::select("personals.*")->get();
        return response()->JSON([
            "personals" => $personals
        ]);
    }

    public function byTipo(Request $request)
    {
        $personals = Personal::select("personals.*");
        if (isset($request->tipo) && trim($request->tipo) != "") {
            $personals = $personals->where("tipo", $request->tipo);
        }

        if ($request->order && $request->order == "desc") {
            $personals->orderBy("personals.id", "desc");
        }

        $personals = $personals->get();

        return response()->JSON([
            "personals" => $personals
        ]);
    }

    public function paginado(Request $request)
    {
        $search = $request->search;
        $personals = Personal::select("personals.*");

        if (trim($search) != "") {
            $personals->where("Personal", "LIKE", "%$search%");
            $personals->orWhereRaw("CONCAT(nombre,' ', paterno,' ', materno) LIKE ?", ["%$search%"]);
            $personals->orWhere("ci", "LIKE", "%$search%");
        }

        $personals = $personals->paginate($request->itemsPerPage);
        return response()->JSON([
            "personals" => $personals
        ]);
    }

    public function store(Request $request)
    {
        $this->validacion['ci'] = 'required|min:4|numeric|unique:personals,ci';
        if ($request->hasFile('foto')) {
            $this->validacion['foto'] = 'image|mimes:jpeg,jpg,png|max:2048';
        }
        $request->validate($this->validacion, $this->mensajes);
        $request['fecha_registro'] = date('Y-m-d');
        DB::beginTransaction();
        try {
            // crear el Personal
            $nuevo_personal = Personal::create(array_map('mb_strtoupper', $request->except('foto')));
            if ($request->hasFile('foto')) {
                $file = $request->foto;
                $nom_foto = time() . '_' . $nuevo_personal->Personal . '.' . $file->getClientOriginalExtension();
                $nuevo_personal->foto = $nom_foto;
                $file->move(public_path() . '/imgs/personals/', $nom_foto);
            }
            $nuevo_personal->save();

            $datos_original = HistorialAccion::getDetalleRegistro($nuevo_personal, "personals");
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'CREACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->Personal . ' REGISTRO UN PERSONAL',
                'datos_original' => $datos_original,
                'modulo' => 'PERSONAL',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);

            DB::commit();
            return redirect()->route("personals.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(Personal $personal)
    {
    }

    public function update(Personal $personal, Request $request)
    {
        $this->validacion['ci'] = 'required|min:4|numeric|unique:personals,ci,' . $personal->id;
        if ($request->hasFile('foto')) {
            $this->validacion['foto'] = 'image|mimes:jpeg,jpg,png|max:2048';
        }
        $request->validate($this->validacion, $this->mensajes);
        DB::beginTransaction();
        try {
            $datos_original = HistorialAccion::getDetalleRegistro($personal, "personals");
            $personal->update(array_map('mb_strtoupper', $request->except('foto')));
            if ($request->hasFile('foto')) {
                $antiguo = $personal->foto;
                if ($antiguo != 'default.png') {
                    \File::delete(public_path() . '/imgs/personals/' . $antiguo);
                }
                $file = $request->foto;
                $nom_foto = time() . '_' . $personal->id . '.' . $file->getClientOriginalExtension();
                $personal->foto = $nom_foto;
                $file->move(public_path() . '/imgs/personals/', $nom_foto);
            }
            $personal->save();

            $datos_nuevo = HistorialAccion::getDetalleRegistro($personal, "personals");
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'MODIFICACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->Personal . ' MODIFICÓ UN PERSONAL',
                'datos_original' => $datos_original,
                'datos_nuevo' => $datos_nuevo,
                'modulo' => 'PERSONAL',
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s')
            ]);


            DB::commit();
            return redirect()->route("personals.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function destroy(Personal $personal)
    {
        DB::beginTransaction();
        try {
            $existe = AsignacionPersonal::where("personal_id", $personal->id)->get();
            if (count($existe) > 0) {
                throw ValidationException::withMessages([
                    'error' =>  "No es posible eliminar el registro porque esta siendo utilizado en otros registros",
                ]);
            }

            $antiguo = $personal->foto;
            if ($antiguo != 'default.png') {
                \File::delete(public_path() . '/imgs/personals/' . $antiguo);
            }
            $datos_original = HistorialAccion::getDetalleRegistro($personal, "personals");
            $personal->delete();
            HistorialAccion::create([
                'user_id' => Auth::user()->id,
                'accion' => 'ELIMINACIÓN',
                'descripcion' => 'EL USUARIO ' . Auth::user()->Personal . ' ELIMINÓ UN PERSONAL',
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
