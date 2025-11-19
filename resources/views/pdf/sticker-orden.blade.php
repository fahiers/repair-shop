<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sticker - OT {{ $orden->numero_orden }}</title>
    <style>
        @page {
            margin: 0;
            size: letter portrait;
        }
        
        * {
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        body {
            font-family: Arial, sans-serif;
        }
        
        .sticker-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 60mm;
            height: 40mm;
            padding: 2mm;
            border: 1px solid #000;
            box-sizing: border-box;
            background: white;
        }
        
        .sticker-content {
            display: flex;
            flex-direction: column;
            height: 100%;
            gap: 1.5mm;
            overflow: hidden;
        }
        
        .field-row {
            display: flex;
            flex-direction: column;
            gap: 0.5mm;
            flex-shrink: 0;
        }
        
        .field-label {
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            color: #000;
        }
        
        .field-value {
            font-size: 8px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            color: #000;
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

    <div class="sticker-container">
        <div class="sticker-content">
            <div class="field-row">
                <span class="field-label">Nombre Cliente:</span>
                <span class="field-value">{{ $cliente ? $cliente->nombre : '' }}</span>
            </div>
            
            <div class="field-row">
                <span class="field-label"># Orden:</span>
                <span class="field-value field-value-large">{{ $orden->numero_orden }}</span>
            </div>
            
            <div class="field-row">
                <span class="field-label">Dispositivo:</span>
                <span class="field-value">{{ $dispositivoTexto }}</span>
            </div>
            
            <div class="field-row">
                <span class="field-label">Problema:</span>
                <span class="field-value">{{ $orden->problema_reportado ?? '' }}</span>
            </div>
            
            <div class="field-row">
                <span class="field-label">Fecha Compromiso:</span>
                <span class="field-value">{{ $fechaCompromiso }}</span>
            </div>
        </div>
    </div>
</body>
</html>

