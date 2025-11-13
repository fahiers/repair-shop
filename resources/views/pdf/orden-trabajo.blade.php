<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Trabajo - {{ $orden->numero_orden }}</title>

    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #0f172a;
            padding: 22px;
            line-height: 1.55;
        }

        /* ---------- PALETA SAMSUNG ---------- */
        :root {
            --blue-light: #e0f2fe;
            --blue-primary: #0d6efd;
            --blue-dark: #1e40af;
            --gray-light: #f1f5f9;
            --gray-border: #cbd5e1;
        }

        /* ---------- ENCABEZADO ---------- */
        .header {
            border-bottom: 3px solid var(--blue-primary);
            margin-bottom: 25px;
            padding-bottom: 12px;
        }
        .header-title {
            font-size: 26px;
            font-weight: bold;
            color: var(--blue-dark);
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 11px;
            color: #475569;
        }

        /* ---------- SECCIONES ---------- */
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: var(--blue-dark);
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid var(--blue-primary);
            padding-left: 8px;
        }

        /* ---------- COLUMNAS ---------- */
        .columns {
            width: 100%;
            border-spacing: 14px 0;
        }
        .col {
            width: 50%;
            vertical-align: top;
        }

        /* ---------- TARJETAS ---------- */
        .card {
            background: var(--gray-light);
            border: 1px solid var(--gray-border);
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 14px;
        }

        /* ---------- INFO ROW ---------- */
        .info-row { margin-bottom: 6px; }
        .info-label { font-weight: bold; color: #334155; }

        /* ---------- BADGES ---------- */
        .badge {
            padding: 3px 7px;
            font-size: 10px;
            border-radius: 4px;
            font-weight: bold;
            border: 1px solid #e2e8f0;
        }

        .badge-pendiente { background:#fff7ed; color:#c2410c; border-color:#fed7aa; }
        .badge-diagnostico { background:#dbeafe; color:#1e3a8a; border-color:#bfdbfe; }
        .badge-en_reparacion { background:#fce7f3; color:#9d174d; border-color:#fbcfe8; }
        .badge-espera_repuesto { background:#fff7ed; color:#b45309; border-color:#fed7aa; }
        .badge-listo { background:#d1fae5; color:#047857; border-color:#a7f3d0; }
        .badge-entregado { background:#d1fae5; color:#047857; border-color:#a7f3d0; }
        .badge-cancelado { background:#fee2e2; color:#b91c1c; border-color:#fecaca; }

        /* ---------- BOX DE TEXTO ---------- */
        .textbox {
            background: var(--blue-light);
            border: 1px solid #bae6fd;
            padding: 12px;
            border-radius: 6px;
        }

        /* ---------- TABLAS ---------- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 6px;
        }
        th {
            background: var(--blue-primary);
            color: white;
            padding: 8px;
            text-align: left;
            letter-spacing: 0.3px;
        }
        td {
            padding: 7px 9px;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-child(even) td {
            background: #f8fafc;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* ---------- RESUMEN FINANCIERO ---------- */
        .totals-box {
            width: 280px;
            margin-left: auto;
            margin-top: 6px;
            background: var(--gray-light);
            border: 1px solid var(--gray-border);
            border-radius: 6px;
            padding: 12px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .total-final {
            border-top: 2px solid var(--blue-primary);
            padding-top: 7px;
            margin-top: 6px;
            font-weight: bold;
            font-size: 14px;
            color: var(--blue-dark);
        }

        /* ---------- COMENTARIOS ---------- */
        .comment {
            background: #f1f5f9;
            border-left: 3px solid var(--blue-primary);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .comment-meta {
            font-size: 10px;
            color: #64748b;
        }

        /* ---------- FOOTER ---------- */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #64748b;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            margin-top: 40px;
        }
    </style>
</head>

<body>

<!-- ENCABEZADO -->
<div class="header">
    <div class="header-title">ORDEN DE TRABAJO</div>
    <div class="subtitle">Centro de Servicio Técnico</div>
</div>

<!-- COLUMNAS: ORDEN + CLIENTE -->
<table class="columns">
    <tr>
        <td class="col">
            <div class="section-title">Información de la Orden</div>
            <div class="card">
                <div class="info-row"><span class="info-label">N° Orden:</span> {{ $orden->numero_orden }}</div>
                <div class="info-row"><span class="info-label">Ingreso:</span> {{ $orden->fecha_ingreso?->format('d/m/Y') }}</div>

                @if($orden->fecha_entrega_estimada)
                <div class="info-row"><span class="info-label">Entrega Estimada:</span> {{ $orden->fecha_entrega_estimada->format('d/m/Y') }}</div>
                @endif

                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="badge badge-{{ $orden->estado->value }}">{{ $orden->estado->etiqueta() }}</span>
                </div>

                @if($orden->tecnico)
                <div class="info-row"><span class="info-label">Técnico:</span> {{ $orden->tecnico->name }}</div>
                @endif
            </div>
        </td>


        <td class="col">
            <div class="section-title">Información del Cliente</div>
            <div class="card">
                <div class="info-row"><span class="info-label">Nombre:</span> {{ $orden->dispositivo->cliente->nombre }}</div>

                @if($orden->dispositivo->cliente->telefono)
                <div class="info-row"><span class="info-label">Teléfono:</span> {{ $orden->dispositivo->cliente->telefono }}</div>
                @endif

                @if($orden->dispositivo->cliente->email)
                <div class="info-row"><span class="info-label">Email:</span> {{ $orden->dispositivo->cliente->email }}</div>
                @endif

                @if($orden->dispositivo->cliente->direccion)
                <div class="info-row"><span class="info-label">Dirección:</span> {{ $orden->dispositivo->cliente->direccion }}</div>
                @endif

                @if($orden->dispositivo->cliente->rut)
                <div class="info-row"><span class="info-label">RUT:</span> {{ $orden->dispositivo->cliente->rut }}</div>
                @endif
            </div>
        </td>
    </tr>
</table>

<!-- COLUMNS DISPOSITIVO -->
<div class="section-title">Información del Dispositivo</div>
<table class="columns">
    <tr>
        <td class="col">
            <div class="card">
                @if($orden->dispositivo->modelo)
                <div class="info-row"><span class="info-label">Marca:</span> {{ $orden->dispositivo->modelo->marca }}</div>
                <div class="info-row"><span class="info-label">Modelo:</span> {{ $orden->dispositivo->modelo->modelo }}</div>
                <div class="info-row"><span class="info-label">Año:</span> {{ $orden->dispositivo->modelo->anio }}</div>
                @endif
            </div>
        </td>

        <td class="col">
            <div class="card">
                @if($orden->dispositivo->imei)
                <div class="info-row"><span class="info-label">IMEI:</span> {{ $orden->dispositivo->imei }}</div>
                @endif
                @if($orden->dispositivo->color)
                <div class="info-row"><span class="info-label">Color:</span> {{ $orden->dispositivo->color }}</div>
                @endif
                <div class="info-row">
                    <span class="info-label">Accesorios:</span>
                    @php
                        $accesoriosNombres = [];
                        if ($orden->dispositivo->accesorios && count(array_filter($orden->dispositivo->accesorios)) > 0) {
                            // Cargar todos los accesorios activos una sola vez
                            $accesoriosActivos = \App\Models\AccesorioConfig::where('activo', true)->get()->keyBy(function($acc) {
                                return \Illuminate\Support\Str::slug($acc->nombre, '_');
                            });
                            
                            foreach ($orden->dispositivo->accesorios as $clave => $seleccionado) {
                                if ($seleccionado && isset($accesoriosActivos[$clave])) {
                                    $accesoriosNombres[] = $accesoriosActivos[$clave]->nombre;
                                }
                            }
                        }
                    @endphp
                    {{ !empty($accesoriosNombres) ? implode(', ', $accesoriosNombres) : 'Sin accesorios' }}
                </div>
            </div>
        </td>
    </tr>
</table>

<!-- PROBLEMA -->
<div class="section-title">Problema Reportado</div>
<div class="textbox">{{ $orden->problema_reportado }}</div>

<!-- DIAGNOSTICO -->
@if($orden->diagnostico)
<div class="section-title">Diagnóstico</div>
<div class="textbox">{{ $orden->diagnostico }}</div>
@endif

<!-- SERVICIOS -->
@if($orden->servicios->count() > 0)
<div class="section-title">Servicios</div>
<table>
    <thead>
        <tr>
            <th>Descripción</th>
            <th class="text-center">Cant.</th>
            <th class="text-right">P. Unitario</th>
            <th class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orden->servicios as $s)
        <tr>
            <td>{{ $s->nombre }}</td>
            <td class="text-center">{{ $s->pivot->cantidad }}</td>
            <td class="text-right">${{ number_format($s->pivot->precio_unitario,0,',','.') }}</td>
            <td class="text-right">${{ number_format($s->pivot->subtotal,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- PRODUCTOS -->
@if($orden->productos->count() > 0)
<div class="section-title">Productos / Repuestos</div>
<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th class="text-center">Cant.</th>
            <th class="text-right">P. Unitario</th>
            <th class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orden->productos as $p)
        <tr>
            <td>{{ $p->nombre }}</td>
            <td class="text-center">{{ $p->pivot->cantidad }}</td>
            <td class="text-right">${{ number_format($p->pivot->precio_unitario,0,',','.') }}</td>
            <td class="text-right">${{ number_format($p->pivot->subtotal,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- RESUMEN FINANCIERO -->
@php
    $subtotalServicios = $orden->servicios->sum('pivot.subtotal');
    $subtotalProductos = $orden->productos->sum('pivot.subtotal');
    $subtotal = $subtotalServicios + $subtotalProductos;
    $costo = $orden->costo_final ?? $orden->costo_estimado ?? $subtotal;
@endphp

<div class="section-title">Resumen Financiero</div>
<div class="totals-box">
    @if($subtotal > 0)
    <div class="total-row"><span>Subtotal:</span><span>${{ number_format($subtotal,0,',','.') }}</span></div>
    @endif
    @if($orden->costo_estimado)
    <div class="total-row"><span>Estimado:</span><span>${{ number_format($orden->costo_estimado,0,',','.') }}</span></div>
    @endif
    @if($orden->costo_final)
    <div class="total-row"><span>Final:</span><span>${{ number_format($orden->costo_final,0,',','.') }}</span></div>
    @endif
    @if($orden->anticipo)
    <div class="total-row"><span>Anticipo:</span><span>${{ number_format($orden->anticipo,0,',','.') }}</span></div>
    @endif
    @if($orden->saldo)
    <div class="total-row"><span>Saldo:</span><span>${{ number_format($orden->saldo,0,',','.') }}</span></div>
    @endif

    <div class="total-row total-final"><span>Total:</span><span>${{ number_format($costo,0,',','.') }}</span></div>
</div>

<!-- OBSERVACIONES -->
@if($orden->observaciones)
<div class="section-title">Observaciones</div>
<div class="textbox">{{ $orden->observaciones }}</div>
@endif

<!-- COMENTARIOS -->
@if($orden->comentarios->count() > 0)
<div class="section-title">Comentarios</div>
@foreach($orden->comentarios as $c)
<div class="comment">
    <div class="comment-meta">{{ $c->user->name ?? 'Sistema' }} — {{ $c->created_at->format('d/m/Y H:i') }}</div>
    {{ $c->comentario }}
</div>
@endforeach
@endif

<!-- FOOTER -->
<div class="footer">
    Documento generado el {{ now()->format('d/m/Y H:i') }} — Sistema de Servicio Técnico
</div>

</body>
</html>
