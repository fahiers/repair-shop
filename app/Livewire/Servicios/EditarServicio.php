<?php

namespace App\Livewire\Servicios;

use App\Models\Servicio;
use Livewire\Component;

class EditarServicio extends Component
{
    public int $servicioId;

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

    public function mount(int $id): void
    {
        $this->servicioId = $id;

        $servicio = Servicio::find($id);
        if (! $servicio) {
            return;
        }

        $this->nombre = $servicio->nombre;
        $this->descripcion = $servicio->descripcion;
        $this->precio_base = $servicio->precio_base;
        $this->categoria = $servicio->categoria;
        $this->estado = $servicio->estado ?? 'activo';
    }

    public function update()
    {
        $this->validate();

        $servicio = Servicio::findOrFail($this->servicioId);
        $servicio->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio_base' => $this->precio_base,
            'categoria' => $this->categoria,
            'estado' => $this->estado,
        ]);

        return redirect()->route('servicios.index')->with('success', 'Servicio actualizado correctamente.');
    }

    public function render()
    {
        return view('livewire.servicios.editar-servicio');
    }
}
