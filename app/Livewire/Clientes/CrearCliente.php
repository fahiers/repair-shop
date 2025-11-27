<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;

class CrearCliente extends Component
{
    public string $nombre = '';

    public string $telefono = '';

    public string $email = '';

    public string $direccion = '';

    public string $rut = '';

    public ?string $notas = '';

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clientes',
            'direccion' => 'required|string|max:255',
            'rut' => 'required|string|max:255|unique:clientes',
            'notas' => 'nullable|string|max:255',
        ]);

        Cliente::create([
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'rut' => $this->rut,
            'notas' => $this->notas,
        ]);

        return $this->redirectRoute('clientes.index');
    }

    public function render()
    {
        return view('livewire.clientes.crear-cliente');
    }
}
