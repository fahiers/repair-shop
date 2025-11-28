<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recibo de Ingreso - OT {{ $orden->numero_orden }}</title>
    <style>
        @page {
            margin: 6mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
            line-height: 1.1;
            color: #000;
        }

        .receipt-section {
            /* Reducimos altura para asegurar que quepan dos */
            height: 44%; 
            position: relative;
            margin-bottom: 5px;
            padding-bottom: 0;
        }

        .cut-line {
            border-bottom: 1px dashed #999;
            width: 100%;
            margin: 8px 0;
            position: relative;
            height: 1px;
        }
        .cut-line::after {
            content: "✂";
            position: absolute;
            right: 10px;
            top: -9px;
            background: #fff;
            padding: 0 5px;
            font-size: 12px;
            color: #999;
        }

        /* Estilos generales */
        .header-section { display: table; width: 100%; margin-bottom: 2px; }
        .header-left { display: table-cell; width: 60%; vertical-align: top; }
        .header-right { display: table-cell; width: 40%; text-align: right; vertical-align: top; }
        
        .header-empresa { font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .header-empresa-info { font-size: 9px; margin-top: 1px; line-height: 1.2; }
        
        .title { font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .orden-number { font-size: 12px; font-weight: bold; }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; border: 1px solid #000; }
        .header-table td { padding: 3px; vertical-align: top; border: 1px solid #000; }
        
        .section-title { font-weight: bold; font-size: 9px; text-transform: uppercase; display: block; margin-bottom: 1px; background-color: #f0f0f0; padding: 1px 2px; }
        
        .grid-container { border: 1px solid #000; width: 100%; margin-bottom: 3px; }
        .grid-row { display: table; width: 100%; border-bottom: 1px solid #000; }
        .grid-row:last-child { border-bottom: none; }
        .grid-col { display: table-cell; padding: 2px; border-right: 1px solid #000; vertical-align: top; }
        .grid-col:last-child { border-right: none; }

        /* Altura controlada para las cajas */
        .box-detalles { height: 32px; overflow: hidden; }
        .box-diag { height: 32px; overflow: hidden; }

        .legal-text { font-size: 7px; margin-top: 3px; text-align: justify; margin-bottom: 2px; }
        .legal-list { list-style-type: none; padding-left: 0; margin: 1px 0; }
        .legal-list li { margin-bottom: 0; }

        .signatures-section { margin-top: 3px; }
        .sig-line { border-top: 1px solid #000; margin: 0 10px; padding-top: 2px; }
        
        .copy-label {
            text-align: right;
            font-size: 8px;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            margin-bottom: 2px;
            border-bottom: 1px solid #eee;
        }

        .icon-img {
            width: 10px; height: 10px; display: inline-block; vertical-align: middle; margin-right: 2px;
        }
    </style>
</head>
<body>
    @php
        $cliente = $orden->dispositivo->cliente;
        $dispositivo = $orden->dispositivo;
        $modelo = $dispositivo->modelo;
        
        $facebookIcon = '';
        $instagramIcon = '';
        
        try {
            if(file_exists(public_path('images/facebook.png'))) {
                $facebookIcon = base64_encode(file_get_contents(public_path('images/facebook.png')));
            }
            if(file_exists(public_path('images/instagram.png'))) {
                $instagramIcon = base64_encode(file_get_contents(public_path('images/instagram.png')));
            }
        } catch(\Exception $e) {
            // Ignorar errores de imagen
        }
        
        $copias = ['ORIGINAL: EMPRESA', 'COPIA: CLIENTE'];
    @endphp

    @foreach($copias as $index => $copiaLabel)
        <div class="receipt-section">
            <div class="copy-label">{{ $copiaLabel }}</div>

            <!-- Encabezado -->
            <div class="header-section">
                @if($empresa && $empresa->nombre && trim($empresa->nombre) !== '')
                    <div class="header-left">
                        <div class="header-empresa">{{ $empresa->nombre }}</div>
                        <div class="header-empresa-info">
                            @if($empresa->direccion)
                                {{ $empresa->direccion }}<br>
                            @endif
                            @if($empresa->telefono)
                                Tel: {{ $empresa->telefono }}
                            @endif
                            @if(($empresa->facebook_username || $empresa->instagram_username))
                                <div style="margin-top: 2px;">
                                    @if($empresa->facebook_username && $facebookIcon)
                                        <span style="margin-right: 5px;">
                                            <img src="data:image/png;base64,{{ $facebookIcon }}" class="icon-img">
                                            {{ $empresa->facebook_username }}
                                        </span>
                                    @endif
                                    @if($empresa->instagram_username && $instagramIcon)
                                        <span>
                                            <img src="data:image/png;base64,{{ $instagramIcon }}" class="icon-img">
                                            {{ $empresa->instagram_username }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="header-right">
                        <div class="title">RECIBO DE INGRESO</div>
                        <div class="orden-number">OT N°: {{ $orden->numero_orden }}</div>
                        <div style="font-size: 9px; margin-top: 2px;">{{ $orden->fecha_ingreso->format('d/m/Y H:i') }}</div>
                    </div>
                @else
                    <div class="header-left">
                        <div class="title">RECIBO DE INGRESO</div>
                        <div class="orden-number">OT N°: {{ $orden->numero_orden }}</div>
                    </div>
                @endif
            </div>

            <!-- Datos -->
            <table class="header-table">
                <tr>
                    <td style="width: 50%;">
                        <span class="section-title">CLIENTE</span>
                        <div><strong>Nombre:</strong> {{ $cliente ? Str::limit($cliente->nombre, 35) : '' }}</div>
                        <div><strong>Tel:</strong> {{ $cliente ? $cliente->telefono : '' }}</div>
                        @if($cliente && $cliente->rut)
                        <div><strong>RUT:</strong> {{ $cliente->rut }}</div>
                        @endif
                    </td>
                    <td style="width: 50%;">
                        <span class="section-title">EQUIPO</span>
                        <div><strong>Modelo:</strong> {{ $modelo ? Str::limit($modelo->marca . ' ' . $modelo->modelo, 35) : '' }}</div>
                        <div><strong>Color:</strong> {{ $dispositivo->color }} | <strong>IMEI:</strong> {{ $dispositivo->imei }}</div>
                        <div><strong>Pass/Patrón:</strong> {{ $dispositivo->contraseña ?? ($dispositivo->patron ? 'Patrón' : 'No') }}</div>
                    </td>
                </tr>
            </table>

            <!-- Grilla Detalles -->
            <div class="grid-container">
                <div class="grid-row">
                    <div class="grid-col box-detalles" style="width: 50%;">
                        <span class="section-title">PROBLEMA REPORTADO / DETALLES</span>
                        {{ Str::limit($orden->problema_reportado, 200) }}
                    </div>
                    <div class="grid-col box-detalles" style="width: 50%;">
                        <span class="section-title">OBSERVACIONES ESTADO</span>
                        {{ Str::limit($dispositivo->estado_dispositivo, 200) }}
                    </div>
                </div>
                <div class="grid-row">
                    <div class="grid-col box-diag" style="width: 60%;">
                        <span class="section-title">DIAGNÓSTICO TÉCNICO</span>
                        <!-- Espacio en blanco para escritura manual si no hay nada digital -->
                    </div>
                    <div class="grid-col" style="width: 20%;">
                        <span class="section-title">PRESUPUESTO</span>
                        <br>
                        @if($orden->costo_total > 0)
                            {{ Number::currency($orden->costo_total, precision: 0) }}
                        @else
                            <span style="color: #999;">Pendiente</span>
                        @endif
                    </div>
                    <div class="grid-col" style="width: 20%;">
                        <span class="section-title">ABONO</span>
                        <br>
                        <!-- Espacio para anotar abono -->
                    </div>
                </div>
            </div>

            <!-- Legal -->
            <div class="legal-text">
                <div style="font-weight: bold; margin-bottom: 1px;">CONDICIONES DEL SERVICIO:</div>
                <ul class="legal-list">
                    @foreach($terminos ?? [] as $i => $t)
                        <li>{{ $i + 1 }}. {{ Str::limit($t, 130) }}</li>
                    @endforeach
                </ul>
                <div style="margin-top: 2px; font-style: italic;">
                    Nota: Equipos no retirados en 60 días podrán ser liquidados para cubrir costos (Art. 2525 CC).
                </div>
            </div>

            <!-- Firmas -->
            <div class="signatures-section">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%; text-align: center;">
                            <div class="sig-line"></div>
                            <div>Firma Cliente</div>
                        </td>
                        <td style="width: 20%;"></td>
                        <td style="width: 40%; text-align: center;">
                            <div class="sig-line"></div>
                            <div>Recibido Por ({{ $empresa ? Str::limit($empresa->nombre, 15) : 'Empresa' }})</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        @if($index === 0)
            <div class="cut-line"></div>
        @endif
    @endforeach
</body>
</html>