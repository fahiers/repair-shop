<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <flux:button icon="arrow-left" variant="ghost" class="h-10 w-10 !rounded-full !p-0" href="{{ route('dispositivos.index') }}" />
        <h1 class="text-2xl font-bold text-zinc-900">Editar Dispositivo</h1>
    </div>

    <form wire:submit="save" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Column 1: General Info -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-zinc-200 space-y-6 shadow-sm">
                    <h2 class="text-sm font-semibold text-zinc-900 uppercase tracking-wider mb-4">Datos del Equipo</h2>

                    <!-- Modelo Selector -->
                    <div class="relative">
                        <flux:label>Modelo del Dispositivo</flux:label>
                        @if ($modelo_id)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 rounded-lg border border-zinc-200 mt-2">
                                <span class="font-medium text-zinc-900">{{ $modelo_selected_name }}</span>
                                <button type="button" wire:click="clearModelo" class="text-red-500 hover:bg-red-50 p-1 rounded">
                                    <flux:icon name="x-mark" class="w-4 h-4" />
                                </button>
                            </div>
                        @else
                            <div class="relative mt-2">
                                <flux:input type="text" wire:model.live.debounce.300ms="modelo_search" placeholder="Buscar marca o modelo (ej. iPhone)..." />
                                @if (!empty($modelos) && count($modelos) > 0)
                                    <div class="absolute z-20 w-full mt-1 bg-white border border-zinc-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        @foreach ($modelos as $modelo)
                                            <button type="button" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-sm text-zinc-700 block"
                                                wire:click="selectModelo({{ $modelo->id }}, '{{ $modelo->marca }} {{ $modelo->modelo }}')">
                                                {{ $modelo->marca }} <strong>{{ $modelo->modelo }}</strong>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                        <flux:error name="modelo_id" />
                    </div>

                    <!-- Cliente Selector -->
                    <div class="relative">
                        <flux:label>Cliente (Dueño)</flux:label>
                        @if ($cliente_id)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 rounded-lg border border-zinc-200 mt-2">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                        {{ substr($cliente_selected_name, 0, 2) }}
                                    </div>
                                    <span class="font-medium text-zinc-900">{{ $cliente_selected_name }}</span>
                                </div>
                                <button type="button" wire:click="clearCliente" class="text-red-500 hover:bg-red-50 p-1 rounded">
                                    <flux:icon name="x-mark" class="w-4 h-4" />
                                </button>
                            </div>
                        @else
                            <div class="relative mt-2">
                                <flux:input type="text" wire:model.live.debounce.300ms="cliente_search" placeholder="Buscar cliente por nombre..." />
                                @if (!empty($clientes) && count($clientes) > 0)
                                    <div class="absolute z-20 w-full mt-1 bg-white border border-zinc-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        @foreach ($clientes as $cliente)
                                            <button type="button" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-sm block"
                                                wire:click="selectCliente({{ $cliente->id }}, '{{ $cliente->nombre }}')">
                                                <div class="font-medium text-zinc-900">{{ $cliente->nombre }}</div>
                                                <div class="text-xs text-zinc-500">{{ $cliente->rut }}</div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                        <flux:error name="cliente_id" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input label="IMEI / Serie" placeholder="3528..." wire:model="imei" />
                        <flux:input label="Color" placeholder="Negro..." wire:model="color" />
                    </div>

                    <flux:textarea label="Estado Físico / Detalles" placeholder="Rayas en pantalla, golpe en esquina..." wire:model="estado_dispositivo" rows="3" />
                </div>
            </div>

            <!-- Column 2: Security & Accessories -->
            <div class="space-y-6">

                <!-- Security Box -->
                <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
                    <h3 class="text-sm font-semibold text-zinc-900 mb-4 flex items-center gap-2">
                        <flux:icon name="lock-closed" class="w-4 h-4" /> Seguridad
                    </h3>

                    <div class="flex gap-4 mb-6">
                        <flux:radio.group wire:model.live="tipo_bloqueo" class="flex gap-4">
                            <flux:radio value="ninguno" label="Ninguno" />
                            <flux:radio value="contrasena" label="PIN/Pass" />
                            <flux:radio value="patron" label="Patrón" />
                        </flux:radio.group>
                    </div>

                    @if ($tipo_bloqueo === 'contrasena')
                        <div class="animate-in fade-in slide-in-from-top-2">
                            <flux:input label="Contraseña / PIN" placeholder="1234..." wire:model="contraseña" />
                        </div>
                    @endif

                    @if ($tipo_bloqueo === 'patron')
                        <div class="flex flex-col items-center animate-in fade-in slide-in-from-top-2"
                             x-data="{
                                path: @entangle('patron'),
                                isDrawing: false,
                                getCoordinates(index) {
                                    const row = Math.floor((index - 1) / 3);
                                    const col = (index - 1) % 3;
                                    const x = 25 + col * 25;
                                    const y = 25 + row * 25;
                                    return { x: `${x}%`, y: `${y}%` };
                                },
                                getPoints() {
                                    return this.path.map(i => {
                                        const row = Math.floor((i - 1) / 3);
                                        const col = (i - 1) % 3;
                                        const centers = [32, 96, 160];
                                        return `${centers[col]},${centers[row]}`;
                                    }).join(' ');
                                },
                                handleStart(index) {
                                    this.isDrawing = true;
                                    this.path = [index];
                                },
                                handleMove(e) {
                                    if (!this.isDrawing) return;
                                    let clientX, clientY;
                                    if (e.touches) {
                                        clientX = e.touches[0].clientX;
                                        clientY = e.touches[0].clientY;
                                    } else {
                                        clientX = e.clientX;
                                        clientY = e.clientY;
                                    }
                                    const element = document.elementFromPoint(clientX, clientY);
                                    const id = element?.getAttribute('data-id');
                                    if (id) {
                                        const point = parseInt(id);
                                        if (!this.path.includes(point)) {
                                            this.path.push(point);
                                        }
                                    }
                                },
                                handleEnd() {
                                    this.isDrawing = false;
                                }
                             }"
                        >
                            <p class="text-xs text-zinc-500 mb-3">Dibuja el patrón de desbloqueo:</p>

                            <div
                                class="relative h-48 w-48 bg-zinc-50 rounded-lg border border-zinc-200 select-none touch-none mx-auto"
                                @mouseup="handleEnd"
                                @mouseleave="handleEnd"
                                @touchend="handleEnd"
                            >
                                <svg class="absolute inset-0 h-full w-full pointer-events-none z-10 text-emerald-600 dark:text-emerald-500" viewBox="0 0 192 192">
                                    <polyline
                                        :points="getPoints()"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>

                                <div
                                    class="grid grid-cols-3 gap-6 h-full w-full items-center justify-items-center p-6"
                                    @mousemove="handleMove"
                                    @touchmove.prevent="handleMove"
                                >
                                    @foreach (range(1, 9) as $i)
                                        <div
                                            key="{{ $i }}"
                                            data-id="{{ $i }}"
                                            @mousedown="handleStart({{ $i }})"
                                            @touchstart.prevent="handleStart({{ $i }})"
                                            class="h-10 w-10 rounded-full border-2 transition-all duration-200 cursor-pointer z-20 flex items-center justify-center text-xs font-semibold"
                                            :class="path.includes({{ $i }}) ? 'bg-emerald-600 border-emerald-800 dark:bg-emerald-500 dark:border-emerald-700 text-white scale-125' : 'bg-white dark:bg-zinc-800 border-zinc-400 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700'"
                                        >
                                            {{ $i }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <p class="text-xs text-zinc-500 mb-2">Patrón dibujado:</p>
                                <div class="flex items-center justify-center gap-2 flex-wrap" x-show="path.length > 0">
                                    <template x-for="(num, index) in path" :key="index">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 text-sm font-semibold" x-text="num"></span>
                                    </template>
                                </div>
                                <p class="text-xs text-zinc-400 mt-2" x-show="path.length === 0">Dibuja para registrar</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Accessories Box -->
                <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
                    <h3 class="text-sm font-semibold text-zinc-900 mb-4 flex items-center gap-2">
                        <flux:icon name="briefcase" class="w-4 h-4" /> Accesorios Recibidos
                    </h3>

                    <div class="grid grid-cols-2 gap-3">
                        @forelse($accesoriosDisponibles as $acc)
                            <flux:checkbox wire:model="accesoriosSeleccionados.{{ $acc['clave'] }}" label="{{ $acc['nombre'] }}" />
                        @empty
                            <p class="text-sm text-zinc-500 col-span-2">Sin accesorios configurados.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-zinc-200 gap-3">
            <flux:button variant="ghost" href="{{ route('dispositivos.index') }}">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Actualizar Dispositivo</flux:button>
        </div>
    </form>
</div>

