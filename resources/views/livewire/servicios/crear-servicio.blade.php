<div class="max-w-3xl mx-auto px-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('servicios.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700" title="Volver a servicios">
            <flux:icon.arrow-left class="size-4" />
            <span class="sr-only">Volver</span>
        </a>
        <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100">Agregar un nuevo servicio</h1>
    </div>
    <form wire:submit.prevent="save" class="mt-6 space-y-6">
        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-900">Nombre del servicio</label>
            <input
                type="text"
                id="nombre"
                wire:model="nombre"
                autofocus
                autocomplete="off"
                placeholder="Ej. Alineación"
                class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
            >
            @error('nombre') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="categoria" class="block text-sm font-medium text-gray-900">Categoría</label>
                <input
                    type="text"
                    id="categoria"
                    wire:model="categoria"
                    autocomplete="off"
                    placeholder="Ej. Suspensión"
                    class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                >
                @error('categoria') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="precio_base" class="block text-sm font-medium text-gray-900">Precio base</label>
                <div class="relative mt-2">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400" aria-hidden="true">$</span>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        inputmode="decimal"
                        id="precio_base"
                        wire:model="precio_base"
                        placeholder="2999"
                        class="block w-full rounded-lg border border-gray-200 bg-gray-50 pl-8 pr-3.5 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                    >
                </div>
                @error('precio_base') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-900">Estado</label>
                <select
                    id="estado"
                    wire:model="estado"
                    class="mt-2 block w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
                @error('estado') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="hidden md:block"></div>
        </div>

        <div>
            <label for="descripcion" class="block text-sm font-medium text-gray-900">Descripción</label>
            <textarea
                id="descripcion"
                rows="5"
                wire:model="descripcion"
                placeholder="Tu descripción aquí"
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
                <span wire:loading.remove>Agregar servicio</span>
                <span wire:loading>Guardando…</span>
            </button>
        </div>
    </form>
</div>
