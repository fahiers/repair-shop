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
    </x-settings.layout>
    
</section>


