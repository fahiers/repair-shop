<div>
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Clientes</h1>

        <a href="{{ route('clientes.crear') }}" wire:navigate class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 4.5a.75.75 0 01.75.75v6h6a.75.75 0 010 1.5h-6v6a.75.75 0 01-1.5 0v-6h-6a.75.75 0 010-1.5h6v-6A.75.75 0 0112 4.5z"/></svg>
            <span>Nuevo cliente</span>
        </a>
    </div>

    <div class="mt-6">
        <!-- Buscador -->
        <div class="mb-4">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
                </span>
                <input
                    type="text"
                    placeholder="Buscar cliente..."
                    wire:model.live.debounce.500ms="search"
                    class="w-full rounded-md border border-zinc-300 bg-white pl-10 pr-3 py-2 text-zinc-900 placeholder-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-0 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                />
            </div>
        </div>

        <!-- Tabla de clientes -->
        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Dirección</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">RUT</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                    @forelse ($clientes as $cliente)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">{{ $cliente->nombre }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">{{ $cliente->telefono }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">{{ $cliente->email }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                <div class="max-w-xs truncate">{{ $cliente->direccion }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">{{ $cliente->rut }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('clientes.editar', $cliente) }}" wire:navigate class="inline-flex items-center gap-1 text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                            <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712z" />
                                            <path d="M19.513 8.199l-3.712-3.712-12 12A4.5 4.5 0 003 19.5V21h1.5a4.5 4.5 0 003.014-1.2l11.999-12z" />
                                        </svg>
                                        <span class="sr-only">Editar</span>
                                    </a>
                                    <button wire:click="delete({{ $cliente->id }})" wire:confirm="¿Estás seguro de que quieres eliminar este cliente?" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                            <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H4.84a3 3 0 01-2.991-2.77L1.01 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 013.478 4.5h13.022zM6.75 7.5a.75.75 0 00-.75.75v9a.75.75 0 001.5 0v-9a.75.75 0 00-.75-.75zm6.75 0a.75.75 0 00-.75.75v9a.75.75 0 001.5 0v-9a.75.75 0 00-.75-.75z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="sr-only">Eliminar</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                No se encontraron clientes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex items-center justify-between gap-4 border-t border-zinc-200 p-4 dark:border-zinc-800">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Mostrando {{ $clientes->firstItem() }}-{{ $clientes->lastItem() }} de {{ $clientes->total() }}
                </p>
                <div>
                    {{ $clientes->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
