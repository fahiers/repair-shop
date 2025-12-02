<?php

use App\Enums\EstadoOrden;
use App\Models\OrdenPago;
use App\Models\OrdenTrabajo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $fechaInicio = '';
    public string $fechaFin = '';
    public string $metodoPago = '';
    public bool $hasError = false;
    public string $errorMessage = '';

    public function mount(): void
    {
        try {
            $this->fechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d');
            $this->fechaFin = Carbon::now()->endOfMonth()->format('Y-m-d');
            $this->hasError = false;
            $this->errorMessage = '';
        } catch (\Throwable $e) {
            Log::error('Finanzas: Error al inicializar fechas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->fechaInicio = now()->format('Y-m-d');
            $this->fechaFin = now()->format('Y-m-d');
        }
    }

    public function updating($property, $value): void
    {
        $this->resetPage();
    }

    /**
     * Valida y parsea una fecha de forma segura.
     */
    private function parseFechaSafe(string $fecha, string $tipo = 'inicio'): Carbon
    {
        try {
            $parsed = Carbon::parse($fecha);
            return $tipo === 'inicio' ? $parsed->startOfDay() : $parsed->endOfDay();
        } catch (\Throwable $e) {
            Log::warning('Finanzas: Fecha inválida, usando fecha actual', [
                'fecha_recibida' => $fecha,
                'tipo' => $tipo,
                'error' => $e->getMessage(),
            ]);
            return $tipo === 'inicio' 
                ? Carbon::now()->startOfMonth()->startOfDay() 
                : Carbon::now()->endOfMonth()->endOfDay();
        }
    }

    /**
     * Valida el método de pago.
     */
    private function getMetodoPagoValido(): string
    {
        $metodosValidos = ['', 'efectivo', 'tarjeta', 'transferencia', 'otros'];
        return in_array($this->metodoPago, $metodosValidos, true) ? $this->metodoPago : '';
    }

    /**
     * Calcula los KPIs de forma segura.
     *
     * @return array{totalIngresos: int, cuentasPorCobrar: int, ticketPromedio: float, pagosCount: int}
     */
    private function calcularKPIs(Carbon $inicio, Carbon $fin, string $metodoPago): array
    {
        $totalIngresos = 0;
        $cuentasPorCobrar = 0;
        $pagosCount = 0;
        $ticketPromedio = 0.0;

        try {
            // Calcular ingresos totales
            $pagosQuery = OrdenPago::query()
                ->whereBetween('fecha_pago', [$inicio, $fin]);
            
            if ($metodoPago !== '') {
                $pagosQuery->where('metodo_pago', $metodoPago);
            }

            $totalIngresos = (int) $pagosQuery->sum('monto');
            $pagosCount = (int) $pagosQuery->count();
            
            Log::debug('Finanzas: Ingresos calculados', [
                'inicio' => $inicio->toDateString(),
                'fin' => $fin->toDateString(),
                'metodo_pago' => $metodoPago ?: 'todos',
                'total_ingresos' => $totalIngresos,
                'pagos_count' => $pagosCount,
            ]);

        } catch (\Throwable $e) {
            Log::error('Finanzas: Error al calcular ingresos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        try {
            // Calcular cuentas por cobrar (órdenes listas o entregadas con saldo pendiente)
            $cuentasPorCobrar = (int) OrdenTrabajo::query()
                ->whereIn('estado', [EstadoOrden::Listo, EstadoOrden::Entregado])
                ->where('saldo', '>', 0)
                ->sum('saldo');

            Log::debug('Finanzas: Cuentas por cobrar calculadas', [
                'cuentas_por_cobrar' => $cuentasPorCobrar,
            ]);

        } catch (\Throwable $e) {
            Log::error('Finanzas: Error al calcular cuentas por cobrar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Calcular ticket promedio (protección contra división por cero)
        if ($pagosCount > 0) {
            $ticketPromedio = round($totalIngresos / $pagosCount, 2);
        }

        return [
            'totalIngresos' => $totalIngresos,
            'cuentasPorCobrar' => $cuentasPorCobrar,
            'ticketPromedio' => $ticketPromedio,
            'pagosCount' => $pagosCount,
        ];
    }

    /**
     * Obtiene la distribución por método de pago de forma segura.
     *
     * @return \Illuminate\Support\Collection
     */
    private function obtenerDistribucionMetodos(Carbon $inicio, Carbon $fin, string $metodoPago): \Illuminate\Support\Collection
    {
        try {
            $query = OrdenPago::query()
                ->whereBetween('fecha_pago', [$inicio, $fin]);
            
            if ($metodoPago !== '') {
                $query->where('metodo_pago', $metodoPago);
            }

            return $query
                ->select('metodo_pago', DB::raw('COALESCE(SUM(monto), 0) as total'), DB::raw('COUNT(*) as cantidad'))
                ->groupBy('metodo_pago')
                ->orderByDesc('total')
                ->get();

        } catch (\Throwable $e) {
            Log::error('Finanzas: Error al obtener distribución de métodos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return collect();
        }
    }

    /**
     * Obtiene los pagos paginados de forma segura.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function obtenerPagosPaginados(Carbon $inicio, Carbon $fin, string $metodoPago)
    {
        try {
            $query = OrdenPago::query()
                ->with([
                    'orden' => function ($q) {
                        $q->select('id', 'numero_orden', 'dispositivo_id');
                    },
                    'orden.dispositivo' => function ($q) {
                        $q->select('id', 'cliente_id');
                    },
                    'orden.dispositivo.cliente' => function ($q) {
                        $q->select('id', 'nombre');
                    },
                ])
                ->whereBetween('fecha_pago', [$inicio, $fin]);

            if ($metodoPago !== '') {
                $query->where('metodo_pago', $metodoPago);
            }

            return $query->latest('fecha_pago')->paginate(10);

        } catch (\Throwable $e) {
            Log::error('Finanzas: Error al obtener pagos paginados', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Retornar paginador vacío en caso de error
            return new \Illuminate\Pagination\LengthAwarePaginator(
                items: collect(),
                total: 0,
                perPage: 10,
                currentPage: 1
            );
        }
    }

    public function with(): array
    {
        $this->hasError = false;
        $this->errorMessage = '';

        try {
            // Validar y parsear fechas
            $inicio = $this->parseFechaSafe($this->fechaInicio, 'inicio');
            $fin = $this->parseFechaSafe($this->fechaFin, 'fin');

            // Validar que la fecha de inicio no sea mayor que la de fin
            if ($inicio->greaterThan($fin)) {
                Log::warning('Finanzas: Fecha inicio mayor que fecha fin, intercambiando', [
                    'fecha_inicio' => $this->fechaInicio,
                    'fecha_fin' => $this->fechaFin,
                ]);
                [$inicio, $fin] = [$fin, $inicio];
            }

            // Validar método de pago
            $metodoPago = $this->getMetodoPagoValido();

            // Calcular KPIs
            $kpis = $this->calcularKPIs($inicio, $fin, $metodoPago);

            // Obtener distribución por método
            $distribucionMetodos = $this->obtenerDistribucionMetodos($inicio, $fin, $metodoPago);

            // Obtener pagos paginados
            $pagos = $this->obtenerPagosPaginados($inicio, $fin, $metodoPago);

            Log::info('Finanzas: Datos cargados exitosamente', [
                'periodo' => $inicio->toDateString() . ' - ' . $fin->toDateString(),
                'metodo_filtro' => $metodoPago ?: 'todos',
                'total_pagos' => $pagos->total(),
            ]);

            return [
                'pagos' => $pagos,
                'totalIngresos' => $kpis['totalIngresos'],
                'cuentasPorCobrar' => $kpis['cuentasPorCobrar'],
                'ticketPromedio' => $kpis['ticketPromedio'],
                'distribucionMetodos' => $distribucionMetodos,
            ];

        } catch (\Throwable $e) {
            $this->hasError = true;
            $this->errorMessage = 'Ocurrió un error al cargar los datos financieros.';
            
            Log::error('Finanzas: Error general al cargar datos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'fecha_inicio' => $this->fechaInicio,
                'fecha_fin' => $this->fechaFin,
                'metodo_pago' => $this->metodoPago,
            ]);

            // Retornar datos vacíos en caso de error
            return [
                'pagos' => new \Illuminate\Pagination\LengthAwarePaginator(
                    items: collect(),
                    total: 0,
                    perPage: 10,
                    currentPage: 1
                ),
                'totalIngresos' => 0,
                'cuentasPorCobrar' => 0,
                'ticketPromedio' => 0,
                'distribucionMetodos' => collect(),
            ];
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    {{-- Banner de Error --}}
    @if($hasError)
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl dark:bg-red-900/20 dark:border-red-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400">
                    <flux:icon.exclamation-triangle class="size-6" />
                </div>
                <div>
                    <p class="font-medium text-red-800 dark:text-red-200">{{ $errorMessage }}</p>
                    <p class="text-sm text-red-600 dark:text-red-400">Por favor, intenta recargar la página o contacta al administrador si el problema persiste.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <flux:heading size="xl">Finanzas</flux:heading>
        
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <flux:input type="date" wire:model.live="fechaInicio" label="Desde" />
            <flux:input type="date" wire:model.live="fechaFin" label="Hasta" />
            
            <flux:select wire:model.live="metodoPago" placeholder="Todos los métodos" label="Método">
                <flux:select.option value="">Todos</flux:select.option>
                <flux:select.option value="efectivo">Efectivo</flux:select.option>
                <flux:select.option value="tarjeta">Tarjeta</flux:select.option>
                <flux:select.option value="transferencia">Transferencia</flux:select.option>
                <flux:select.option value="otros">Otros</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="flex flex-col gap-2 p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Ingresos Totales</flux:heading>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                    <flux:icon.banknotes class="size-6" />
                </div>
            </div>
            <div class="text-3xl font-bold tabular-nums text-gray-900 dark:text-white" wire:loading.class="opacity-50">
                {{ Number::currency($totalIngresos ?? 0, precision: 0) }}
            </div>
            <flux:subheading>En el periodo seleccionado</flux:subheading>
        </div>

        <div class="flex flex-col gap-2 p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Por Cobrar</flux:heading>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                    <flux:icon.exclamation-circle class="size-6" />
                </div>
            </div>
            <div class="text-3xl font-bold tabular-nums text-gray-900 dark:text-white" wire:loading.class="opacity-50">
                {{ Number::currency($cuentasPorCobrar ?? 0, precision: 0) }}
            </div>
            <flux:subheading>Saldo en órdenes listas/entregadas</flux:subheading>
        </div>

        <div class="flex flex-col gap-2 p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Ticket Promedio</flux:heading>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <flux:icon.chart-bar class="size-6" />
                </div>
            </div>
            <div class="text-3xl font-bold tabular-nums text-gray-900 dark:text-white" wire:loading.class="opacity-50">
                {{ Number::currency($ticketPromedio ?? 0, precision: 0) }}
            </div>
            <flux:subheading>Promedio por transacción</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Tabla de Pagos --}}
        <div class="lg:col-span-2 flex flex-col gap-4">
            <div class="p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">Movimientos</flux:heading>
                    <div wire:loading class="text-sm text-zinc-500">
                        <flux:icon.arrow-path class="size-4 animate-spin inline-block mr-1" />
                        Cargando...
                    </div>
                </div>

                <div class="overflow-x-auto" wire:loading.class="opacity-50">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Orden</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Método</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                            @forelse ($pagos ?? [] as $pago)
                                <tr wire:key="pago-{{ $pago->id }}">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                        {{ $pago->fecha_pago?->format('d/m/Y') ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        @if($pago->orden)
                                            <a href="{{ route('ordenes-trabajo.editar', $pago->orden_id) }}" class="font-medium text-indigo-600 hover:text-indigo-500" wire:navigate>
                                                #{{ $pago->orden->numero_orden ?? 'N/A' }}
                                            </a>
                                        @else
                                            <span class="text-zinc-400">Sin orden</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                        {{ $pago->orden?->dispositivo?->cliente?->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                        <flux:badge size="sm" color="zinc" inset="top bottom">
                                            {{ ucfirst($pago->metodo_pago ?? 'N/A') }}
                                        </flux:badge>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium tabular-nums text-zinc-900 dark:text-zinc-100">
                                        {{ Number::currency($pago->monto ?? 0, precision: 0) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-500">
                                        <div class="flex flex-col items-center gap-2">
                                            <flux:icon.inbox class="size-8 text-zinc-400" />
                                            <p>No hay movimientos en este periodo.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($pagos) && $pagos->hasPages())
                    <div class="mt-4">
                        {{ $pagos->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Resumen por Método --}}
        <div class="flex flex-col gap-4">
            <div class="p-6 bg-white border rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="lg" class="mb-4">Por Método de Pago</flux:heading>
                
                <div class="space-y-4" wire:loading.class="opacity-50">
                    @forelse ($distribucionMetodos ?? [] as $metodo)
                        <div wire:key="metodo-{{ $metodo->metodo_pago ?? 'unknown' }}">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                                    {{ $metodo->metodo_pago ?? 'Desconocido' }}
                                </span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                    {{ Number::currency($metodo->total ?? 0, precision: 0) }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                @php
                                    $total = $metodo->total ?? 0;
                                    $ingresos = $totalIngresos ?? 0;
                                    $percentage = ($ingresos > 0 && $total > 0) ? min(100, ($total / $ingresos) * 100) : 0;
                                @endphp
                                <div 
                                    class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                                    style="width: {{ number_format($percentage, 2) }}%"
                                ></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1 text-right tabular-nums">
                                {{ number_format($metodo->cantidad ?? 0) }} transacciones
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 text-center py-6">
                            <div class="flex flex-col items-center gap-2">
                                <flux:icon.chart-pie class="size-8 text-zinc-400" />
                                <p>Sin datos en este periodo</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
