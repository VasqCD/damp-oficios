<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $respuesta->numero_oficio_respuesta }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            /* Aplicar márgenes directamente al body */
            margin: 2cm 2.5cm 2cm 2.5cm;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-line {
            font-size: 10pt;
            font-weight: bold;
            margin: 1px 0;
            line-height: 1.2;
        }

        .fecha-lugar {
            text-align: left;
            margin-top: 15px;
            margin-bottom: 15px;
            font-size: 11pt;
        }

        .numero-oficio {
            text-align: left;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .destinatario {
            text-align: left;
            margin-bottom: 20px;
        }

        .destinatario-line {
            font-size: 11pt;
            margin: 1px 0;
        }

        .contenido {
            text-align: justify;
            margin-bottom: 20px;
        }

        .parrafo {
            margin-bottom: 20px;
            text-indent: 0;
        }

        .parrafo-numero {
            font-weight: bold;
            margin-right: 10px;
        }

        .tabla-resultados {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 15px 0;
            font-size: 9pt;
        }

        .tabla-resultados th {
            background-color: #e5e7eb;
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-weight: bold;
        }

        .tabla-resultados td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .despedida {
            margin-top: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .lema {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 30px;
            font-size: 11pt;
            font-weight: bold;
        }

        .firmas {
            margin-top: 40px;
        }

        .firma-bloque {
            margin-bottom: 40px;
        }

        .firma-linea {
            border-top: 1px solid #000;
            width: 220px;
            margin: 0 0 5px 0;
        }

        .firma-nombre {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .firma-cargo {
            font-size: 9pt;
        }

        .lista-personas {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        .lista-personas li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Encabezado Oficial -->
    <div class="header">
        <div class="header-line">República de Honduras</div>
        <div class="header-line">Secretaría de Seguridad</div>
        <div class="header-line">Dirección General Policía Nacional</div>
        <div class="header-line">Dirección Policial Antimaras y Pandillas Contra el Crimen Organizado</div>
        <div class="header-line">San Pedro Sula, Cortés</div>
    </div>

    <!-- Fecha y Lugar -->
    <div class="fecha-lugar">
        San Pedro Sula, {{ \Carbon\Carbon::parse($respuesta->fecha_respuesta)->locale('es')->isoFormat('DD [de] MMMM [del] YYYY') }}.
    </div>

    <!-- Número de Oficio -->
    <div class="numero-oficio">
        OFICIO DIPAMPCO-{{ $respuesta->numero_oficio_respuesta }}.
    </div>

    <!-- Destinatario -->
    <div class="destinatario">
        <div class="destinatario-line"><strong>Señor(a)</strong></div>
        <div class="destinatario-line"><strong>{{ $respuesta->solicitudOficio->agenteSolicitante->cargo->nombre }}</strong></div>
        <div class="destinatario-line"><strong>{{ $respuesta->solicitudOficio->agenteSolicitante->nombres }} {{ $respuesta->solicitudOficio->agenteSolicitante->apellidos }}</strong></div>
        <div class="destinatario-line"><strong>{{ $respuesta->solicitudOficio->institucion->nombre }}</strong></div>
        @if($respuesta->solicitudOficio->unidad)
        <div class="destinatario-line"><strong>{{ $respuesta->solicitudOficio->unidad->nombre }}</strong></div>
        @endif
        <div class="destinatario-line"><strong>Su oficina.</strong></div>
    </div>

    <!-- Contenido -->
    <div class="contenido">
        <!-- Párrafo 1 -->
        <div class="parrafo">
            <span class="parrafo-numero">1.</span>
            Por medio de la presente me dirijo a usted muy cordialmente, deseando éxitos en el desempeño de sus funciones y a la vez dar respuesta a la solicitud
            @if($respuesta->solicitudOficio->numero_oficio_entrante)
                con número de oficio <strong>{{ $respuesta->solicitudOficio->numero_oficio_entrante }}</strong>
            @endif
            en fecha <strong>{{ \Carbon\Carbon::parse($respuesta->solicitudOficio->fecha_recepcion)->locale('es')->isoFormat('DD [de] MMMM [del] YYYY') }}</strong>,
            donde se solicita información de
            @if(count($respuesta->resultadosConsulta) == 1)
                @php $persona = $respuesta->resultadosConsulta->first()->personaSolicitada; @endphp
                la persona <strong>{{ $persona->nombres }} {{ $persona->apellidos }}</strong>, con número de identidad <strong>{{ $persona->dni }}</strong>.
            @else
                las siguientes personas:
            @endif
        </div>

        <!-- Lista de personas si son más de una -->
        @if(count($respuesta->resultadosConsulta) > 1)
        <ul class="lista-personas">
            @foreach($respuesta->resultadosConsulta as $resultado)
            <li>
                <strong>{{ $resultado->personaSolicitada->nombres }} {{ $resultado->personaSolicitada->apellidos }}</strong>,
                DNI: <strong>{{ $resultado->personaSolicitada->dni }}</strong>
            </li>
            @endforeach
        </ul>
        @endif

        <!-- Párrafo 2 - Resultados -->
        <div class="parrafo">
            <span class="parrafo-numero">2.</span>
            Referente a lo solicitado se informa:
        </div>

        <!-- Tabla de Resultados -->
        <table class="tabla-resultados">
            <thead>
                <tr>
                    <th style="width: 35%;">Nombre Completo</th>
                    <th style="width: 20%;">DNI</th>
                    <th style="width: 15%;">Estado</th>
                    <th style="width: 30%;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($respuesta->resultadosConsulta as $resultado)
                <tr>
                    <td>{{ $resultado->personaSolicitada->nombres }} {{ $resultado->personaSolicitada->apellidos }}</td>
                    <td>{{ $resultado->personaSolicitada->dni }}</td>
                    <td>
                        @if($resultado->encontrada)
                            <strong>REGISTRADO</strong>
                        @else
                            NO REGISTRADO
                        @endif
                    </td>
                    <td>
                        @if($resultado->encontrada && $resultado->detalles)
                            @if(isset($resultado->detalles['grupo_delictivo']))
                                Grupo: {{ $resultado->detalles['grupo_delictivo'] }}.
                            @endif
                            @if(isset($resultado->detalles['estructura_criminal']))
                                Estructura: {{ $resultado->detalles['estructura_criminal'] }}.
                            @endif
                        @else
                            Sin antecedentes en base de datos.
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Observaciones adicionales si existen -->
        @if($respuesta->contenido_respuesta)
        <div class="parrafo">
            {{ $respuesta->contenido_respuesta }}
        </div>
        @endif

        <!-- Párrafo final -->
        <div class="parrafo">
            <span class="parrafo-numero">3.</span>
            Me suscribo de usted muy atentamente.
        </div>
    </div>

    <!-- Lema -->
    <div class="lema">
        <div>Dios&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Patria&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Servicio</div>
    </div>

    <!-- Firmas -->
    <div class="firmas">
        <!-- Firma del Analista -->
        <div class="firma-bloque">
            <div class="firma-linea"></div>
            <div class="firma-nombre">{{ $respuesta->analista->name }}</div>
            <div class="firma-cargo">Analista</div>
            <div class="firma-cargo">DIPAMPCO</div>
        </div>

        <!-- Firma del Jefe Regional -->
        <div class="firma-bloque">
            <div class="firma-linea"></div>
            <div class="firma-nombre">{{ $respuesta->jefeRegional->name }}</div>
            <div class="firma-cargo">Jefe Regional</div>
            <div class="firma-cargo">DIPAMPCO</div>
        </div>
    </div>
</body>
</html>
