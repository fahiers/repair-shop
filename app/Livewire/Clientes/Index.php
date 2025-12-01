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
        $searchTerm = trim($this->search);
        $searchTerm = preg_replace('/\s+/', ' ', $searchTerm);

        $clientes = Cliente::query()
            ->when($searchTerm !== '', function ($query) use ($searchTerm) {
                $term = '%'.$searchTerm.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('nombre', 'like', $term)
                        ->orWhere('telefono', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('rut', 'like', $term);
                });
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.clientes.index', [
            'clientes' => $clientes,
        ]);
    }
}
