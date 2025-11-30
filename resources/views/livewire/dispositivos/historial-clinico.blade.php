<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <flux:button icon="arrow-left" variant="ghost" class="h-10 w-10 !rounded-full !p-0" href="{{ route('dispositivos.index') }}" />
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Historial Clínico del Dispositivo</h1>
    </div>

    <!-- Información del Dispositivo -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4 flex items-center gap-2">
            <flux:icon name="device-phone-mobile" class="w-5 h-5" />
            Información del Equipo
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Modelo y Cliente -->
            <div class="space-y-4">
                <div>
                    <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">Modelo</flux:label>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mt-1">
                        {{ $dispositivo->modelo->marca ?? 'N/A' }} {{ $dispositivo->modelo->modelo ?? 'Desconocido' }}
                    </p>
                </div>
                
                <div>
                    <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">Cliente</flux:label>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mt-1">
                        {{ $dispositivo->cliente->nombre ?? 'Sin Cliente' }}
                    </p>
                </div>
            </div>

            <!-- IMEI y Color -->
            <div class="space-y-4">
                <div>
                    <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">IMEI / Serie</flux:label>
                    <p class="text-sm font-mono text-zinc-900 dark:text-zinc-100 mt-1">
                        {{ $dispositivo->imei ?? '-' }}
                    </p>
                </div>
                
                <div>
                    <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">Color</flux:label>
                    <p class="text-sm text-zinc-900 dark:text-zinc-100 mt-1">
                        {{ $dispositivo->color ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Seguridad -->
        <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
            <flux:label class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">Seguridad</flux:label>
            <div>
                @if ($dispositivo->patron || $dispositivo->pattern_encrypted)
                    <flux:badge color="indigo" size="sm" icon="squares-2x2">Patrón</flux:badge>
                @elseif ($dispositivo->contraseña)
                    <flux:badge color="emerald" size="sm" icon="lock-closed">PIN/Contraseña</flux:badge>
                @else
                    <flux:badge color="zinc" size="sm" icon="lock-open">Libre</flux:badge>
                @endif
            </div>
        </div>

        <!-- Accesorios -->
        @if ($dispositivo->accesorios && count(array_filter($dispositivo->accesorios)))
            <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <flux:label class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">Accesorios Recibidos</flux:label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($dispositivo->accesorios as $clave => $presente)
                        @if ($presente)
                            <flux:badge color="zinc" size="sm">{{ ucfirst(str_replace('_', ' ', $clave)) }}</flux:badge>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Estado Físico -->
        @if ($dispositivo->estado_dispositivo)
            <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <flux:label class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">Estado Físico / Detalles</flux:label>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $dispositivo->estado_dispositivo }}</p>
            </div>
        @endif
    </div>

    <!-- Historial de Órdenes -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4 flex items-center gap-2">
            <flux:icon name="document-text" class="w-5 h-5" />
            Historial de Reparaciones
            <span class="text-sm font-normal text-zinc-500 dark:text-zinc-400">
                ({{ $dispositivo->ordenes->count() }} {{ $dispositivo->ordenes->count() === 1 ? 'orden' : 'órdenes' }})
            </span>
        </h2>

        @forelse ($dispositivo->ordenes as $orden)
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg p-6 mb-4 last:mb-0 {{ $loop->first ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                <!-- Header de la Orden -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $orden->numero_orden }}
                            </h3>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $orden->estado->clasesColor() }}">
                                {{ $orden->estado->etiqueta() }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-zinc-600 dark:text-zinc-400">
                            <span>
                                <flux:icon name="calendar" class="w-4 h-4 inline mr-1" />
                                Ingreso: {{ $orden->fecha_ingreso->format('d/m/Y') }}
                            </span>
                            @if ($orden->fecha_entrega_estimada)
                                <span>
                                    <flux:icon name="clock" class="w-4 h-4 inline mr-1" />
                                    Estimada: {{ $orden->fecha_entrega_estimada->format('d/m/Y') }}
                                </span>
                            @endif
                            @if ($orden->fecha_entrega_real)
                                <span>
                                    <flux:icon name="check-circle" class="w-4 h-4 inline mr-1" />
                                    Entregada: {{ $orden->fecha_entrega_real->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Total</div>
                        <div class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ Number::currency($orden->costo_total ?? 0, precision: 0) }}
                        </div>
                    </div>
                </div>

                <!-- Problema y Diagnóstico -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <flux:label class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Problema Reportado</flux:label>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $orden->problema_reportado }}</p>
                    </div>
                    @if ($orden->diagnostico)
                        <div>
                            <flux:label class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Diagnóstico</flux:label>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $orden->diagnostico }}</p>
                        </div>
                    @endif
                </div>

                <!-- Técnico -->
                @if ($orden->tecnico)
                    <div class="mb-4">
                        <flux:label class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Técnico Asignado</flux:label>
                        <p class="text-sm text-zinc-900 dark:text-zinc-100">{{ $orden->tecnico->name }}</p>
                    </div>
                @endif

                <!-- Servicios y Productos -->
                @if ($orden->servicios->count() > 0 || $orden->productos->count() > 0)
                    <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:label class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">Servicios y Productos</flux:label>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400">Item</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400">Cantidad</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400">Precio Unit.</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    @foreach ($orden->servicios as $servicio)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ $servicio->nombre }}
                                                @if ($servicio->pivot->descripcion)
                                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $servicio->pivot->descripcion }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-right text-zinc-700 dark:text-zinc-300">{{ $servicio->pivot->cantidad }}</td>
                                            <td class="px-4 py-2 text-sm text-right text-zinc-700 dark:text-zinc-300">{{ Number::currency($servicio->pivot->precio_unitario ?? 0, precision: 0) }}</td>
                                            <td class="px-4 py-2 text-sm text-right font-medium text-zinc-900 dark:text-zinc-100">{{ Number::currency($servicio->pivot->subtotal ?? 0, precision: 0) }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach ($orden->productos as $producto)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-100">{{ $producto->nombre }}</td>
                                            <td class="px-4 py-2 text-sm text-right text-zinc-700 dark:text-zinc-300">{{ $producto->pivot->cantidad }}</td>
                                            <td class="px-4 py-2 text-sm text-right text-zinc-700 dark:text-zinc-300">{{ Number::currency($producto->pivot->precio_unitario ?? 0, precision: 0) }}</td>
                                            <td class="px-4 py-2 text-sm text-right font-medium text-zinc-900 dark:text-zinc-100">{{ Number::currency($producto->pivot->subtotal ?? 0, precision: 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Resumen Financiero -->
                <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">Subtotal</flux:label>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ Number::currency($orden->subtotal ?? 0, precision: 0) }}</p>
                        </div>
                        <div>
                            <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">IVA</flux:label>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ Number::currency($orden->monto_iva ?? 0, precision: 0) }}</p>
                        </div>
                        <div>
                            <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">Anticipo</flux:label>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ Number::currency($orden->anticipo ?? 0, precision: 0) }}</p>
                        </div>
                        <div>
                            <flux:label class="text-xs text-zinc-500 dark:text-zinc-400">Saldo</flux:label>
                            <p class="font-medium {{ ($orden->saldo ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                                {{ Number::currency($orden->saldo ?? 0, precision: 0) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                @if ($orden->observaciones)
                    <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:label class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Observaciones</flux:label>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $orden->observaciones }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12">
                <flux:icon name="document-text" class="w-12 h-12 mx-auto mb-3 opacity-20 text-zinc-400" />
                <p class="text-zinc-500 dark:text-zinc-400">Este dispositivo no tiene órdenes de trabajo registradas.</p>
            </div>
        @endforelse
    </div>
</div>

