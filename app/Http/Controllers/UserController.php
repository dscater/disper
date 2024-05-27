<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Doctor;
use App\Models\Entrenamiento;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{


    public static $permisos = [
        "ADMINISTRADOR" => [
            "usuarios.index",
            "usuarios.create",
            "usuarios.edit",
            "usuarios.destroy",

            "configuracions.index",
            "configuracions.create",
            "configuracions.edit",
            "configuracions.destroy",

            "personals.index",
            "personals.create",
            "personals.edit",
            "personals.destroy",

            "entrenamientos.index",
            "entrenamientos.create",
            "entrenamientos.edit",
            "entrenamientos.destroy",

            "asignacions.index",
            "asignacions.create",
            "asignacions.edit",
            "asignacions.destroy",

            "reportes.usuarios",
            "reportes.personals",
            "reportes.asignacion_personal",
            "reportes.personal_zonas",

        ],
        "SUPERVISOR DE PERSONAL" => [
            "personals.index",
            "personals.create",
            "personals.edit",
            "personals.destroy",

            "entrenamientos.index",
            "entrenamientos.create",
            "entrenamientos.edit",
            "entrenamientos.destroy",

            "asignacions.index",
            "asignacions.create",
            "asignacions.edit",
            "asignacions.destroy",
            
            "reportes.personals",
            "reportes.asignacion_personal",
            "reportes.personal_zonas",
        ],
    ];

    public static function getPermisosUser()
    {
        $array_permisos = self::$permisos;
        if ($array_permisos[Auth::user()->tipo]) {
            return $array_permisos[Auth::user()->tipo];
        }
        return [];
    }


    public static function verificaPermiso($permiso)
    {
        if (in_array($permiso, self::$permisos[Auth::user()->tipo])) {
            return true;
        }
        return false;
    }

    public function permisos(Request $request)
    {
        return response()->JSON([
            "permisos" => $this->permisos[Auth::user()->tipo]
        ]);
    }

    public function getUser()
    {
        return response()->JSON([
            "user" => Auth::user()
        ]);
    }

    public static function getInfoBoxUser()
    {
        $tipo = Auth::user()->tipo;
        $array_infos = [];
        if (in_array('usuarios.index', self::$permisos[$tipo])) {
            $array_infos[] = [
                'label' => 'Usuarios',
                'cantidad' => count(User::where('id', '!=', 1)->get()),
                'color' => 'bg-blue-darken-2',
                'icon' => asset("imgs/icon_users.png"),
                "url" => "usuarios.index"
            ];
        }


        if (in_array('personals.index', self::$permisos[$tipo])) {
            $array_infos[] = [
                'label' => 'Personal',
                'cantidad' => Personal::count(),
                'color' => 'bg-orange',
                'icon' => asset("imgs/businessman.png"),
                "url" => "usuarios.index"
            ];
        }
        if (in_array('entrenamientos.index', self::$permisos[$tipo])) {
            $array_infos[] = [
                'label' => 'Entrenamientos',
                'cantidad' => Entrenamiento::where("status", 1)->count(),
                'color' => 'bg-cyan-accent-2',
                'icon' => asset("imgs/checklist.png"),
                "url" => "usuarios.index"
            ];
        }
        if (in_array('asignacions.index', self::$permisos[$tipo])) {
            $array_infos[] = [
                'label' => 'Asignaciones de Personal',
                'cantidad' => Asignacion::where("status", 1)->count(),
                'color' => 'bg-indigo',
                'icon' => asset("imgs/icon_inscripcion.png"),
                "url" => "usuarios.index"
            ];
        }

        return $array_infos;
    }
}
