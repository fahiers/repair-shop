<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $companyName = '';
    public string $companyEmail = '';
    public string $companyPhone = '';

    public function save(): void
    {
        $this->validate([
            'companyName' => ['required', 'string', 'max:255'],
            'companyEmail' => ['nullable', 'email', 'max:255'],
            'companyPhone' => ['nullable', 'string', 'max:50'],
        ]);

        // Aquí se guardará la configuración de la empresa (modelo/configuración futura)

        $this->dispatch('company-settings-saved');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Configurar mi empresa')" :subheading="__('Actualiza la información básica de tu empresa')">
        <form wire:submit="save" class="my-6 w-full space-y-6">
            <flux:input wire:model="companyName" :label="__('Nombre de la empresa')" type="text" required autofocus />
            <flux:input wire:model="companyEmail" :label="__('Correo de la empresa')" type="email" />
            <flux:input wire:model="companyPhone" :label="__('Teléfono de la empresa')" type="text" />

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" class="w-full">
                    {{ __('Guardar') }}
                </flux:button>

                <x-action-message class="me-3" on="company-settings-saved">
                    {{ __('Guardado.') }}
                </x-action-message>
            </div>
        </form>
        
        <div class="mt-10 grid gap-6">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Configurar accesorios') }}</h3>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Administra los accesorios disponibles para tus órdenes.') }}</p>
                    </div>
                    <flux:button :href="route('accesorios.index')" icon="wrench" variant="primary" wire:navigate>
                        {{ __('Abrir') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </x-settings.layout>
    
</section>


