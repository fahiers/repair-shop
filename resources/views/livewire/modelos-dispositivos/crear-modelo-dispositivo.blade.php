<div class="max-w-3xl mx-auto px-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('modelos.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700" title="Volver a modelos de dispositivos">
            <flux:icon.arrow-left class="size-4" />
            <span class="sr-only">Volver</span>
        </a>
        <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100">Agregar un nuevo modelo de dispositivo</h1>
    </div>
    <form wire:submit.prevent="save" class="mt-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="marca" class="block text-sm font-medium text-gray-900">Marca</label>
                <input
                    type="text"
                    id="marca"
                    wire:model="marca"
                    autofocus
                    autocomplete="off"
                    placeholder="Ej. Apple"
                    class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                >
                @error('marca') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="modelo" class="block text-sm font-medium text-gray-900">Modelo</label>
                <input
                    type="text"
                    id="modelo"
                    wire:model="modelo"
                    autocomplete="off"
                    placeholder="Ej. iPhone 15 Pro"
                    class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                >
                @error('modelo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="anio" class="block text-sm font-medium text-gray-900">Año</label>
                <input
                    type="number"
                    id="anio"
                    wire:model="anio"
                    min="1900"
                    max="2030"
                    placeholder="Ej. 2023"
                    class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                >
                @error('anio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="hidden md:block"></div>
        </div>

        <div>
            <label for="descripcion" class="block text-sm font-medium text-gray-900">Descripción</label>
            <textarea
                id="descripcion"
                rows="5"
                wire:model="descripcion"
                placeholder="Descripción del modelo de dispositivo..."
                class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
            ></textarea>
            @error('descripcion') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center rounded-lg bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:opacity-60"
            >
                <span wire:loading.remove>Agregar modelo</span>
                <span wire:loading>Guardando…</span>
            </button>
        </div>
    </form>
</div>

