<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function delete(Cliente $cliente)
    {
        $cliente->delete();
    }

    public function render()
    {
        return view('livewire.clientes.index', [
            'clientes' => Cliente::paginate(10),
        ]);
    }
}
