<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use App\Models\Cliente;

class CrearCliente extends Component
{
    public string $nombre = '';
    public string $telefono = '';
    public string $email = '';
    public string $direccion = '';
    public string $rut = '';

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clientes',
            'direccion' => 'required|string|max:255',
            'rut' => 'required|string|max:255|unique:clientes',
        ]);

        Cliente::create([
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
        return view('livewire.clientes.crear-cliente');
    }
}
