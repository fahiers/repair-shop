<?php

namespace App\Livewire\ModelosDispositivos;

use App\Models\ModeloDispositivo;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $modelos = ModeloDispositivo::query()
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('marca', 'like', $term)
                      ->orWhere('modelo', 'like', $term)
                      ->orWhere('descripcion', 'like', $term);
                });
            })
            ->orderBy('marca')
            ->orderBy('modelo')
            ->paginate(10);

        return view('livewire.modelos-dispositivos.index', [
            'modelos' => $modelos,
        ]);
    }
}
