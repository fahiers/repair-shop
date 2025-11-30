<?php

use App\Models\Empresa;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $nombre = '';

    public string $email = '';

    public string $telefono = '';

    public string $direccion = '';

    public string $rut = '';

    public string $facebook_username = '';

    public string $instagram_username = '';

    public $logo = null;

    public ?string $logoPreview = null;

    public function mount(): void
    {
        $empresa = Empresa::first();

        if ($empresa) {
            $this->nombre = $empresa->nombre ?? '';
            $this->email = $empresa->email ?? '';
            $this->telefono = $empresa->telefono ?? '';
            $this->direccion = $empresa->direccion ?? '';
            $this->rut = $empresa->rut ?? '';
            $this->facebook_username = $empresa->facebook_username ?? '';
            $this->instagram_username = $empresa->instagram_username ?? '';
            $this->logoPreview = $empresa->logo_url;
        }
    }

    public function updatedLogo(): void
    {
        $this->validate([
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        if ($this->logo) {
            $this->logoPreview = $this->logo->temporaryUrl();
        }
    }

    public function deleteLogo(): void
    {
        $empresa = Empresa::first();

        if ($empresa && $empresa->logo_path) {
            if (Storage::disk('public')->exists($empresa->logo_path)) {
                Storage::disk('public')->delete($empresa->logo_path);
            }

            $empresa->logo_path = null;
            $empresa->save();
        }

        $this->logoPreview = null;
        $this->logo = null;

        $this->dispatch('company-settings-saved');
    }

    public function save(): void
    {
        $this->validate([
            'nombre' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string'],
            'rut' => ['nullable', 'string', 'max:20'],
            'facebook_username' => ['nullable', 'string', 'max:255'],
            'instagram_username' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        $empresa = Empresa::first();

        if (! $empresa) {
            $empresa = new Empresa();
        }

        $empresa->nombre = trim($this->nombre) !== '' ? $this->nombre : null;
        $empresa->email = $this->email ?: null;
        $empresa->telefono = $this->telefono ?: null;
        $empresa->direccion = $this->direccion ?: null;
        $empresa->rut = $this->rut ?: null;
        $empresa->facebook_username = $this->facebook_username ?: null;
        $empresa->instagram_username = $this->instagram_username ?: null;

        if ($this->logo) {
            // Eliminar logo anterior si existe
            if ($empresa->logo_path && Storage::disk('public')->exists($empresa->logo_path)) {
                Storage::disk('public')->delete($empresa->logo_path);
            }

            // Guardar nuevo logo
            $empresa->logo_path = $this->logo->store('logos', 'public');
        }

        $empresa->save();

        // Refrescar el modelo para obtener los valores actualizados
        $empresa->refresh();

        // Actualizar preview después de guardar
        if ($this->logo) {
            $this->logo = null;
            $this->logoPreview = $empresa->logo_url;
        } elseif ($empresa->logo_path) {
            // Si no se subió un nuevo logo pero existe uno, mantener el preview
            $this->logoPreview = $empresa->logo_url;
        }

        $this->dispatch('company-settings-saved');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Configurar mi empresa')" :subheading="__('Actualiza la información básica de tu empresa')">
        <form wire:submit="save" class="my-6 w-full space-y-6">
            <flux:input wire:model="nombre" :label="__('Nombre de la empresa')" type="text" autofocus />
            
            <flux:input wire:model="email" :label="__('Correo de la empresa')" type="email" />
            
            <flux:input wire:model="telefono" :label="__('Teléfono de la empresa')" type="text" />
            
            <flux:textarea wire:model="direccion" :label="__('Dirección')" rows="3" />
            
            <flux:input wire:model="rut" :label="__('RUT / Identificación Fiscal')" type="text" />

            <flux:input wire:model="facebook_username" :label="__('Usuario de Facebook')" type="text" />
            
            <flux:input wire:model="instagram_username" :label="__('Usuario de Instagram')" type="text" />

            <div>
                <flux:field>
                    <flux:label>{{ __('Logo de la empresa') }}</flux:label>
                    <flux:input type="file" wire:model="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp" />
                    <flux:description>
                        {{ __('Formatos aceptados: PNG, JPG, JPEG, SVG, WEBP (recomendado: PNG o SVG para mejor calidad). Tamaño máximo: 2MB') }}
                    </flux:description>
                    @error('logo')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                @if ($logoPreview)
                    <div class="mt-4 space-y-3">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Vista previa del logo:') }}</p>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <img src="{{ $logoPreview }}" alt="Logo preview" class="h-32 w-32 rounded-lg object-contain border-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 shadow-sm" />
                            </div>
                            <div class="flex-1 pt-2">
                                <flux:button 
                                    type="button" 
                                    variant="danger" 
                                    wire:click="deleteLogo"
                                    wire:confirm="{{ __('¿Estás seguro de que deseas eliminar el logo? Esta acción no se puede deshacer.') }}"
                                    wire:loading.attr="disabled"
                                    size="sm"
                                >
                                    <span wire:loading.remove wire:target="deleteLogo">{{ __('Eliminar logo') }}</span>
                                    <span wire:loading wire:target="deleteLogo">{{ __('Eliminando...') }}</span>
                                </flux:button>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('El logo se mostrará en la barra lateral y en los documentos generados.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" class="w-full" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Guardar') }}</span>
                    <span wire:loading>{{ __('Guardando...') }}</span>
                </flux:button>

                <x-action-message class="me-3" on="company-settings-saved">
                    {{ __('Guardado.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
    
</section>


