<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Asignacion de Personal</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 1cm;
            margin-bottom: 1cm;
            margin-left: 2cm;
            margin-right: 1cm;
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
            font-size: 8.5pt;
        }

        table tbody tr td {
            font-size: 7.5pt;
        }


        .encabezado {
            width: 100%;
        }

        .logo img {
            position: absolute;
            height: 100px;
            top: -20px;
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
            background: #63c3df;
            color: black;
        }

        .txt_rojo {}

        .img_celda img {
            width: 45px;
        }

        .nueva_pagina {
            page-break-after: always;
        }

        .bold {
            font-weight: bold;
        }

        .text-md {
            font-size: 0.8em;
        }

        p.subtitulo {
            font-size: 10pt;
            width: 100%;
            text-align: center;
        }

        .listado {
            padding: 0px;
            padding-left: 10px;
        }

        td.red {
            background: rgb(255, 226, 226);
        }

        .bl {
            border-left: solid 0.5px black;
        }

        .br {
            border-right: solid 0.5px black;
        }

        .bt {
            border-top: solid 0.5px black;
        }

        .bb {
            border-bottom: solid 0.5px black;
        }

        .col_per {
            color: rgb(0, 170, 0);
        }
    </style>
</head>

<body>
    @php
        $contador = 0;
    @endphp
    @foreach ($asignacions as $asignacion)
        @php
            $a_personal_id = [];
        @endphp
        @inject('configuracion', 'App\Models\Configuracion')
        <div class="encabezado">
            <div class="logo">
                <img src="{{ $configuracion->first()->url_logo }}">
            </div>
            <h2 class="titulo">
                {{ $configuracion->first()->razon_social }}
            </h2>
            <h4 class="texto">ASIGNACIÓN DE PERSONAL</h4>
            <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4>
        </div>
        <table class="info">
            <tbody>
                <tr>
                    <td class="bold text-md" width="24%">Código de asignación: </td>
                    <td class="text-md">{{ $asignacion->cod }}</td>
                    <td class="bold text-md" width="10%">Fecha: </td>
                    <td class="text-md">{{ $asignacion->fecha_registro_t }}</td>
                </tr>
            </tbody>
        </table>
        <table>
            <thead>
                <tr>
                    <th class="br bt bl" width="4%">N°</th>
                    <th class="bt bl">Zona</th>
                    <th class="bt bl">Lat.</th>
                    <th class="bt bl">Long.</th>
                    <th class="bt bl">Personal</th>
                    <th class="bt bl" width="6%">Requerido</th>
                    <th class="bt bl" width="6%">Asignado</th>
                    <th class="bt br bl" width="6%">Restante</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cont = 1;
                @endphp
                @foreach ($lugares as $key_lugar => $lugar)
                    @foreach ($array_asignacions[$asignacion->id]['asignacion_detalles'][$key_lugar] as $key_items => $items)
                        @foreach ($items as $key_i => $item)
                            @if ($key_items == 0)
                                <tr>
                                    <td
                                        class="bl br bt {{ $key_items == count($array_asignacions[$asignacion->id]['asignacion_detalles'][$key_lugar]) - 1 ? 'bb' : '' }}">
                                        {{ $cont++ }} </td>
                                    <td
                                        class="bl br bt {{ $key_items == count($array_asignacions[$asignacion->id]['asignacion_detalles'][$key_lugar]) - 1 ? 'bb' : '' }}">
                                        {{ $lugar }}</td>
                                    <td class="bl br bt {{ $item->restante > 0 ? 'red' : '' }}">{{ $item->lat }}
                                    </td>
                                    <td class="bl br bt {{ $item->restante > 0 ? 'red' : '' }}">{{ $item->lng }}
                                    </td>
                                    <td class="bl br bt {{ $item->restante > 0 ? 'red' : '' }}">
                                        <ul class="listado">
                                            @foreach ($item->asignacion_personals as $ap)
                                                @php
                                                    if (!in_array($ap->personal_id, $a_personal_id)) {
                                                        $a_personal_id[] = $ap->personal_id;
                                                    }
                                                @endphp
                                                <li
                                                    class="{{ $personal_id != 'todos' && $personal_id == $ap->personal_id ? 'bold col_per' : '' }}">
                                                    {{ $ap->personal->full_name }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="bl br bt centreado {{ $item->restante > 0 ? 'red' : '' }}">
                                        {{ $item->requerido }}</td>
                                    <td class="bl br bt centreado {{ $item->restante > 0 ? 'red' : '' }}">
                                        {{ $item->total_personal }}</td>
                                    <td class="bl br bt centreado {{ $item->restante > 0 ? 'red' : '' }}">
                                        {{ $item->restante }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td
                                        class="bl br {{ $key_items == count($array_asignacions[$asignacion->id]['asignacion_detalles'][$key_lugar]) - 1 ? 'bb' : '' }}">
                                    </td>
                                    <td
                                        class="bl br {{ $key_items == count($array_asignacions[$asignacion->id]['asignacion_detalles'][$key_lugar]) - 1 ? 'bb' : '' }}">
                                    </td>
                                    <td class="bb br bt {{ $item->restante > 0 ? 'red' : '' }}">{{ $item->lat }}
                                    </td>
                                    <td class="bb br bt {{ $item->restante > 0 ? 'red' : '' }}">{{ $item->lng }}
                                    </td>
                                    <td class="bb br bt {{ $item->restante > 0 ? 'red' : '' }}">
                                        <ul class="listado">
                                            @foreach ($item->asignacion_personals as $ap)
                                                @php
                                                    if (!in_array($ap->personal_id, $a_personal_id)) {
                                                        $a_personal_id[] = $ap->personal_id;
                                                    }
                                                @endphp
                                                <li
                                                    class="{{ $personal_id != 'todos' && $personal_id == $ap->personal_id ? 'bold col_per' : '' }}">
                                                    {{ $ap->personal->full_name }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="bb br bt centreado {{ $item->restante > 0 ? 'red' : '' }}">
                                        {{ $item->requerido }}</td>
                                    <td class="bb br bt centreado {{ $item->restante > 0 ? 'red' : '' }}">
                                        {{ $item->total_personal }}</td>
                                    <td class="bb br bt centreado {{ $item->restante > 0 ? 'red' : '' }}">
                                        {{ $item->restante }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>

        @php
            $personal_no_asignado = App\Models\Personal::whereNotIn('id', $a_personal_id)->get();
        @endphp
        <p class="subtitulo bold">Personal no asignado</p>
        <table border="1">
            <thead>
                <tr>
                    <th width="4%">N°</th>
                    <th>Nombre</th>
                    <th>C.I.</th>
                    <th>Celular</th>
                </tr>
            </thead>
            <tbody>
                @if (count($personal_no_asignado) > 0)
                    @foreach ($personal_no_asignado as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->full_name }}</td>
                            <td>{{ $item->full_ci }}</td>
                            <td>{{ $item->cel }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="centreado">Sin registros</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @php
            $contador++;
        @endphp
        @if ($contador < count($asignacions))
            <div class="nueva_pagina"></div>
        @endif
    @endforeach
</body>

</html>
