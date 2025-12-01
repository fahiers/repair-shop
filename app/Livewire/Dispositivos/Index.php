<?php

namespace App\Livewire\Dispositivos;

use App\Enums\EstadoOrden;
use App\Models\Dispositivo;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filterLock = '';

    public $activeTab = 'en_taller'; // 'en_taller' | 'historial'

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLock()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $searchTerm = trim($this->search);
        $searchTerm = preg_replace('/\s+/', ' ', $searchTerm);

        $dispositivos = Dispositivo::query()
            ->with(['cliente', 'modelo'])
            ->when($this->activeTab === 'en_taller', function ($query) {
                // Solo dispositivos con órdenes abiertas (Pendiente, Diagnostico, EnReparacion, Listo)
                $query->whereHas('ordenes', function ($q) {
                    $q->whereIn('estado', [
                        EstadoOrden::Pendiente->value,
                        EstadoOrden::Diagnostico->value,
                        EstadoOrden::EnReparacion->value,
                        EstadoOrden::Listo->value,
                    ]);
                });
            })
            ->when($this->activeTab === 'en_taller', function ($query) {
                // Cargar la orden activa más reciente
                $query->with(['ordenes' => function ($q) {
                    $q->whereIn('estado', [
                        EstadoOrden::Pendiente->value,
                        EstadoOrden::Diagnostico->value,
                        EstadoOrden::EnReparacion->value,
                        EstadoOrden::Listo->value,
                    ])
                        ->latest()
                        ->limit(1);
                }]);
            })
            ->when($searchTerm !== '', function ($query) use ($searchTerm) {
                $term = '%'.$searchTerm.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('imei', 'like', $term)
                        ->orWhereHas('modelo', function ($modeloQuery) use ($term) {
                            $modeloQuery->where('modelo', 'like', $term)
                                ->orWhere('marca', 'like', $term);
                        })
                        ->orWhereHas('cliente', function ($clienteQuery) use ($term) {
                            $clienteQuery->where('nombre', 'like', $term);
                        });
                });
            })
            ->when($this->filterLock, function ($query) {
                if ($this->filterLock === 'patron') {
                    $query->where(function ($q) {
                        $q->whereNotNull('patron')->orWhereNotNull('pattern_encrypted');
                    });
                } elseif ($this->filterLock === 'contrasena') {
                    $query->whereNotNull('contraseña');
                } elseif ($this->filterLock === 'ninguno') {
                    $query->whereNull('patron')
                        ->whereNull('pattern_encrypted')
                        ->whereNull('contraseña');
                }
            })
            ->latest()
            ->paginate(10);

        return view('livewire.dispositivos.index', [
            'dispositivos' => $dispositivos,
        ]);
    }
}
