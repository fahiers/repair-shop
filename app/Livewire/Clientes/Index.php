<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
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

    public function delete(Cliente $cliente)
    {
        $cliente->delete();
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('nombre', 'like', $term)
                        ->orWhere('telefono', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('rut', 'like', $term)
                        ->orWhere('direccion', 'like', $term);
                });
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.clientes.index', [
            'clientes' => $clientes,
        ]);
    }
}
