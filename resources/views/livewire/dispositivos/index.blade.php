<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Dispositivos</h1>
    </div>

    <!-- Pestañas -->
    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center gap-2 md:gap-4 flex-wrap">
            <button wire:click="setTab('en_taller')"
                    class="px-3 py-1.5 text-sm transition-colors {{ $activeTab === 'en_taller' ? 'font-semibold text-zinc-900 dark:text-zinc-100 border-b-2 border-indigo-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                En Taller
            </button>
            <button wire:click="setTab('historial')"
                    class="px-3 py-1.5 text-sm transition-colors {{ $activeTab === 'historial' ? 'font-semibold text-zinc-900 dark:text-zinc-100 border-b-2 border-indigo-600' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                Historial
            </button>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-4">
        <div class="relative flex-1">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar por modelo, cliente o IMEI..." />
        </div>
        <div class="w-full sm:w-48">
            <flux:select wire:model.live="filterLock" placeholder="Todos los bloqueos">
                <flux:select.option value="">Todos</flux:select.option>
                <flux:select.option value="patron">Con Patrón</flux:select.option>
                <flux:select.option value="contrasena">Con Contraseña</flux:select.option>
                <flux:select.option value="ninguno">Libre</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Modelo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">IMEI / Color</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Seguridad</th>
                    @if($activeTab === 'en_taller')
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Estado OT</th>
                    @endif
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                @forelse ($dispositivos as $device)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40" wire:key="device-{{ $device->id }}">
                        <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500">
                                    <flux:icon name="device-phone-mobile" class="w-5 h-5" />
                                </div>
                                <div>
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $device->modelo->modelo ?? 'Desconocido' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $device->modelo->marca ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                            <div class="text-zinc-900 dark:text-zinc-100 font-medium">{{ $device->cliente->nombre ?? 'Sin Cliente' }}</div>
                            <div class="text-xs text-zinc-500">Registrado</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                            <div class="text-zinc-700 dark:text-zinc-300 font-mono text-xs">{{ $device->imei ?? '-' }}</div>
                            <div class="text-xs text-zinc-500">{{ $device->color }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                            @if ($device->patron || $device->pattern_encrypted)
                                <flux:badge color="indigo" size="sm" icon="squares-2x2">Patrón</flux:badge>
                            @elseif ($device->contraseña)
                                <flux:badge color="emerald" size="sm" icon="lock-closed">PIN</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" icon="lock-open">Libre</flux:badge>
                            @endif
                        </td>
                        @if($activeTab === 'en_taller')
                            <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                @php
                                    $ordenActiva = $device->ordenes->first();
                                @endphp
                                @if($ordenActiva && $ordenActiva->estado)
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $ordenActiva->estado->clasesColor() }}">
                                        {{ $ordenActiva->estado->etiqueta() }}
                                    </span>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-600">-</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button icon="document-text" variant="ghost" size="sm" href="{{ route('dispositivos.historial-clinico', $device) }}" title="Ver Historial Clínico" />
                                <flux:button icon="pencil" variant="ghost" size="sm" href="{{ route('dispositivos.edit', $device) }}" title="Editar" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $activeTab === 'en_taller' ? '6' : '5' }}" class="px-6 py-12 text-center text-zinc-500">
                            <div class="flex flex-col items-center justify-center">
                                <flux:icon name="device-phone-mobile" class="w-12 h-12 mb-3 opacity-20" />
                                <p>No se encontraron dispositivos</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $dispositivos->links() }}
</div>
