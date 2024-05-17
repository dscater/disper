<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
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
    public function pacientes()
    {
        return Inertia::render("Reportes/Pacientes");
    }

    public function r_pacientes(Request $request)
    {
        $filtro =  $request->filtro;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $tipo =  $request->tipo;
        $pacientes = Paciente::select("pacientes.*")
            ->leftjoin("diagnosticos", "diagnosticos.paciente_id", "=", "pacientes.id")
            ->get();

        if ($filtro != 'todos') {
            if ($filtro == 'fechas') {
                $pacientes = Paciente::select("pacientes.*")
                    ->leftJoin("diagnosticos", "diagnosticos.paciente_id", "=", "pacientes.id")
                    ->whereBetween("pacientes.fecha_registro", [$fecha_ini, $fecha_fin])
                    ->get();
            }
            if ($filtro == 'tipo' && $tipo != 'todos') {
                $pacientes = Paciente::select("pacientes.*")
                    ->join("diagnosticos", "diagnosticos.paciente_id", "=", "pacientes.id")
                    ->where("diagnosticos.diagnostico", $tipo)
                    ->get();
            }
        }

        $pdf = PDF::loadView('reportes.pacientes', compact('pacientes'))->setPaper('legal', 'landscape');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('pacientes.pdf');
    }

    public function historial_pacientes()
    {
        return Inertia::render("Reportes/HistorialPacientes");
    }

    public function r_historial_pacientes(Request $request)
    {
        $tipo =  $request->tipo;
        $pacientes = Paciente::all();

        if ($tipo != 'TODOS') {
            $request->validate([
                'tipo' => 'required',
            ]);
            $pacientes = Paciente::where("id", $tipo)->get();
        }

        $pdf = PDF::loadView('reportes.historial_pacientes', compact('pacientes'))->setPaper('letter', 'portrait');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('historial_pacientes.pdf');
    }

    public function diagnosticos()
    {
        return Inertia::render("Reportes/Diagnosticos");
    }

    public function r_diagnosticos(Request $request)
    {
        $filtro = $request->filtro;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        if ($filtro == 'todos') {
            $nc = count(Diagnostico::where("diagnostico", "NO TIENE CANCER")->get());
            $cmb = count(Diagnostico::where("diagnostico", "CANCER DE MAMA BENIGNO")->get());
            $cmm = count(Diagnostico::where("diagnostico", "CANCER DE MAMA MALIGNO")->get());
        } else {
            $nc = count(Diagnostico::where("diagnostico", "NO TIENE CANCER")->whereBetween("fecha_diagnostico", [$fecha_ini, $fecha_fin])->get());
            $cmb = count(Diagnostico::where("diagnostico", "CANCER DE MAMA BENIGNO")->whereBetween("fecha_diagnostico", [$fecha_ini, $fecha_fin])->get());
            $cmm = count(Diagnostico::where("diagnostico", "CANCER DE MAMA MALIGNO")->whereBetween("fecha_diagnostico", [$fecha_ini, $fecha_fin])->get());
        }



        $data = [
            [
                "name" => "NO TIENE CANCER",
                "y" => (int)$nc,
            ],
            [
                "name" => "CANCER DE MAMA BENIGNO",
                "y" => (int)$cmb,
            ],
            [
                "name" => "CANCER DE MAMA MALIGNO",
                "y" => (int)$cmm,
            ],
        ];


        return response()->JSON([
            "data" => $data
        ]);
    }
}
