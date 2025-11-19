<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Condiciones y Garantía del Servicio</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            margin-bottom: 10px;
        }
        
        .header-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header-info {
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .separator-line {
            border-bottom: 1px solid #000;
            margin: 8px 0;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .info-left, .info-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            padding-right: 20px;
        }
        
        .info-right {
            padding-right: 0;
            padding-left: 20px;
        }
        
        .info-label {
            font-weight: bold;
            margin-bottom: 1px;
            font-size: 10px;
        }
        
        .info-value {
            margin-bottom: 3px;
            font-size: 11px;
        }
        
        .device-info {
            text-align: right;
        }
        
        .device-brand {
            font-size: 12px;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 2px;
        }
        
        .device-model {
            font-size: 11px;
        }
        
        .content-section {
            margin: 10px 0;
        }
        
        .content-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .content-text {
            font-size: 11px;
            line-height: 1.4;
            text-align: justify;
        }
        
        .signatures-spacer {
            height: 150px;
        }
        
        .signatures-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        
        .signature-left, .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding-top: 50px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 5px;
            padding-top: 5px;
            height: 40px;
        }
        
        .signature-label {
            font-size: 10px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    @php
        // Formatear fecha en español
        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];
        $fechaActual = now();
        $fechaFormateada = $fechaActual->format('d') . ' ' . $meses[(int)$fechaActual->format('n')] . ' ' . $fechaActual->format('Y');
        
        $numeroDocumento = $orden ? $orden->numero_orden : '1';
        
        if ($orden) {
            $cliente = $orden->dispositivo->cliente;
            $dispositivo = $orden->dispositivo;
            $modelo = $dispositivo->modelo;
        } else {
            $cliente = null;
            $dispositivo = null;
            $modelo = null;
        }
    @endphp

    <!-- Encabezado -->
    <div class="header">
        <div class="header-title">Condiciones y garantía</div>
        <div class="header-info">#{{ $numeroDocumento }} • {{ $fechaFormateada }}</div>
    </div>
    
    <!-- Línea separadora -->
    <div class="separator-line"></div>
    
    @if($orden && $cliente)
        <!-- Información del cliente y dispositivo -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-value" style="margin-bottom: 4px; font-weight: bold; font-size: 12px;">{{ $cliente->nombre ?? '-' }}</div>
                <div style="margin-bottom: 2px;"><span class="info-label">DNI:</span> <span class="info-value" style="margin-bottom: 0; display: inline;">{{ $cliente && $cliente->rut ? $cliente->rut : '-' }}</span></div>
                <div style="margin-bottom: 2px;"><span class="info-label">Teléfono:</span> <span class="info-value" style="margin-bottom: 0; display: inline;">{{ $cliente && $cliente->telefono ? $cliente->telefono : '-' }}</span></div>
                <div style="margin-bottom: 2px;"><span class="info-label">Email:</span> <span class="info-value" style="margin-bottom: 0; display: inline;">{{ $cliente && $cliente->email ? $cliente->email : '-' }}</span></div>
            </div>
            <div class="info-right device-info">
                @if($modelo)
                    <div class="device-brand">{{ strtoupper($modelo->marca) }}</div>
                    <div class="device-model">{{ $modelo->modelo }}@if($modelo->anio) {{ $modelo->anio }}@endif</div>
                @else
                    <div class="device-model">Dispositivo sin modelo</div>
                @endif
            </div>
        </div>
        
        <!-- Línea separadora -->
        <div class="separator-line"></div>
    @endif
    
    <!-- Contenido -->
    <div class="content-section">
        <div class="content-title">Condiciones y Garantía del Servicio</div>
        <div class="content-text">
            @foreach(explode("\n", $contenido) as $line)
                @php $line = trim($line); @endphp
                @if(empty($line)) 
                    @continue 
                @endif
                
                @if(preg_match('/^\d+\./', $line))
                    <div style="font-weight: bold; margin-top: 12px; margin-bottom: 3px;">{{ $line }}</div>
                @else
                    <div style="margin-bottom: 3px;">{{ $line }}</div>
                @endif
            @endforeach
        </div>
    </div>
    
    <!-- Espaciador para empujar firmas al final -->
    <div class="signatures-spacer"></div>
    
    <!-- Firmas -->
    <div class="signatures-section">
        <div class="signature-left">
            <div class="signature-label">Firma técnico</div>
            <div class="signature-line"></div>
        </div>
        <div class="signature-right">
            <div class="signature-label">Firma cliente</div>
            <div class="signature-line"></div>
        </div>
    </div>
</body>
</html>
