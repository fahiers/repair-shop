<div class="space-y-4">
    {{-- Patrón de desbloqueo --}}
    @if($pattern)
        {{-- Título --}}
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    Patrón de desbloqueo
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-mono">
                    {{ $pattern }}
                </p>
            </div>
            <button
                type="button"
                wire:click="$dispatch('cambiar-patron')"
                wire:confirm="¿Estas seguro de cambiar el patron?"
                class="text-xs px-2 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700"
            >
                Cambiar patrón
            </button>
        </div>

        {{-- Visualización del patrón guardado --}}
        <div
            x-data="patternViewer(@js($pattern))"
            x-init="draw()"
            wire:key="pattern-viewer-{{ md5($pattern) }}-{{ $refreshKey }}"
            class="relative mx-auto h-40 w-40 select-none"
        >
            <svg class="absolute inset-0 h-full w-full pointer-events-none">
                <path
                    x-ref="path"
                    stroke="currentColor"
                    stroke-width="3"
                    fill="none"
                    class="text-emerald-600 dark:text-emerald-500"
                ></path>
            </svg>

            <div class="grid grid-cols-3 gap-4 h-full w-full items-center justify-items-center">
                @for ($i = 1; $i <= 9; $i++)
                    <div
                        class="h-8 w-8 rounded-full border-2 border-zinc-400 dark:border-zinc-600 bg-white dark:bg-zinc-800 flex items-center justify-center transition"
                        data-id="{{ $i }}"
                        :class="isActive({{ $i }}) ? 'bg-emerald-600 border-emerald-800 dark:bg-emerald-500 dark:border-emerald-700 text-white' : ''"
                    >
                        <span class="text-[10px] font-medium">{{ $i }}</span>
                    </div>
                @endfor
            </div>
        </div>
    {{-- Contraseña de desbloqueo --}}
    @elseif($password)
        {{-- Título --}}
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    Contraseña de desbloqueo
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-mono">
                    {{ $password }}
                </p>
            </div>
            <button
                type="button"
                wire:click="$dispatch('cambiar-patron')"
                wire:confirm="¿Estas seguro de cambiar la contraseña?"
                class="text-xs px-2 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700"
            >
                Cambiar contraseña
            </button>
        </div>
    {{-- Sin bloqueo configurado --}}
    @else
        <div>
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                Método de desbloqueo
            </h3>
            <div class="text-center py-8 text-zinc-400 dark:text-zinc-500 text-sm">
                Sin bloqueo configurado
            </div>
        </div>
    @endif
</div>
