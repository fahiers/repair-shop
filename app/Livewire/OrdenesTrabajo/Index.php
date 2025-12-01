<?php

namespace App\Livewire\OrdenesTrabajo;

use App\Models\OrdenTrabajo;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $estado = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEstado(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $searchTerm = trim($this->search);
        $searchTerm = preg_replace('/\s+/', ' ', $searchTerm);

        $ordenes = OrdenTrabajo::query()
            ->with(['dispositivo.cliente', 'dispositivo.modelo', 'tecnico'])
            ->when($searchTerm !== '', function ($query) use ($searchTerm) {
                $term = '%'.$searchTerm.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('numero_orden', 'like', $term)
                        ->orWhereHas('dispositivo', function ($qd) use ($term) {
                            $qd->where('imei', 'like', $term)
                                ->orWhereHas('cliente', function ($qc) use ($term) {
                                    $qc->where('nombre', 'like', $term);
                                })
                                ->orWhereHas('modelo', function ($qm) use ($term) {
                                    $qm->where('marca', 'like', $term)
                                        ->orWhere('modelo', 'like', $term);
                                });
                        });
                });
            })
            ->when($this->estado !== '', function ($query) {
                $query->where('estado', $this->estado);
            })
            ->orderByDesc('fecha_ingreso')
            ->paginate(10);

        return view('livewire.ordenes-trabajo.index', [
            'ordenes' => $ordenes,
        ]);
    }
}
