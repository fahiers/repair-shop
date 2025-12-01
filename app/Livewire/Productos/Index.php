<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
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
            \Illuminate\Support\Facades\Log::error('Error al actualizar búsqueda en Productos Index: '.$e->getMessage());
        }
    }

    public function render()
    {
        try {
            $searchTerm = trim($this->search);
            $searchTerm = preg_replace('/\s+/', ' ', $searchTerm);

            $productos = Producto::query()
                ->when($searchTerm !== '', function ($query) use ($searchTerm) {
                    $term = '%'.$searchTerm.'%';
                    $query->where(function ($q) use ($term) {
                        $q->where('nombre', 'like', $term)
                            ->orWhere('categoria', 'like', $term)
                            ->orWhere('marca', 'like', $term)
                            ->orWhere('descripcion', 'like', $term);
                    });
                })
                ->orderBy('nombre')
                ->paginate(10);

            return view('livewire.productos.index', [
                'productos' => $productos,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al renderizar lista de productos: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al cargar la lista de productos.');

            return view('livewire.productos.index', [
                'productos' => [],
            ]);
        }
    }
}
