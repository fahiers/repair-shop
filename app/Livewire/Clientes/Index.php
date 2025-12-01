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
        try {
            $cliente->delete();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al eliminar cliente: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al eliminar el cliente.');
        }
    }

    public function render()
    {
        try {
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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al renderizar lista de clientes: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al cargar la lista de clientes.');

            return view('livewire.clientes.index', [
                'clientes' => [],
            ]);
        }
    }
}
