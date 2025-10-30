<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;

class EditarCliente extends Component
{
    public Cliente $cliente;

    public ?string $nombre = '';

    public ?string $telefono = '';

    public ?string $email = '';

    public ?string $direccion = '';

    public ?string $rut = '';

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;
        $this->nombre = $cliente->nombre ?? '';
        $this->telefono = $cliente->telefono ?? '';
        $this->email = $cliente->email ?? '';
        $this->direccion = $cliente->direccion ?? '';
        $this->rut = $cliente->rut ?? '';
    }

    public function update()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clientes,email,'.$this->cliente->id,
            'direccion' => 'required|string|max:255',
            'rut' => 'required|string|max:255|unique:clientes,rut,'.$this->cliente->id,
        ]);

        $this->cliente->update([
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'rut' => $this->rut,
        ]);

        return $this->redirectRoute('clientes.index');
    }

    public function render()
    {
        return view('livewire.clientes.editar-cliente');
    }
}
