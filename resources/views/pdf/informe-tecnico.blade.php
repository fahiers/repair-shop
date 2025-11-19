<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe Técnico - OT {{ $orden->numero_orden }}</title>
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
            margin-bottom: 15px;
        }
        
        .header-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header-info {
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .separator-line {
            border-bottom: 1px solid #000;
            margin: 15px 0;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
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
            margin-bottom: 3px;
        }
        
        .info-value {
            margin-bottom: 8px;
        }
        
        .device-info {
            text-align: right;
        }
        
        .device-brand {
            font-size: 11px;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 5px;
        }
        
        .device-model {
            font-size: 11px;
        }
        
        .description-section {
            margin: 20px 0;
            min-height: 400px;
        }
        
        .signatures-spacer {
            height: 150px;
        }
        
        .description-text {
            font-size: 11px;
            line-height: 1.6;
            text-align: justify;
        }
        
        .no-informe {
            text-align: center;
            padding: 40px 20px;
            font-size: 14px;
            color: #666;
            font-style: italic;
        }
        
        .signatures-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        
        .signature-left, .signature-center, .signature-right {
            display: table-cell;
            width: 33.33%;
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
        
        .signature-name {
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    @php
        $cliente = $orden->dispositivo->cliente;
        $dispositivo = $orden->dispositivo;
        $modelo = $dispositivo->modelo;
        
        // Usar el número de orden como número de informe
        $numeroInforme = $orden->numero_orden;
        
        // Fecha del informe técnico o fecha actual
        $fechaInforme = $informeTecnico ? $informeTecnico->created_at : now();
        
        // Formatear fecha en español
        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];
        $fechaFormateada = $fechaInforme->format('d') . ' ' . $meses[(int)$fechaInforme->format('n')] . ' ' . $fechaInforme->format('Y');
    @endphp

    <!-- Encabezado -->
    <div class="header">
        <div class="header-title">INFORME TECNICO</div>
        <div class="header-info">#{{ $numeroInforme }} • {{ $fechaFormateada }}</div>
    </div>
    
    <!-- Línea separadora -->
    <div class="separator-line"></div>
    
    <!-- Información del cliente y dispositivo -->
    <div class="info-section">
        <div class="info-left">
            <div class="info-label">DNI:</div>
            <div class="info-value">{{ $cliente && $cliente->rut ? $cliente->rut : '-' }}</div>
            
            <div class="info-label">Teléfono:</div>
            <div class="info-value">{{ $cliente && $cliente->telefono ? $cliente->telefono : '-' }}</div>
            
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $cliente && $cliente->email ? $cliente->email : '-' }}</div>
        </div>
        <div class="info-right device-info">
            @if($modelo)
                <div class="device-model">Marca y modelo: {{ $modelo->marca }} {{ $modelo->modelo }}@if($modelo->anio) {{ $modelo->anio }}@endif</div>
            @else
                <div class="device-model">Dispositivo sin modelo</div>
            @endif
        </div>
    </div>
    
    <!-- Línea separadora -->
    <div class="separator-line"></div>
    
    <!-- Descripción del informe técnico -->
    <div class="description-section">
        @if($informeTecnico && $informeTecnico->comentario)
            <div class="description-text">
                {{ $informeTecnico->comentario }}
            </div>
        @else
            <div class="no-informe">
                No hay informe técnico disponible para esta orden de trabajo.
            </div>
        @endif
    </div>
    
    <!-- Espaciador para empujar firmas al final -->
    <div class="signatures-spacer"></div>
    
    <!-- Firmas -->
    <div class="signatures-section">
        <div class="signature-left">
            <div class="signature-label">Fecha de entrega</div>
            <div class="signature-line"></div>
        </div>
        <div class="signature-center">
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

