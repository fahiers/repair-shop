<div class="flex flex-col gap-6">
    <!-- Tarjetas Superiores -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="flex flex-col gap-2 p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Por Asignar</flux:heading>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400">
                    <flux:icon.user class="size-6" />
                </div>
            </div>
            <div class="text-3xl font-bold tabular-nums">{{ $porAsignar }}</div>
            <flux:subheading>Órdenes pendientes</flux:subheading>
        </div>

        <div class="flex flex-col gap-2 p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">En Proceso</flux:heading>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <flux:icon.clock class="size-6" />
                </div>
            </div>
            <div class="text-3xl font-bold tabular-nums">{{ $enProceso }}</div>
            <flux:subheading>Diagnóstico o Reparación</flux:subheading>
        </div>

        <div class="flex flex-col gap-2 p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Por Entregar</flux:heading>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                    <flux:icon.shopping-bag class="size-6" />
                </div>
            </div>
            <div class="text-3xl font-bold tabular-nums">{{ $porEntregar }}</div>
            <flux:subheading>Listas para entrega</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Gráfico de Distribución -->
        <div class="p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 lg:col-span-2">
            <flux:heading size="lg" class="mb-4">Distribución de Órdenes</flux:heading>
            <div 
                class="relative h-64 w-full" 
                x-data="{ 
                    initChart() {
                        if (typeof Chart === 'undefined') {
                            const script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                            script.onload = () => this.renderChart();
                            document.head.appendChild(script);
                        } else {
                            this.renderChart();
                        }
                    },
                    renderChart() {
                        const ctx = this.$refs.canvas.getContext('2d');
                        // Destruir gráfico previo si existe para evitar superposición en re-render
                        if (this.chart) this.chart.destroy();

                        this.chart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: @js($chartConfig['labels']),
                                datasets: [{
                                    data: @js($chartConfig['data']),
                                    backgroundColor: @js($chartConfig['colors']),
                                    borderWidth: 0,
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'right',
                                        labels: {
                                            usePointStyle: true,
                                            font: { family: 'Inter' }
                                        }
                                    }
                                },
                                cutout: '60%'
                            }
                        });
                    }
                }"
                x-init="initChart()"
            >
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="flex flex-col justify-center gap-6 p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-center">
                <flux:subheading>Ventas (Mes Actual)</flux:subheading>
                <div class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                    {{ Number::currency($ventasTotales, precision: 0) }}
                </div>
            </div>

            <flux:separator />

            <div class="text-center">
                <flux:subheading>Finalizadas</flux:subheading>
                <div class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                    {{ $ordenesFinalizadas }}
                </div>
                <flux:subheading class="mt-1 text-xs">Órdenes entregadas este mes</flux:subheading>
            </div>
            
            <div class="mt-auto">
                <flux:button variant="primary" class="w-full justify-center" href="{{ route('ordenes-trabajo.index') }}">Ver todas las Órdenes</flux:button>
            </div>
        </div>
    </div>

    <!-- Tabla de Vencimientos -->
    <div class="p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4 flex items-center justify-between">
            <flux:heading size="lg">Vencimientos de la Semana</flux:heading>
            <flux:button size="sm" icon="arrow-right" variant="ghost" href="{{ route('ordenes-trabajo.index') }}">Ver todas</flux:button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                    <tr>
                        <th class="pb-3 font-medium">Orden</th>
                        <th class="pb-3 font-medium">Fecha Límite</th>
                        <th class="pb-3 font-medium">Cliente</th>
                        <th class="pb-3 font-medium">Dispositivo</th>
                        <th class="pb-3 font-medium">Estado</th>
                        <th class="pb-3 font-medium">Prioridad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($proximosVencimientos as $orden)
                        <tr>
                            <td class="py-3 font-medium">#{{ $orden->numero_orden }}</td>
                            <td class="py-3">
                                <span class="{{ $orden->fecha_entrega_estimada->isPast() ? 'text-red-600 font-medium' : '' }}">
                                    {{ $orden->fecha_entrega_estimada->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-3">{{ $orden->dispositivo->cliente->nombre ?? 'Cliente' }}</td>
                            <td class="py-3">
                                <div class="flex flex-col">
                                    <span class="text-sm">{{ $orden->dispositivo->modelo->marca ?? '' }} {{ $orden->dispositivo->modelo->modelo ?? '' }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                <flux:badge size="sm" inset="top bottom" class="{{ $orden->estado->clasesColor() }}">
                                    {{ $orden->estado->etiqueta() }}
                                </flux:badge>
                            </td>
                            <td class="py-3">
                               <flux:badge size="sm" color="zinc">Normal</flux:badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-3 text-center text-gray-500">
                                No hay vencimientos para esta semana.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
