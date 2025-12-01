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
        if ($propertyName === 'rut' && ! empty($this->rut)) {
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
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser texto.',
            'nombre.max' => 'El campo nombre no puede tener más de 255 caracteres.',
            'telefono.string' => 'El campo teléfono debe ser texto.',
            'telefono.max' => 'El campo teléfono no puede tener más de 255 caracteres.',
            'email.email' => 'El campo email debe ser una dirección de correo válida.',
            'email.max' => 'El campo email no puede tener más de 255 caracteres.',
            'email.unique' => 'Este email ya está registrado.',
            'direccion.string' => 'El campo dirección debe ser texto.',
            'direccion.max' => 'El campo dirección no puede tener más de 255 caracteres.',
            'rut.required' => 'El RUT es obligatorio',
            'rut.string' => 'El campo RUT debe ser texto.',
            'rut.max' => 'El campo RUT no puede tener más de 255 caracteres.',
            'rut.unique' => 'Este RUT ya está registrado',
            'rut.cl_rut' => 'El campo RUT no es un RUT chileno válido.',
            'notas.string' => 'El campo notas debe ser texto.',
            'notas.max' => 'El campo notas no puede tener más de 255 caracteres.',
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
        return $cuerpo.'-'.$dv;
    }

    public function render()
    {
        return view('livewire.clientes.crear-cliente');
    }
}
