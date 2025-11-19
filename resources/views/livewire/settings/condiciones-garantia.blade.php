<?php

use App\Models\CondicionesGarantia;
use Livewire\Volt\Component;

new class extends Component {
    public string $contenido = '';

    public function mount(): void
    {
        $this->loadCondiciones();
    }

    private function loadCondiciones(): void
    {
        $condiciones = CondicionesGarantia::query()->first();

        if ($condiciones) {
            $this->contenido = $condiciones->contenido;
        } else {
            // Inicializar con valores por defecto si no existen
            $this->contenido = "1. **Recepción del Equipo**\n\nEl cliente declara el estado del equipo, incluyendo accesorios, estado físico y fallas reportadas, según se detalla en la orden de trabajo. El taller no se hace responsable de fallas no reportadas al ingreso ni de daños ocultos que se manifiesten durante el diagnóstico o reparación.\n\n2. **Diagnóstico y Presupuestos**\n\nEl diagnóstico puede requerir desarmado o pruebas internas. Si el cliente decide no proceder con la reparación, puede aplicarse un cobro por revisión técnica. El presupuesto informado puede variar si durante el proceso de reparación se detectan fallas adicionales.\n\n3. **Pérdida de Datos**\n\nEl taller no se hace responsable de la pérdida de información, configuraciones, cuentas, fotografías o aplicaciones. El cliente es responsable de realizar una copia de seguridad antes de entregar el equipo, siempre que sea posible.\n\n4. **Manipulación de Componentes**\n\nEn reparaciones que requieren soldadura, re-soldadura, reballing u otras intervenciones de alto riesgo, el equipo puede presentar fallas colaterales o dejar de funcionar completamente debido al estado previo del hardware.\n\n5. **Garantía de Reparación**\n\nTodas las reparaciones cuentan con garantía únicamente sobre el servicio y/o pieza reemplazada, no sobre otras fallas del equipo. Garantía para piezas nuevas: 30 a 90 días (según pieza y proveedor). Garantía para reparaciones técnicas (microsoldadura, pistas, conectores, etc.): 15 a 30 días.";
        }
    }

    public function save(): void
    {
        $this->validate([
            'contenido' => ['required', 'string'],
        ]);

        CondicionesGarantia::query()->updateOrCreate(
            ['id' => 1],
            [
                'contenido' => $this->contenido,
            ]
        );

        $this->dispatch('condiciones-saved');
    }

    public function preview(): void
    {
        // Validar antes de abrir previsualización
        $this->validate([
            'contenido' => ['required', 'string'],
        ]);
        
        // Guardar primero para asegurar que los datos estén actualizados
        $this->save();
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Condiciones y garantía')" :subheading="__('Configura el contenido de condiciones y garantía del servicio')" max-width="max-w-4xl">
        <div class="my-6 w-full space-y-6">
            <!-- Header con título y botón previsualizar -->
            <div class="mb-6 flex items-center justify-between border-b border-zinc-200 pb-4 dark:border-zinc-700">
                <h3 class="text-lg font-semibold">{{ __('Contenido (Cuerpo de texto)') }}</h3>
                <flux:button 
                    variant="primary" 
                    wire:click="preview" 
                    x-on:click.prevent="setTimeout(() => window.open('{{ route('condiciones-garantia.preview') }}', '_blank'), 100)"
                    type="button"
                >
                    <flux:icon name="magnifying-glass" class="me-2" />
                    {{ __('Previsualizar') }}
                </flux:button>
            </div>

            <!-- Título principal y cuadro de texto -->
            <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <h4 class="mb-6 text-xl font-bold">{{ __('Condiciones y Garantía del Servicio') }}</h4>

                <form wire:submit="save" class="space-y-6">
                    <flux:textarea 
                        wire:model="contenido" 
                        :label="__('Contenido')" 
                        rows="20"
                        class="w-full"
                        required 
                    />

                    <div class="flex items-center gap-4 pt-4">
                        <flux:button variant="primary" type="submit" class="w-full">
                            {{ __('Guardar') }}
                        </flux:button>

                        <x-action-message class="me-3" on="condiciones-saved">
                            {{ __('Guardado.') }}
                        </x-action-message>
                    </div>
                </form>
            </div>
        </div>
    </x-settings.layout>
</section>
