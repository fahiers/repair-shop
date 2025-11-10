<div>
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Órdenes de Trabajo</h1>

        <a href="{{ route('ordenes-trabajo.crear') }}" wire:navigate class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 4.5a.75.75 0 01.75.75v6h6a.75.75 0 010 1.5h-6v6a.75.75 0 01-1.5 0v-6h-6a.75.75 0 010-1.5h6v-6A.75.75 0 0112 4.5z"/></svg>
            <span>Nueva orden</span>
        </a>
    </div>

    <div class="mt-6">
        <!-- Buscador y filtro de estado -->
        <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="relative md:flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
                </span>
                <input
                    type="text"
                    placeholder="Buscar orden..."
                    wire:model.live.debounce.500ms="search"
                    class="w-full rounded-md border border-zinc-300 bg-white pl-10 pr-3 py-2 text-zinc-900 placeholder-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-0 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                />
            </div>
            <div class="md:w-64">
                <select
                    wire:model.live="estado"
                    class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-zinc-900 focus:border-zinc-400 focus:outline-none focus:ring-0 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                >
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="diagnostico">Diagnóstico</option>
                    <option value="en_reparacion">En reparación</option>
                    <option value="espera_repuesto">Espera repuesto</option>
                    <option value="listo">Listo</option>
                    <option value="entregado">Entregado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </div>

        <!-- Tabla de órdenes -->
        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Orden</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Dispositivo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Ingreso</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                    @forelse ($ordenes as $orden)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">{{ $orden->numero_orden ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">{{ data_get($orden, 'dispositivo.cliente.nombre') ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">{{ data_get($orden, 'dispositivo.modelo.nombre') ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">{{ optional($orden->fecha_ingreso ? \Carbon\Carbon::parse($orden->fecha_ingreso) : null)?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                ${{ number_format(($orden->costo_final ?? $orden->costo_estimado ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ ($orden->estado?->value ?? 'pendiente') === 'cancelado' ? 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' : 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' }}">
                                    {{ ucfirst(str_replace('_', ' ', $orden->estado?->value ?? 'pendiente')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('ordenes-trabajo.editar', ['id' => $orden->id]) }}" wire:navigate class="inline-flex items-center gap-1 text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">
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
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                No se encontraron órdenes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex items-center justify-between gap-4 border-t border-zinc-200 p-4 dark:border-zinc-800">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Mostrando {{ $ordenes->firstItem() }}-{{ $ordenes->lastItem() }} de {{ $ordenes->total() }}
                </p>
                <div>
                    {{ $ordenes->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
</div>


