<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>historialpacientes</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 0.5cm;
            margin-bottom: 0.5cm;
            margin-left: 1.5cm;
            margin-right: 0.5cm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
            page-break-before: avoid;
        }

        table thead tr th,
        tbody tr td {
            padding: 3px;
            word-wrap: break-word;
        }

        table thead tr th {
            font-size: 8pt;
        }

        table tbody tr td {
            font-size: 7pt;
        }


        .encabezado {
            width: 100%;
        }

        .logo img {
            position: absolute;
            height: 100px;
            top: 0px;
            left: 0px;
        }

        h2.titulo {
            width: 450px;
            margin: auto;
            margin-top: 0PX;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14pt;
        }

        .texto {
            width: 250px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.1em;
        }

        .fecha {
            width: 250px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: normal;
            font-size: 0.85em;
        }

        .total {
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
        }

        table {
            width: 100%;
        }

        table thead {
            background: rgb(236, 236, 236)
        }

        tr {
            page-break-inside: avoid !important;
        }

        .centreado {
            padding-left: 0px;
            text-align: center;
        }

        .datos {
            margin-left: 15px;
            border-top: solid 1px;
            border-collapse: collapse;
            width: 250px;
        }

        .txt {
            font-weight: bold;
            text-align: right;
            padding-right: 5px;
        }

        .txt_center {
            font-weight: bold;
            text-align: center;
        }

        .cumplimiento {
            position: absolute;
            width: 150px;
            right: 0px;
            top: 86px;
        }

        .b_top {
            border-top: solid 1px black;
        }

        .gray {
            background: rgb(202, 202, 202);
        }

        .bg-principal {
            background: #019199;
            color: white;
        }

        .txt_rojo {}

        .img_celda img {
            width: 45px;
        }

        .nueva_pagina {
            page-break-after: always;
        }

        .subtitulo {
            font-size: 0.9em;
        }

        .txtinfo {
            font-weight: bold;
        }

        .border-bot {
            border-bottom: solid 0.7px black;
        }

        .table-info {
            margin-top: 4px;
            border-collapse: separate;
            border-spacing: 15px 0px;
        }

        .bordeado {
            border: solid 0.7px black;
            height: 14px;
        }

        .bold {
            font-weight: bold;
        }

        .txt-md {
            font-size: 0.8em;
        }

        .foto {
            width: 14%;
            padding: 0px;
            padding-bottom: 5px;
        }

        .foto img {
            width: 100%;
        }
    </style>
</head>

<body>
    @inject('institucion', 'App\Models\Institucion')
    @php
        $contador = 0;
    @endphp
    @foreach ($pacientes as $paciente)
        <div class="encabezado">
            <div class="logo">
                <img src="{{ $institucion->first()->url_logo }}">
            </div>
            <h2 class="titulo">
                {{ $institucion->first()->razon_social }}
            </h2>
            <h4 class="texto">HISTORIAL DE PACIENTES</h4>
            <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4>
        </div>

        <h2 class="subtitulo centreado">DATOS DEL PACIENTE</h2>
        <table class="table-info">
            <tbody>
                <tr>
                    <td class="centreado border-bot">{{ $paciente->paterno }}</td>
                    <td class="centreado border-bot">{{ $paciente->materno }}</td>
                    <td class="centreado border-bot">{{ $paciente->nombre }}</td>
                    <td rowspan="4" class="foto">
                        @if ($paciente->url_foto)
                            <img src="{{ $paciente->url_foto }}" alt="">
                        @else
                            <img src="{{ asset('imgs/users/user_default.png') }}" alt="">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="centreado txtinfo">Ap. Paterno</td>
                    <td class="centreado txtinfo">Ap. Materno</td>
                    <td class="centreado txtinfo">Nombres</td>
                </tr>
                <tr>
                    <td class="centreado border-bot">{{ $paciente->full_ci }}</td>
                    <td class="centreado border-bot">{{ $paciente->fecha_nac }}</td>
                    <td class="centreado border-bot">{{ $paciente->sexo }}</td>
                </tr>
                <tr>
                    <td class="centreado txtinfo">Cédula de Identidad</td>
                    <td class="centreado txtinfo">Fecha de Nacimiento</td>
                    <td class="centreado txtinfo">Sexo</td>
                </tr>
                <tr>
                    <td class="centreado border-bot">{{ $paciente->estado_civil }}</td>
                    <td class="centreado border-bot">{{ $paciente->ocupacion }}</td>
                    <td class="centreado border-bot">{{ $paciente->correo }}</td>
                    <td class="centreado border-bot">{{ $paciente->cel }}</td>
                </tr>
                <tr>
                    <td class="centreado txtinfo">Estado Civil</td>
                    <td class="centreado txtinfo">Ocupación</td>
                    <td class="centreado txtinfo">Correo</td>
                    <td class="centreado txtinfo">Celular</td>
                </tr>
                <tr>
                    <td class="centreado border-bot">{{ $paciente->cel_familiar }}</td>
                    <td class="centreado border-bot">{{ $paciente->dir_trabajo }}</td>
                    <td class="centreado border-bot">{{ $paciente->dir_domicilio }}</td>
                    <td class="centreado border-bot">{{ $paciente->fecha_registro_t }}</td>
                </tr>
                <tr>
                    <td class="centreado txtinfo">Celular de Familiar</td>
                    <td class="centreado txtinfo">Dir. Trabajo</td>
                    <td class="centreado txtinfo">Dir. Domicilio</td>
                    <td class="centreado txtinfo">Fecha de Registro</td>
                </tr>
            </tbody>
        </table>
        <h2 class="subtitulo centreado">HISTORIAL</h2>
        @if ($paciente->historial_paciente)
            @php
                $historial_paciente = $paciente->historial_paciente;
            @endphp
            <table border="1" style="margin-top:0px;">
                <tbody>
                    <tr>
                        <td class="bold">Fecha de primera consulta:</td>
                        <td>{{ $historial_paciente->fecha_pc }}</td>
                        <td class="bold">Meses en demora para consultar:</td>
                        <td>{{ $historial_paciente->meses_dpc }}</td>
                        <td class="bold">Mamografía durante el año anterior:</td>
                        <td>{{ $historial_paciente->mamografia_aa }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Biopsias Mamarias Previas:</td>
                        <td>{{ $historial_paciente->biopsias_mp }}</td>
                        <td class="bold">Alguna Fue Hiperplasia Atípica:</td>
                        <td>{{ $historial_paciente->hiperplasia_a }}</td>
                        <td class="bold">Cáncer de mama Previo:</td>
                        <td>{{ $historial_paciente->cancer_mp }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Lado del Tumor Anterior:</td>
                        <td>{{ $historial_paciente->lado_ta }}</td>
                        <td class="bold">Ha Tenido Cáncer en Algún Otro Lado del Cuerpo:</td>
                        <td>{{ $historial_paciente->cancer_olc }}</td>
                        <td class="bold">Cuántos Parientes en Primer Grado de Cáncer de Mama:</td>
                        <td>{{ $historial_paciente->parientes_pgcm }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Otros Parientes con Cáncer de Mama:</td>
                        <td>{{ $historial_paciente->otros_pccm }}</td>
                        <td class="bold">Parientes con Cáncer de Ovario:</td>
                        <td>{{ $historial_paciente->parientes_cco }}</td>
                        <td class="bold">Parientes con Cáncer de Colon:</td>
                        <td>{{ $historial_paciente->parientes_ccc }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Parientes con Cáncer de Endometrio:</td>
                        <td>{{ $historial_paciente->parientes_cce }}</td>
                        <td class="bold">Parientes con Otros Tipos de Cáncer:</td>
                        <td>{{ $historial_paciente->parientes_cotc }}</td>
                        <td class="bold">Fecha de Registro:</td>
                        <td>{{ $historial_paciente->fecha_registro_t }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <p class="txt-md centreado">NO SE ENCONTRÓ NINGUN HISTORIAL</p>
        @endif
        @php
            $contador++;
        @endphp
        @if ($contador < count($pacientes))
            <div class="nueva_pagina"></div>
        @endif
    @endforeach
</body>

</html>
