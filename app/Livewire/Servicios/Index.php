<?php

namespace App\Livewire\Servicios;

use App\Models\Servicio;
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
        $servicios = Servicio::query()
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('nombre', 'like', $term)
                      ->orWhere('categoria', 'like', $term)
                      ->orWhere('descripcion', 'like', $term);
                });
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.servicios.index', [
            'servicios' => $servicios,
        ]);
    }
}
