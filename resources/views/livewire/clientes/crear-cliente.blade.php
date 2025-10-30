<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:field>
                                <flux:label>{{ __('Nombre') }}</flux:label>
                                <flux:input wire:model.defer="nombre" />
                                <flux:error name="nombre" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Teléfono') }}</flux:label>
                                <flux:input wire:model.defer="telefono" />
                                <flux:error name="telefono" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Email') }}</flux:label>
                                <flux:input type="email" wire:model.defer="email" />
                                <flux:error name="email" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Dirección') }}</flux:label>
                                <flux:input wire:model.defer="direccion" />
                                <flux:error name="direccion" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('RUT') }}</flux:label>
                                <flux:input wire:model.defer="rut" />
                                <flux:error name="rut" />
                            </flux:field>
                        </div>

                        <div class="mt-6">
                            <flux:button type="submit" variant="primary">
                                {{ __('Guardar') }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
