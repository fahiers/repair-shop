<?php

namespace App\Livewire\ModelosDispositivos;

use App\Models\ModeloDispositivo;
use Livewire\Component;

class EditarModeloDispositivo extends Component
{
    public int $modeloId;

    public $marca;
    public $modelo;
    public $descripcion;
    public $anio;

    protected $rules = [
        'marca' => 'required|string|max:100',
        'modelo' => 'required|string|max:100',
        'descripcion' => 'nullable|string',
        'anio' => 'nullable|integer|min:1900|max:2030',
    ];

    public function mount(int $id): void
    {
        $this->modeloId = $id;

        $modelo = ModeloDispositivo::find($id);
        if (! $modelo) {
            return;
        }

        $this->marca = $modelo->marca;
        $this->modelo = $modelo->modelo;
        $this->descripcion = $modelo->descripcion;
        $this->anio = $modelo->anio;
    }

    public function update()
    {
        $this->validate();

        $modelo = ModeloDispositivo::findOrFail($this->modeloId);
        $modelo->update([
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'descripcion' => $this->descripcion,
            'anio' => $this->anio,
        ]);

        return redirect()->route('modelos.index')->with('success', 'Modelo de dispositivo actualizado correctamente.');
    }

    public function render()
    {
        return view('livewire.modelos-dispositivos.editar-modelo-dispositivo');
    }
}
