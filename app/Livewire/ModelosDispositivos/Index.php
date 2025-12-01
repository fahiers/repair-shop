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
        // Normalizar el término de búsqueda
        $searchTerm = trim($this->search);
        $searchTerm = preg_replace('/\s+/', ' ', $searchTerm);

        $modelos = ModeloDispositivo::query()
            ->when($searchTerm !== '', function ($query) use ($searchTerm) {
                // Dividir el término de búsqueda en palabras individuales
                $words = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);

                // Búsqueda por palabras individuales (AND entre palabras)
                // Cada palabra debe aparecer en marca o modelo
                foreach ($words as $word) {
                    $term = '%'.$word.'%';
                    $query->where(function ($q) use ($term) {
                        $q->where('marca', 'like', $term)
                            ->orWhere('modelo', 'like', $term);
                    });
                }

                // Ordenar por relevancia: priorizar coincidencias en modelo, luego marca
                $searchLower = mb_strtolower($searchTerm);
                $query->orderByRaw('
                    CASE 
                        WHEN LOWER(modelo) LIKE ? THEN 1
                        WHEN LOWER(marca) LIKE ? THEN 2
                        ELSE 3
                    END
                ', [$searchLower.'%', $searchLower.'%']);
            })
            ->orderBy('marca')
            ->orderBy('modelo')
            ->paginate(10);

        return view('livewire.modelos-dispositivos.index', [
            'modelos' => $modelos,
        ]);
    }
}
