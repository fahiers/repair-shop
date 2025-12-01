<?php

namespace App\Livewire\Servicios;

use App\Models\Servicio;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        try {
            $this->resetPage();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al actualizar búsqueda en Servicios Index: '.$e->getMessage());
        }
    }

    public function render()
    {
        try {
            $searchTerm = trim($this->search);
            $searchTerm = preg_replace('/\s+/', ' ', $searchTerm);

            $servicios = Servicio::query()
                ->when($searchTerm !== '', function ($query) use ($searchTerm) {
                    $term = '%'.$searchTerm.'%';
                    $query->where(function ($q) use ($term) {
                        $q->where('nombre', 'like', $term)
                          ->orWhere('categoria', 'like', $term)
                          ->orWhere('descripcion', 'like', $term);
                    });
                })
                ->orderBy('nombre')
                ->paginate(10);

            return view('livewire.servicios.index', [
                'servicios' => $servicios,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al renderizar lista de servicios: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al cargar la lista de servicios.');
            
            return view('livewire.servicios.index', [
                'servicios' => [],
            ]);
        }
    }
}
