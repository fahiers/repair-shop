<?php

namespace App\Livewire\Servicios;

use App\Models\Servicio;
use Livewire\Component;

class CrearServicio extends Component
{
    public $nombre;
    public $descripcion;
    public $precio_base;
    public $categoria;
    public $estado = 'activo';

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'precio_base' => 'required|numeric|min:0',
        'categoria' => 'nullable|string|max:255',
        'estado' => 'required|in:activo,inactivo',
    ];

    public function save()
    {
        $this->validate();

        Servicio::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio_base' => $this->precio_base,
            'categoria' => $this->categoria,
            'estado' => $this->estado,
        ]);

        return redirect()->route('servicios.index')->with('success', 'Servicio creado correctamente.');
    }

    public function render()
    {
        return view('livewire.servicios.crear-servicio');
    }
}
