<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recibo de Ingreso - OT {{ $orden->numero_orden }}</title>
    <style>
        @page {
            margin: 10mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 100%;
            min-height: calc(100vh - 20mm);
            display: flex;
            flex-direction: column;
        }
        
        .container-content {
            flex: 1;
        }
        
        .signatures-section {
            margin-top: auto;
            padding-top: 20px;
        }

        /* Header styles */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .header-table td {
            vertical-align: top;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            font-size: 10px;
        }

        .info-value {
            margin-bottom: 4px;
        }

        /* Grid Layout for Body */
        .grid-container {
            border: 2px solid #000;
            width: 100%;
            margin-bottom: 15px;
        }

        .grid-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }

        .grid-row:last-child {
            border-bottom: none;
        }

        .grid-col {
            display: table-cell;
            padding: 5px;
            border-right: 1px solid #000;
            vertical-align: top;
        }

        .grid-col:last-child {
            border-right: none;
        }

        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
            display: block;
        }

        /* Specific widths based on image roughly */
        .col-detalles { width: 40%; }
        .col-obs { width: 40%; }
        .col-fecha { width: 20%; }
        
        .col-diag { width: 60%; }
        .col-clave { width: 20%; }
        .col-valor { width: 20%; }

        /* Legal Text */
        .legal-text {
            font-size: 9px;
            margin-top: 10px;
            text-align: justify;
            width: 60%; /* Rotated text occupies left side usually but here it looks like standard text block or side text. 
                           In the image, the legal text is rotated 90deg on the left or placed on the side. 
                           Looking closely at the image description: "Al firmar...". 
                           The image shows the text is vertical on the left side or just main body text.
                           Wait, looking at the image again (mental reconstruction from user description "lo que hay mas arriba seguro son los datos...").
                           Actually, the image provided in my context is cut but it looks like a form.
                           Let's put the legal text below the form for clarity in a standard A4 receipt or half-sheet.
                           User said: "piensalo que es como un recibo que me tiene que traer el cliente".
                           I will place the legal text clearly visible. 
                        */
             margin-bottom: 20px;
        }

        .legal-list {
            list-style-type: none;
            padding-left: 0;
            margin: 5px 0;
        }

        .legal-list li {
            margin-bottom: 4px;
        }

        /* Signatures */
        .signatures-wrapper {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .sig-box {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: bottom;
        }

        .sig-spacer {
            display: table-cell;
            width: 20%;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin: 0 20px;
            padding-top: 5px;
        }
        
        .vertical-text {
             /* If we needed vertical text like a side-stub */
             /* transform: rotate(-90deg); */
        }

        .footer-copy {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
            font-size: 12px;
        }

        .header-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        
        .header-empresa {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .header-empresa-info {
            font-size: 12px;
            margin-top: 3px;
            line-height: 1.4;
        }

        .client-box {
            border: 1px solid #000;
            padding: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    @php
        $cliente = $orden->dispositivo->cliente;
        $dispositivo = $orden->dispositivo;
        $modelo = $dispositivo->modelo;
        
        // Iconos en base64
        $facebookIcon = base64_encode(file_get_contents(public_path('images/facebook.png')));
        $instagramIcon = base64_encode(file_get_contents(public_path('images/instagram.png')));
    @endphp

    <div class="container">
        <div class="container-content">
        <!-- Encabezado con Datos -->
        <div class="header-section">
            @if($empresa && $empresa->nombre)
                <div class="header-left">
                    <div class="header-empresa">{{ $empresa->nombre }}</div>
                    <div class="header-empresa-info">
                        @if($empresa->direccion)
                            {{ $empresa->direccion }}<br>
                        @endif
                        @if($empresa->telefono)
                            Tel: {{ $empresa->telefono }}
                        @endif
                        @if($empresa->facebook_username || $empresa->instagram_username)
                            <div style="margin-top: 5px;">
                                @if($empresa->facebook_username)
                                    <span style="display: inline-block; margin-right: 10px;">
                                        <img src="data:image/png;base64,{{ $facebookIcon }}" alt="Facebook" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; margin-right: 3px;">
                                        {{ $empresa->facebook_username }}
                                    </span>
                                @endif
                                @if($empresa->instagram_username)
                                    <span style="display: inline-block;">
                                        <img src="data:image/png;base64,{{ $instagramIcon }}" alt="Instagram" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; margin-right: 3px;">
                                        {{ $empresa->instagram_username }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="header-right">
                    <div class="title">RECIBO DE INGRESO</div>
                    <div><strong>N° Orden: {{ $orden->numero_orden }}</strong></div>
                </div>
            @else
                <div class="header-left">
                    <div class="title">RECIBO DE INGRESO</div>
                    <div><strong>N° Orden: {{ $orden->numero_orden }}</strong></div>
                </div>
            @endif
        </div>

        <!-- Datos Cliente y Equipo -->
        <table class="header-table" style="margin-bottom: 10px; width: 100%; border: 1px solid #000;">
            <tr>
                <td style="padding: 5px; border-right: 1px solid #000; width: 50%;">
                    <div class="section-title">DATOS DEL CLIENTE</div>
                    <div><strong>Nombre:</strong> {{ $cliente ? $cliente->nombre : '' }}</div>
                    <div><strong>Teléfono:</strong> {{ $cliente ? $cliente->telefono : '' }}</div>
                    <div><strong>Email:</strong> {{ $cliente ? $cliente->email : '' }}</div>
                    @if($cliente && $cliente->rut)
                    <div><strong>RUT:</strong> {{ $cliente->rut }}</div>
                    @endif
                </td>
                <td style="padding: 5px; width: 50%;">
                    <div class="section-title">DATOS DEL EQUIPO</div>
                    <div><strong>Marca/Modelo:</strong> {{ $modelo ? $modelo->marca . ' ' . $modelo->modelo : '' }}</div>
                    <div><strong>Color:</strong> {{ $dispositivo->color }}</div>
                    <div><strong>IMEI:</strong> {{ $dispositivo->imei }}</div>
                    <div><strong>Contraseña:</strong> {{ $dispositivo->contraseña ?? 'No informada' }}</div>
                    @if($dispositivo->patron)
                    <div><strong>Patrón:</strong> Sí</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Grilla Principal -->
        <div class="grid-container">
            <!-- Fila 1: Detalles, Observaciones, Fecha -->
            <div class="grid-row">
                <div class="grid-col col-detalles" style="height: 100px;">
                    <span class="section-title">Detalles:</span>
                    {{ $orden->problema_reportado }}
                </div>
                <div class="grid-col col-obs">
                    <span class="section-title">Observaciones:</span>
                    {{ $dispositivo->estado_dispositivo }}
                </div>
                <div class="grid-col col-fecha" style="text-align: center;">
                    <span class="section-title">Fecha Recepción *2:</span>
                    <br>
                    {{ $orden->fecha_ingreso->format('d-m-Y') }}
                </div>
            </div>

            <!-- Fila 2: Diagnostico, Clave, Valor Total -->
            <div class="grid-row">
                <div class="grid-col col-diag" style="height: 100px;">
                    <span class="section-title">Diagnostico Técnico:</span>
                    <!-- Espacio para escribir a mano o llenar si existe un diagnóstico inicial -->
                </div>
                <div class="grid-col col-clave">
                    <span class="section-title">CLAVE:</span>
                    <br><br>
                    {{ $dispositivo->contraseña }}
                </div>
                <div class="grid-col col-valor">
                    <span class="section-title">Valor total:</span>
                    <br><br>
                    @if($orden->costo_total > 0)
                        {{ Number::currency($orden->costo_total, precision: 0) }}
                    @endif
                </div>
            </div>
        </div>

        <div class="footer-copy">
            Copia Cliente
        </div>

        <!-- Sección Legal -->
        <div style="width: 100%; margin-bottom: 20px;">
            <div class="legal-text" style="width: 100%;">
                <strong>Al firmar, el cliente acepta las siguientes condiciones para su servicio:</strong>
                <ul class="legal-list">
                    @foreach($terminos ?? [] as $index => $termino)
                        <li><strong>{{ $index + 1 }}*</strong> {{ $termino }}</li>
                    @endforeach
                </ul>
                <p>
                    En caso de NO RETIRO DEL EQUIPO despues de 60 dias, nuestra empresa se guarda el derecho de disponer del equipo como forma de pago del servicio, sin lugar a reclamos posteriores. (Articulo 2525, 2526 Codigo Civil)

                </p>
            </div>
        </div>
        </div>

        <!-- Sección Firmas -->
        <div class="signatures-section" style="width: 100%;">
            <div style="display: table; width: 100%;">
                <div style="display: table-cell; width: 50%; text-align: center; padding-right: 20px;">
                    <div class="sig-line"></div>
                    <div>Firma Cliente</div>
                </div>
                <div style="display: table-cell; width: 50%; text-align: center;">
                    <div class="sig-line"></div>
                    <div>Equipo entregado</div>
                </div>
            </div>
        </div>

    </div>
    
    <!-- Optional: Separator for two copies on one sheet if needed, but user asked for "the pdf of the image" -->
</body>
</html>

