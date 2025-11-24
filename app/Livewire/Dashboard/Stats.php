<?php

namespace App\Livewire\Dashboard;

use App\Enums\EstadoOrden;
use App\Models\OrdenTrabajo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Stats extends Component
{
    public function render()
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Contadores Principales
        $porAsignar = OrdenTrabajo::where('estado', EstadoOrden::Pendiente)->count();

        $enProceso = OrdenTrabajo::whereIn('estado', [
            EstadoOrden::Diagnostico,
            EstadoOrden::EnReparacion,
        ])->count();

        $porEntregar = OrdenTrabajo::where('estado', EstadoOrden::Listo)->count();

        // Finanzas (Mes Actual)
        $ordenesFinalizadas = OrdenTrabajo::where('estado', EstadoOrden::Entregado)
            ->whereBetween('fecha_entrega_real', [$startOfMonth, $endOfMonth])
            ->count();

        $ventasTotales = OrdenTrabajo::where('estado', EstadoOrden::Entregado)
            ->whereBetween('fecha_entrega_real', [$startOfMonth, $endOfMonth])
            ->sum('costo_final');

        // Próximos Vencimientos (Esta semana)
        $proximosVencimientos = OrdenTrabajo::query()
            ->whereNotIn('estado', [EstadoOrden::Entregado, EstadoOrden::Cancelado])
            ->whereBetween('fecha_entrega_estimada', [$startOfWeek, $endOfWeek])
            ->orderBy('fecha_entrega_estimada')
            ->with(['dispositivo.cliente', 'dispositivo.modelo', 'tecnico'])
            ->take(5)
            ->get();

        // Datos para el Gráfico (Distribución total)
        $distribucionEstados = OrdenTrabajo::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->estado->value => $item->total];
            });

        $chartLabels = [];
        $chartData = [];
        $chartColors = [];

        foreach (EstadoOrden::cases() as $estado) {
            $chartLabels[] = $estado->etiqueta();
            $chartData[] = $distribucionEstados[$estado->value] ?? 0;

            $chartColors[] = match ($estado) {
                EstadoOrden::Pendiente => '#f59e0b',
                EstadoOrden::Diagnostico => '#3b82f6',
                EstadoOrden::EnReparacion => '#f97316',
                EstadoOrden::Listo => '#22c55e',
                EstadoOrden::Entregado => '#10b981',
                EstadoOrden::Cancelado => '#71717a',
            };
        }

        return view('livewire.dashboard.stats', [
            'porAsignar' => $porAsignar,
            'enProceso' => $enProceso,
            'porEntregar' => $porEntregar,
            'ordenesFinalizadas' => $ordenesFinalizadas,
            'ventasTotales' => $ventasTotales,
            'proximosVencimientos' => $proximosVencimientos,
            'chartConfig' => [
                'labels' => $chartLabels,
                'data' => $chartData,
                'colors' => $chartColors,
            ],
        ]);
    }
}
