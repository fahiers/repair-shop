<?php

use App\Models\AccesorioConfig;
use Livewire\Volt\Component;

new class extends Component {
    public string $nombre = '';
    public bool $activo = true;

    /** @var array<int, array{id:int,nombre:string,activo:bool}> */
    public array $items = [];

    public ?int $editingId = null;
    public string $editingNombre = '';
    public bool $editingActivo = true;

    public function mount(): void
    {
        $this->loadItems();
    }

    private function loadItems(): void
    {
        $this->items = AccesorioConfig::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'activo'])
            ->map(fn ($a) => [
                'id' => (int) $a->id,
                'nombre' => (string) $a->nombre,
                'activo' => (bool) $a->activo,
            ])->all();
    }

    public function create(): void
    {
        $validated = $this->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'activo' => ['boolean'],
        ]);

        AccesorioConfig::query()->create($validated);

        $this->reset(['nombre', 'activo']);
        $this->activo = true;
        $this->loadItems();
        $this->dispatch('accesorio-created');
    }

    public function startEdit(int $id): void
    {
        $row = AccesorioConfig::query()->findOrFail($id);
        $this->editingId = $row->id;
        $this->editingNombre = (string) $row->nombre;
        $this->editingActivo = (bool) $row->activo;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editingNombre = '';
        $this->editingActivo = true;
    }

    public function update(): void
    {
        if ($this->editingId === null) {
            return;
        }

        $validated = $this->validate([
            'editingNombre' => ['required', 'string', 'max:255'],
            'editingActivo' => ['boolean'],
        ]);

        AccesorioConfig::query()->whereKey($this->editingId)->update([
            'nombre' => $validated['editingNombre'],
            'activo' => $validated['editingActivo'],
        ]);

        $this->cancelEdit();
        $this->loadItems();
        $this->dispatch('accesorio-updated');
    }

    public function delete(int $id): void
    {
        AccesorioConfig::query()->whereKey($id)->delete();
        $this->loadItems();
        $this->dispatch('accesorio-deleted');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Accesorios')" :subheading="__('Gestiona los accesorios disponibles')">
        <form wire:submit="create" class="my-6 w-full space-y-4">
            <flux:input wire:model="nombre" :label="__('Nombre del accesorio')" type="text" required autofocus />

            <div class="flex items-center gap-3">
                <flux:switch wire:model="activo" :label="__('Activo')" />
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" class="w-full">
                    {{ __('Agregar') }}
                </flux:button>

                <x-action-message class="me-3" on="accesorio-created">
                    {{ __('Creado.') }}
                </x-action-message>
            </div>
        </form>

        <div class="mt-6 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Nombre') }}</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">{{ __('Activo') }}</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @forelse ($items as $item)
                        <tr wire:key="accesorio-{{ $item['id'] }}">
                            <td class="px-4 py-3 align-top">
                                @if ($editingId === $item['id'])
                                    <flux:input wire:model="editingNombre" type="text" />
                                @else
                                    <span class="text-sm">{{ $item['nombre'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                @if ($editingId === $item['id'])
                                    <flux:switch wire:model="editingActivo" />
                                @else
                                    <span class="text-sm">{{ $item['activo'] ? __('Sí') : __('No') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($editingId === $item['id'])
                                        <flux:button size="sm" variant="primary" wire:click="update">{{ __('Guardar') }}</flux:button>
                                        <flux:button size="sm" variant="ghost" wire:click="cancelEdit">{{ __('Cancelar') }}</flux:button>
                                    @else
                                        <flux:button size="sm" variant="ghost" wire:click="startEdit({{ $item['id'] }})">{{ __('Editar') }}</flux:button>
                                        <flux:button size="sm" variant="danger" wire:click="delete({{ $item['id'] }})">{{ __('Eliminar') }}</flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-sm text-zinc-500">{{ __('Sin accesorios aún') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sr-only">
            <x-action-message on="accesorio-updated">{{ __('Actualizado.') }}</x-action-message>
            <x-action-message on="accesorio-deleted">{{ __('Eliminado.') }}</x-action-message>
        </div>
    </x-settings.layout>
</section>


