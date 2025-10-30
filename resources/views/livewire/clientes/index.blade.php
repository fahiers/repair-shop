<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Clientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end mb-4">
                        <x-button-link href="{{ route('clientes.crear') }}">
                            {{ __('Crear Cliente') }}
                        </x-button-link>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nombre</th>
                                    <th scope="col" class="px-6 py-3">Teléfono</th>
                                    <th scope="col" class="px-6 py-3">Email</th>
                                    <th scope="col" class="px-6 py-3">Dirección</th>
                                    <th scope="col" class="px-6 py-3">RUT</th>
                                    <th scope="col" class="px-6 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientes as $cliente)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $cliente->nombre }}</td>
                                        <td class="px-6 py-4">{{ $cliente->telefono }}</td>
                                        <td class="px-6 py-4">{{ $cliente->email }}</td>
                                        <td class="px-6 py-4">{{ $cliente->direccion }}</td>
                                        <td class="px-6 py-4">{{ $cliente->rut }}</td>
                                        <td class="px-6 py-4">
                                            <x-button-link href="{{ route('clientes.editar', $cliente) }}">
                                                {{ __('Editar') }}
                                            </x-button-link>
                                            <x-danger-button wire:click="delete({{ $cliente->id }})" wire:confirm="¿Estás seguro de que quieres eliminar este cliente?">
                                                {{ __('Eliminar') }}
                                            </x-danger-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No hay clientes registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $clientes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
