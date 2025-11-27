<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-6">
                        <a href="{{ route('clientes.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700" title="Volver a clientes">
                            <flux:icon.arrow-left class="size-4" />
                            <span class="sr-only">Volver</span>
                        </a>
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            {{ __('Editar Cliente') }}
                        </h2>
                    </div>
                    <form wire:submit.prevent="update">
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
                            <flux:field>
                                <flux:label>{{ __('Notas') }}</flux:label>
                                <flux:textarea wire:model.defer="notas" rows="4" />
                                <flux:error name="notas" />
                            </flux:field>
                        </div>

                        <div class="mt-6">
                            <flux:button type="submit" variant="primary">
                                {{ __('Actualizar') }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
