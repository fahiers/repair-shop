<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sticker térmico - OT {{ $orden->numero_orden }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        /* Contenedor del sticker, más angosto que los 88mm del papel */
        .sticker {
            width: 60mm;              /* tamaño efectivo de sticker */
            padding: 2mm;
            border: 1px solid #000;  /* opcional: guía de corte */
            margin: 2mm auto;        /* centrado en el papel de 88mm */
            box-sizing: border-box;
        }

        .row {
            margin-bottom: 1mm;
        }

        .label {
            font-weight: bold;
        }

        .small {
            font-size: 9px;
        }

        .center {
            text-align: center;
        }

        .field-row {
            display: flex;
            flex-direction: column;
            gap: 0.5mm;
            margin-bottom: 1mm;
        }

        .field-label {
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
        }

        .field-value {
            font-size: 8px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.2;
        }

        .field-value-large {
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        $cliente = $orden->dispositivo->cliente ?? null;
        $dispositivo = $orden->dispositivo ?? null;
        $modelo = $dispositivo->modelo ?? null;
        
        // Formatear fecha de compromiso
        $fechaCompromiso = '';
        if ($orden->fecha_entrega_estimada) {
            $fechaCompromiso = $orden->fecha_entrega_estimada->format('m/d/Y');
        }
        
        // Obtener marca y modelo del dispositivo
        $dispositivoTexto = '';
        if ($modelo) {
            $dispositivoTexto = $modelo->marca;
            if ($modelo->modelo) {
                $dispositivoTexto .= ' - ' . $modelo->modelo;
            }
        }
    @endphp

    <div class="sticker">
        <div class="center small">
            Orden #{{ $orden->numero_orden }}
        </div>

        <div class="field-row">
            <span class="field-label">Nombre Cliente:</span>
            <span class="field-value">{{ $cliente ? Str::limit($cliente->nombre, 22) : '' }}</span>
        </div>

        <div class="field-row">
            <span class="field-label">Dispositivo:</span>
            <span class="field-value">{{ Str::limit($dispositivoTexto, 24) }}</span>
        </div>

        <div class="field-row">
            <span class="field-label">Problema:</span>
            <span class="field-value">{{ Str::limit($orden->problema_reportado ?? '', 40) }}</span>
        </div>

        @if($fechaCompromiso)
            <div class="field-row">
                <span class="field-label">Fecha Compromiso:</span>
                <span class="field-value">{{ $fechaCompromiso }}</span>
            </div>
        @endif

        <div class="row small">
            Fecha: {{ $orden->created_at->format('d-m-Y') }}
        </div>
    </div>
</body>
</html>

