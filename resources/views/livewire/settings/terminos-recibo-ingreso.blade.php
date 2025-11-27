<?php

use App\Models\TerminosReciboIngreso;
use Livewire\Volt\Component;

new class extends Component {
    public array $terminos = [];

    public function mount(): void
    {
        $this->loadTerminos();
    }

    private function loadTerminos(): void
    {
        $config = TerminosReciboIngreso::query()->first();

        if ($config && $config->terminos) {
            $this->terminos = $config->terminos;
        } else {
            // Valores por defecto
            $this->terminos = [
                'Los equipos descritos se entregaran solamente al portador de este recibo.',
                'Despues de 30 dias de aceptado el presupuesto, en caso de no retiro, se cargaran $200 diarios por concepto de bodegaje.',
            ];
        }
    }

    public function addTermino(): void
    {
        $this->terminos[] = '';
    }

    public function removeTermino(int $index): void
    {
        unset($this->terminos[$index]);
        $this->terminos = array_values($this->terminos); // Reindexar array
    }

    public function save(): void
    {
        $this->validate([
            'terminos' => ['required', 'array', 'min:1'],
            'terminos.*' => ['required', 'string', 'max:500'],
        ]);

        TerminosReciboIngreso::query()->updateOrCreate(
            ['id' => 1],
            [
                'terminos' => array_filter($this->terminos), // Eliminar vacíos
            ]
        );

        $this->dispatch('terminos-saved');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout 
        :heading="__('Términos del Recibo de Ingreso')" 
        :subheading="__('Configura los términos y condiciones que aparecerán en el recibo de ingreso')" 
        max-width="max-w-4xl"
    >
        <div class="my-6 w-full space-y-6">
            <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <form wire:submit="save" class="space-y-4">
                    <div class="space-y-3">
                        @foreach($terminos as $index => $termino)
                            <div class="flex items-start gap-2" wire:key="termino-{{ $index }}">
                                <div class="flex-1">
                                    <flux:input 
                                        wire:model="terminos.{{ $index }}" 
                                        :placeholder="__('Escribe el término aquí...')"
                                        class="w-full"
                                    />
                                </div>
                                @if(count($terminos) > 1)
                                    <flux:button 
                                        type="button"
                                        variant="danger"
                                        wire:click="removeTermino({{ $index }})"
                                        class="shrink-0"
                                    >
                                        <flux:icon name="trash" />
                                    </flux:button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <flux:button 
                        type="button"
                        variant="ghost"
                        wire:click="addTermino"
                        class="w-full"
                    >
                        <flux:icon name="plus" class="me-2" />
                        {{ __('Agregar término') }}
                    </flux:button>

                    <div class="flex items-center gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button variant="primary" type="submit" class="w-full">
                            {{ __('Guardar') }}
                        </flux:button>

                        <x-action-message class="me-3" on="terminos-saved">
                            {{ __('Guardado.') }}
                        </x-action-message>
                    </div>
                </form>
            </div>
        </div>
    </x-settings.layout>
</section>

