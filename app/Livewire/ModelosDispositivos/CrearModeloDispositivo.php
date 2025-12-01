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

    protected $messages = [
        'marca.required' => 'El campo marca es obligatorio.',
        'marca.string' => 'El campo marca debe ser texto.',
        'marca.max' => 'El campo marca no puede tener más de 100 caracteres.',
        'modelo.required' => 'El campo modelo es obligatorio.',
        'modelo.string' => 'El campo modelo debe ser texto.',
        'modelo.max' => 'El campo modelo no puede tener más de 100 caracteres.',
        'descripcion.string' => 'El campo descripción debe ser texto.',
        'anio.integer' => 'El campo año debe ser un número entero.',
        'anio.min' => 'El campo año debe ser mayor o igual a 1900.',
        'anio.max' => 'El campo año debe ser menor o igual a 2030.',
    ];

    public function save()
    {
        try {
            $this->validate();

            ModeloDispositivo::create([
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'descripcion' => $this->descripcion,
                'anio' => $this->anio,
            ]);

            return redirect()->route('modelos.index')->with('success', 'Modelo de dispositivo creado correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al crear modelo de dispositivo: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al crear el modelo.');
        }
    }

    public function render()
    {
        try {
            return view('livewire.modelos-dispositivos.crear-modelo-dispositivo');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al renderizar CrearModeloDispositivo: '.$e->getMessage());
            session()->flash('error', 'Ocurrió un error al cargar el formulario.');
            
            return view('livewire.modelos-dispositivos.crear-modelo-dispositivo');
        }
    }
}
