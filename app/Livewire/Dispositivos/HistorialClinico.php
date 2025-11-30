<?php

namespace App\Livewire\Dispositivos;

use App\Models\Dispositivo;
use Livewire\Component;

class HistorialClinico extends Component
{
    public Dispositivo $dispositivo;

    public function mount(Dispositivo $dispositivo): void
    {
        $this->dispositivo = $dispositivo->load([
            'cliente',
            'modelo',
            'ordenes' => function ($query) {
                $query->with([
                    'tecnico',
                    'servicios',
                    'productos',
                ])->orderBy('fecha_ingreso', 'desc');
            },
        ]);
    }

    public function render()
    {
        return view('livewire.dispositivos.historial-clinico');
    }
}
