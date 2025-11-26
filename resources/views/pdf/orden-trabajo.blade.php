<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden de Trabajo - OT {{ $orden->numero_orden }}</title>
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
            font-size: 18px;
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
            font-size: 14px;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 5px;
        }
        
        .device-model {
            font-size: 12px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table td {
            vertical-align: top;
        }
        
        .col-producto {
            width: 50%;
        }
        
        .col-precio {
            width: 20%;
            text-align: right;
        }
        
        .col-cantidad {
            width: 15%;
            text-align: center;
        }
        
        .col-total {
            width: 15%;
            text-align: right;
        }
        
        .totals-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .totals-left {
            display: table-cell;
            width: 60%;
        }
        
        .totals-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            padding-left: 20px;
        }
        
        .total-row {
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .total-label {
            display: inline-block;
            width: 120px;
            text-align: left;
        }
        
        .total-value {
            display: inline-block;
            width: 100px;
            text-align: right;
        }
        
        .total-final {
            font-weight: bold;
            font-size: 13px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
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
        }
        
        .signature-label {
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .signature-name {
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        // Usar los valores guardados en la orden
        $subtotal = $orden->subtotal ?? 0;
        $montoIva = $orden->monto_iva ?? 0;
        $total = $orden->costo_total ?? 0;
        
        // Si no hay valores guardados, calcular desde los items
        if ($subtotal == 0) {
            $subtotal = $orden->servicios()->sum('orden_servicio.subtotal') + 
                        $orden->productos()->sum('orden_producto.subtotal');
            $porcentajeIva = 19;
            $montoIva = $subtotal * ($porcentajeIva / 100);
            $total = $subtotal + $montoIva;
        }
        
        // Formatear valores usando Number helper
        $subtotalFormatted = \Illuminate\Support\Number::currency($subtotal, precision: 0);
        $ivaFormatted = \Illuminate\Support\Number::currency($montoIva, precision: 0);
        $totalFormatted = \Illuminate\Support\Number::currency($total, precision: 0);
        
        $cliente = $orden->dispositivo->cliente;
        $dispositivo = $orden->dispositivo;
        $modelo = $dispositivo->modelo;
    @endphp

    <!-- Encabezado -->
    <div class="header">
        <div class="header-title">ORDEN DE TRABAJO</div>
        <div class="header-info">#{{ $orden->numero_orden }} • {{ $orden->fecha_ingreso->format('d M Y') }}</div>
    </div>
    
    <!-- Línea separadora -->
    <div class="separator-line"></div>
    
    <!-- Información del cliente y dispositivo -->
    <div class="info-section">
        <div class="info-left">
            <div class="info-value">{{ $cliente ? $cliente->nombre : 'No especificado' }}</div>
            <div class="info-value">{{ $cliente && $cliente->rut ? $cliente->rut : '-' }}</div>
            <div class="info-value">{{ $cliente && $cliente->telefono ? $cliente->telefono : '-' }}</div>
            <div class="info-value">{{ $cliente && $cliente->email ? $cliente->email : '-' }}</div>
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
    
    <!-- Tabla de productos/servicios -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-producto">Producto o servicio</th>
                <th class="col-precio">Precio</th>
                <th class="col-cantidad">Cant</th>
                <th class="col-total">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $allItems = collect();
                
                // Agregar servicios
                foreach ($orden->servicios as $servicio) {
                    $allItems->push([
                        'nombre' => $servicio->nombre,
                        'precio_unitario' => $servicio->pivot->precio_unitario,
                        'cantidad' => $servicio->pivot->cantidad,
                        'subtotal' => $servicio->pivot->subtotal,
                    ]);
                }
                
                // Agregar productos
                foreach ($orden->productos as $producto) {
                    $allItems->push([
                        'nombre' => $producto->nombre,
                        'precio_unitario' => $producto->pivot->precio_unitario,
                        'cantidad' => $producto->pivot->cantidad,
                        'subtotal' => $producto->pivot->subtotal,
                    ]);
                }
            @endphp
            
            @forelse($allItems as $item)
            <tr>
                <td class="col-producto">{{ $item['nombre'] }}</td>
                <td class="col-precio">{{ Number::currency($item['precio_unitario'], precision: 0) }}</td>
                <td class="col-cantidad">{{ Number::format($item['cantidad']) }}</td>
                <td class="col-total">{{ Number::currency($item['subtotal'], precision: 0) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Sin items registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Línea separadora -->
    <div class="separator-line"></div>
    
    <!-- Totales -->
    <div class="totals-section">
        <div class="totals-left"></div>
        <div class="totals-right">
            <div class="total-row">
                <span class="total-label">Subtotal</span>
                <span class="total-value">{{ $subtotalFormatted }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">IVA</span>
                <span class="total-value">{{ $ivaFormatted }}</span>
            </div>
            <div class="total-row total-final">
                <span class="total-label">Total</span>
                <span class="total-value">{{ $totalFormatted }}</span>
            </div>
            <div class="total-row" style="margin-top: 10px;">
                <span class="total-label">Pagado:</span>
                <span class="total-value">{{ Number::currency($orden->calcularTotalPagado(), precision: 0) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Saldo pendiente:</span>
                <span class="total-value">{{ Number::currency($orden->saldo ?? 0, precision: 0) }}</span>
            </div>
        </div>
    </div>
    
    <!-- Firmas -->
    <div class="signatures-section">
        <div class="signature-left">
            <div class="signature-label">Firma del técnico</div>
            <div class="signature-line"></div>
        </div>
        <div class="signature-right">
            <div class="signature-label">Firma del cliente</div>
            <div class="signature-line"></div>
        </div>
    </div>
    
    <!-- Línea final -->
    <div class="separator-line" style="margin-top: 20px;"></div>
</body>
</html>
