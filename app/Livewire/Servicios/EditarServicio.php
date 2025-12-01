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
        try {
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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al montar EditarServicio: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al cargar el servicio.');
        }
    }

    public function update()
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al actualizar servicio: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al actualizar el servicio.');
        }
    }

    public function render()
    {
        try {
            return view('livewire.servicios.editar-servicio');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al renderizar EditarServicio: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al cargar el formulario.');

            return view('livewire.servicios.editar-servicio');
        }
    }
}
