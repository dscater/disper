<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\AsignacionDetalle;
use App\Models\Diagnostico;
use App\Models\Lugar;
use App\Models\Paciente;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use PDF;

class ReporteController extends Controller
{
    public function usuarios()
    {
        return Inertia::render("Reportes/Usuarios");
    }

    public function r_usuarios(Request $request)
    {
        $tipo =  $request->tipo;
        $usuarios = User::where('id', '!=', 1)->orderBy("paterno", "ASC")->get();

        if ($tipo != 'TODOS') {
            $request->validate([
                'tipo' => 'required',
            ]);
            $usuarios = User::where('id', '!=', 1)->where('tipo', $request->tipo)->orderBy("paterno", "ASC")->get();
        }

        $pdf = PDF::loadView('reportes.usuarios', compact('usuarios'))->setPaper('legal', 'landscape');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('usuarios.pdf');
    }

    public function personals()
    {
        return Inertia::render("Reportes/Personals");
    }

    public function r_personals(Request $request)
    {
        $estado =  $request->estado;
        $personals = Personal::orderBy("paterno", "ASC")->get();

        if ($estado != 'TODOS') {
            $request->validate([
                'estado' => 'required',
            ]);
            $personals = Personal::where('estado', $request->estado)->orderBy("paterno", "ASC")->get();
        }

        $pdf = PDF::loadView('reportes.personals', compact('personals'))->setPaper('legal', 'landscape');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('personals.pdf');
    }

    public function asignacion_personal()
    {
        return Inertia::render("Reportes/AsignacionPersonal");
    }

    public function r_asignacion_personal(Request $request)
    {
        $asignacion_id =  $request->asignacion_id;
        $personal_id =  $request->personal_id;
        $lugar =  $request->lugar;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;

        $asignacions = Asignacion::where("status", 1);
        if (isset($asignacion_id) && $asignacion_id != 'todos') {
            $asignacions->where("id", $asignacion_id);
        }

        if (isset($fecha_ini) && isset($fecha_fin) && $fecha_ini && $fecha_fin) {
            $asignacions->whereBetween("fecha_registro", [$fecha_ini, $fecha_fin]);
        }
        $asignacions = $asignacions->get();
        $lugares = Lugar::distinct()->pluck("nombre");
        if (isset($lugar) && $lugar != 'TODOS') {
            $lugares = Lugar::where("nombre", $lugar)->distinct()->pluck("nombre");
        }

        $array_asignacions = [];
        foreach ($asignacions as $asignacion) {
            $array_asignacions[$asignacion->id] = [
                "asignacion_detalles" => [],
                "rowspan_lugars" => [],
            ];
            foreach ($lugares as $lugar) {
                $id_lugars = Lugar::where("nombre", $lugar)->pluck("id");
                $id_lugars = $id_lugars->toArray();
                $rowspan = 1;
                $a_asignacion_detalles = [];
                foreach ($id_lugars as $value) {
                    $asignacion_detalles = AsignacionDetalle::select("asignacion_detalles.*");
                    $asignacion_detalles->where("asignacion_id", $asignacion->id);
                    $asignacion_detalles = $asignacion_detalles->where("lugar_id", $value)->get();
                    $rowspan = (int)$rowspan + count($asignacion_detalles);
                    $a_asignacion_detalles[] = $asignacion_detalles;
                }
                $array_asignacions[$asignacion->id]["rowspan_lugars"][] = $rowspan > 2 ? $rowspan - 1 : 1;
                $array_asignacions[$asignacion->id]["asignacion_detalles"][] = $a_asignacion_detalles;
            }
        }
        $pdf = PDF::loadView('reportes.asignacion_personal', compact('lugares', "asignacions", "array_asignacions", "personal_id"))->setPaper('letter', 'portrait');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('asignacion_personal.pdf');
    }

    public function personal_zonas()
    {
        return Inertia::render("Reportes/PersonalZonas");
    }

    public function r_personal_zonas(Request $request)
    {
        $asignacion_id =  $request->asignacion_id;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $lugares = Lugar::distinct()->pluck("nombre");

        $data = [];
        foreach ($lugares as $value) {
            $id_lugars = Lugar::where("nombre", $value)->pluck("id");
            $id_lugars = $id_lugars->toArray();
            $asignaciones = AsignacionDetalle::select("asignacion_detalles");
            $asignaciones->whereIn("lugar_id", $id_lugars);
            if ($asignacion_id != 'todos') {
                $asignaciones->where("asignacion_id", $asignacion_id);
            }
            if ($fecha_ini && $fecha_fin) {
                $asignaciones->whereBetween("fecha", [$fecha_ini, $fecha_fin]);
            }
            $asignaciones = $asignaciones->sum("total_personal");
            $data[] = [$value, (float)$asignaciones];
        }

        return response()->JSON([
            "data" => $data
        ]);
    }
}
