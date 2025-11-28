<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;

class CrearCliente extends Component
{
    public string $nombre = '';

    public ?string $telefono = '';

    public ?string $email = '';

    public ?string $direccion = '';

    public ?string $rut = '';

    public ?string $notas = '';

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clientes',
            'direccion' => 'nullable|string|max:255',
            'rut' => ['nullable', 'string', 'max:255', 'unique:clientes', 'cl_rut'],
            'notas' => 'nullable|string|max:255',
        ];
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'rut' && !empty($this->rut)) {
            $this->validateOnly($propertyName);
        }
    }

    public function save()
    {
        $this->validate();

        Cliente::create([
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'rut' => $this->rut ? $this->normalizarRut($this->rut) : null,
            'notas' => $this->notas,
        ]);

        return $this->redirectRoute('clientes.index');
    }

    public function messages(): array
    {
        return [
            'rut.required' => 'El RUT es obligatorio',
            'rut.unique' => 'Este RUT ya está registrado',
            'rut.cl_rut' => 'El campo RUT no es un RUT chileno válido.',
        ];
    }

    /**
     * Normaliza el RUT al formato estándar sin puntos: 12345678-9
     */
    private function normalizarRut(?string $rut): ?string
    {
        if (empty($rut)) {
            return null;
        }

        // Limpiar y obtener solo números y K
        $rut = preg_replace('/[^0-9kK]/', '', strtoupper($rut));

        if (strlen($rut) < 2) {
            return $rut;
        }

        // Separar cuerpo y dígito verificador
        $cuerpo = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        // Retornar formato estándar: 12345678-9
        return $cuerpo . '-' . $dv;
    }

    public function render()
    {
        return view('livewire.clientes.crear-cliente');
    }
}
