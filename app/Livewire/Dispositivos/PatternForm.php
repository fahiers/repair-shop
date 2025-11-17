<?php

namespace App\Livewire\Dispositivos;

use App\Models\Dispositivo;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;
use Livewire\Component;

class PatternForm extends Component
{
    public Dispositivo $dispositivo;

    /** Patrón en texto plano: "1-2-5-8-9" */
    public string $pattern = '';

    /** Contraseña del dispositivo */
    public ?string $password = null;

    /** Clave de actualización para forzar re-render del componente Alpine */
    public int $refreshKey = 0;

    public function mount(Dispositivo $dispositivo): void
    {
        $this->dispositivo = $dispositivo;
        $this->cargarBloqueo();
    }

    protected function cargarBloqueo(): void
    {
        // Refrescar el dispositivo desde la base de datos
        $this->dispositivo->refresh();

        if ($this->dispositivo->pattern_encrypted) {
            try {
                $this->pattern = Crypt::decryptString($this->dispositivo->pattern_encrypted);
            } catch (\Exception $e) {
                // Si por alguna razón falla la desencriptación
                $this->pattern = '';
            }
            $this->password = null;
        } elseif ($this->dispositivo->contraseña) {
            $this->password = $this->dispositivo->contraseña;
            $this->pattern = '';
        } else {
            $this->pattern = '';
            $this->password = null;
        }
    }

    #[On('patronActualizado')]
    public function refrescarPatron(): void
    {
        $this->cargarBloqueo();
        // Incrementar refreshKey para forzar la recreación del componente Alpine
        $this->refreshKey++;
    }

    public function save(): void
    {
        // Validar que el patrón tenga al menos 3 puntos si no está vacío
        if ($this->pattern !== '' && count(explode('-', $this->pattern)) < 3) {
            $this->addError('pattern', 'El patrón debe tener al menos 3 puntos.');

            return;
        }

        if ($this->pattern === '') {
            $this->dispositivo->pattern_encrypted = null;
        } else {
            $this->dispositivo->pattern_encrypted = Crypt::encryptString($this->pattern);
        }

        $this->dispositivo->save();

        session()->flash('pattern_message', 'Patrón guardado correctamente.');
    }

    public function render()
    {
        return view('livewire.dispositivos.pattern-form');
    }
}
