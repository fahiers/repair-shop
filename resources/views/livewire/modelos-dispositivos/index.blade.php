<div>
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Modelos de Dispositivos</h1>

        <a href="{{ route('modelos.crear') }}" wire:navigate class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 4.5a.75.75 0 01.75.75v6h6a.75.75 0 010 1.5h-6v6a.75.75 0 01-1.5 0v-6h-6a.75.75 0 010-1.5h6v-6A.75.75 0 0112 4.5z"/></svg>
            <span>Nuevo modelo</span>
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
                    placeholder="Buscar por marca o modelo..."
                    wire:model.live.debounce.500ms="search"
                    class="w-full rounded-md border border-zinc-300 bg-white pl-10 pr-3 py-2 text-zinc-900 placeholder-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-0 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                />
            </div>
        </div>

        <!-- Tabla de modelos -->
        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Marca</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Modelo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Año</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                    @forelse ($modelos as $modelo)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">{{ $modelo->marca }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">{{ $modelo->modelo }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                <div class="max-w-xs truncate">{{ $modelo->descripcion ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">{{ $modelo->anio ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('modelos.editar', ['id' => $modelo->id]) }}" wire:navigate class="inline-flex items-center gap-1 text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712z" />
                                        <path d="M19.513 8.199l-3.712-3.712-12 12A4.5 4.5 0 003 19.5V21h1.5a4.5 4.5 0 003.014-1.2l11.999-12z" />
                                    </svg>
                                    <span class="sr-only">Editar</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                No se encontraron modelos de dispositivos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pie con info y paginación -->
            <div class="flex items-center justify-between gap-4 border-t border-zinc-200 p-4 dark:border-zinc-800">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Mostrando {{ $modelos->firstItem() }}-{{ $modelos->lastItem() }} de {{ $modelos->total() }}
                </p>
                <div>
                    {{ $modelos->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

