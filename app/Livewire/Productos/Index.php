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
        $this->resetPage();
    }

    public function render()
    {
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
    }
}
