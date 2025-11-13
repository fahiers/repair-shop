<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Orden de Trabajo - OT {{ $orden->numero_orden }}</title>
    <style>
        @page {
            margin: 2mm 5mm 2mm 5mm;
            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 10px;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
            line-height: 1.2;
        }
        
        .header-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .header-left, .header-center, .header-right {
            display: table-cell;
            vertical-align: top;
            width: 33.33%;
        }
        
        .header-center {
            text-align: center;
        }
        
        .header-right {
            text-align: right;
        }
        
        .header-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin: 5px 0;
            position: relative;
            left: -50px;
        }
        
        .trabajo-info {
            margin: 8px 0;
        }
        
        .separator-line {
            border-bottom: 2px solid #000;
            margin: 20px 0 8px 0;
        }
        
        .cliente-section {
            display: table;
            width: 100%;
            margin: 8px 0;
        }
        
        .cliente-left, .cliente-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        
        .cliente-right {
            text-align: right;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: left;
            vertical-align: top;
        }
        
        .col-descripcion {
            padding-left: 3px;
            padding-right: 2px;
        }
        
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .col-item {
            width: 5%;
            text-align: center;
        }
        
        .col-cantidad {
            width: 7%;
            text-align: center;
        }
        
        .col-tipo {
            width: 10%;
            text-align: center;
        }
        
        .col-descripcion {
            width: 50%;
        }
        
        .col-precio-unit {
            width: 12%;
            text-align: right;
        }
        
        .col-total {
            width: 16%;
            text-align: right;
        }
        
        .numero-ot-destacado {
            background-color: #ffff00;
            padding: 3px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .logo-container {
            float: left;
            margin-right: 15px;
            text-align: left;
        }
        
        .logo-text {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .preparado-text {
            font-size: 9px;
            margin-top: 2px;
        }
        
        .trabajo-info {
            margin-top: 5px;
            clear: left;
        }
    </style>
</head>
<body>
    <!-- Encabezado superior con logo -->
    <div class="header-row">
        <div class="header-left">
            <div class="logo-container">
                <div class="logo-text">Taller Tecnico</div>
                <div class="preparado-text">Preparado por: {{ $orden->tecnico ? $orden->tecnico->name : 'N/A' }}</div>
                <div class="trabajo-info">
                    <strong>Trabajo N°: <span class="numero-ot-destacado">{{ $orden->numero_orden }}</span></strong>
                </div>
            </div>
        </div>
        <div class="header-center">
        </div>
        <div class="header-right">
            Página 1 de 1
        </div>
    </div>
    
    <!-- Segunda fila del encabezado -->
    <div class="header-row">
        <div class="header-left">
        </div>
        <div class="header-center">
            <div class="header-title">Orden de Trabajo</div>
        </div>
        <div class="header-right">
            {{ now()->format('d-m-Y H:i:s') }}
        </div>
    </div>
    
    <!-- Tercera fila del encabezado -->
    <div class="header-row">
        <div class="header-left">
        </div>
        <div class="header-center"></div>
        <div class="header-right">
            FECHA DE ENTREGA: {{ $orden->fecha_entrega_estimada ? $orden->fecha_entrega_estimada->format('d-m-y') : 'No definida' }}
        </div>
    </div>
    
    <!-- Línea separadora -->
    <div class="separator-line"></div>
    
    <!-- Información del cliente -->
    <div class="cliente-section">
        <div class="cliente-left">
            <strong>Cliente:</strong> {{ $orden->dispositivo->cliente ? $orden->dispositivo->cliente->nombre : 'No especificado' }}
        </div>
        <div class="cliente-right">
            <strong>Fecha OT:</strong> {{ $orden->fecha_ingreso->format('d-m-Y') }}
        </div>
    </div>
    
    <!-- Tabla de items -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-item">Item</th>
                <th class="col-cantidad">Cant.</th>
                <th class="col-tipo">Tipo</th>
                <th class="col-descripcion">Descripción Item</th>
                <th class="col-precio-unit">Precio Unit.</th>
                <th class="col-total">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $itemIndex = 0;
                $allItems = collect();
                
                // Agregar servicios
                foreach ($orden->servicios as $servicio) {
                    $allItems->push([
                        'tipo' => 'servicio',
                        'nombre' => $servicio->nombre,
                        'cantidad' => $servicio->pivot->cantidad,
                        'precio_unitario' => $servicio->pivot->precio_unitario,
                        'subtotal' => $servicio->pivot->subtotal,
                        'descripcion' => $servicio->pivot->descripcion ?? null,
                    ]);
                }
                
                // Agregar productos
                foreach ($orden->productos as $producto) {
                    $allItems->push([
                        'tipo' => 'producto',
                        'nombre' => $producto->nombre,
                        'cantidad' => $producto->pivot->cantidad,
                        'precio_unitario' => $producto->pivot->precio_unitario,
                        'subtotal' => $producto->pivot->subtotal,
                        'descripcion' => null,
                    ]);
                }
            @endphp
            
            @forelse($allItems as $item)
            <tr>
                <td class="col-item">{{ ++$itemIndex }}</td>
                <td class="col-cantidad">{{ number_format($item['cantidad'], 0, ',', '.') }}</td>
                <td class="col-tipo">{{ ucfirst($item['tipo']) }}</td>
                <td class="col-descripcion">
                    {{ $item['nombre'] }}
                </td>
                <td class="col-precio-unit">
                    ${{ number_format($item['precio_unitario'], 0, ',', '.') }}
                </td>
                <td class="col-total">
                    ${{ number_format($item['subtotal'], 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="col-item">1</td>
                <td class="col-cantidad"></td>
                <td class="col-tipo"></td>
                <td class="col-descripcion">Sin items registrados</td>
                <td class="col-precio-unit"></td>
                <td class="col-total"></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
