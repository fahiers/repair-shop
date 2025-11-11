<?php

namespace App\Livewire\ModelosDispositivos;

use App\Models\ModeloDispositivo;
use Livewire\Component;

class CrearModeloDispositivo extends Component
{
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

    public function save()
    {
        $this->validate();

        ModeloDispositivo::create([
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'descripcion' => $this->descripcion,
            'anio' => $this->anio,
        ]);

        return redirect()->route('modelos.index')->with('success', 'Modelo de dispositivo creado correctamente.');
    }

    public function render()
    {
        return view('livewire.modelos-dispositivos.crear-modelo-dispositivo');
    }
}
