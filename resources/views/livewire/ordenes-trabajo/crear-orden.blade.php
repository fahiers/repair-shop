<div class="max-w-screen-2xl mx-auto px-4 py-3 md:px-6 md:py-4 lg:px-8 lg:py-6">
    <div class="flex flex-col gap-3 mb-5">
        <div class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm p-3 md:p-4">
            <div class="flex flex-wrap items-center gap-2 md:gap-3">
                <a href="{{ route('ordenes-trabajo.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700" title="Volver a las órdenes">
                    <flux:icon.arrow-left class="size-4" />
                    <span class="sr-only">Volver</span>
                </a>

                <div class="flex items-center gap-2 min-w-[180px]">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <flux:icon.wrench-screwdriver class="size-4" />
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-zinc-500">Orden</p>
                        <p class="text-sm md:text-base font-semibold text-zinc-900 dark:text-zinc-100">Nueva orden</p>
                    </div>
                </div>

                <div class="flex items-end gap-2 flex-wrap text-sm">
                    <div class="flex flex-col min-w-[150px]">
                        <span class="text-xs uppercase tracking-wide text-zinc-500">Ingreso</span>
                        <input
                            type="date"
                            wire:model.live="fechaIngreso"
                            class="mt-1 h-9 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                    </div>
                    <div class="flex flex-col min-w-[150px]">
                        <span class="text-xs uppercase tracking-wide text-zinc-500">Entrega estimada</span>
                        <input
                            type="date"
                            wire:model.live="fechaEntregaEstimada"
                            class="mt-1 h-9 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                    </div>
                </div>

                <div class="flex items-center gap-2 min-w-[180px]">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 font-medium">
                        {{ strtoupper(mb_substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="text-sm leading-tight">
                        <span class="block text-xs uppercase tracking-wide text-zinc-500">Responsable</span>
                        <span class="block font-medium text-zinc-900 dark:text-zinc-100">{{ auth()->user()?->name ?? 'Usuario' }}</span>
                    </div>
                </div>

                <div class="flex items-end gap-2 ms-auto">
                    <div class="flex flex-col min-w-[150px]">
                        <span class="text-xs uppercase tracking-wide text-zinc-500">Estado</span>
                        <select
                            wire:model.live="estado"
                            class="mt-1 h-9 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            @foreach($estadosDisponibles as $valor => $label)
                                <option value="{{ $valor }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <flux:dropdown position="bottom-end">
                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700" title="Opciones">
                            <flux:icon.ellipsis-vertical class="size-4" />
                            <span class="sr-only">Opciones</span>
                        </button>

                        <flux:menu>
                            <div class="px-3 py-2 text-sm text-zinc-500">Sin opciones disponibles por ahora</div>
                        </flux:menu>
                    </flux:dropdown>

                    <button disabled class="px-3 py-1.5 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">Ingresar pago</button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[3fr_2fr] gap-6 items-start">
        <!-- Panel izquierdo: detalles y conceptos -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
            <!-- Sección de cliente -->
            <div class="p-4 md:p-6 border-b border-zinc-100 dark:border-zinc-700">
                <div class="space-y-3">
                    <div class="relative">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Buscar cliente</label>
                            <button type="button" wire:click="abrirModalCrearCliente" class="text-xs px-2 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 flex items-center gap-1">
                                <flux:icon.plus class="size-3" />
                                Nuevo cliente
                            </button>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="clientSearchTerm"
                            wire:keydown.escape="clearClientSearchResults"
                            wire:blur="$dispatch('clientSearchBlurred')"
                            wire:focus="$dispatch('ignoreBlur')"
                            placeholder="Buscar por nombre, teléfono, email o RUT..."
                            autocomplete="off"
                            class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                        @error('selectedClientId')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        @if($showClientSearchResults)
                            @if($loadingClients)
                                <div class="absolute z-20 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-md mt-1 px-3 py-2 text-sm text-zinc-500">
                                    Buscando...
                                </div>
                            @elseif(count($clientsFound) > 0)
                                <ul class="absolute z-20 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                    @foreach($clientsFound as $foundClient)
                                        <li
                                            wire:mousedown="$dispatch('ignoreBlur')"
                                            wire:click="selectClient({{ $foundClient['id'] }})"
                                            wire:mouseup="$dispatch('processBlur')"
                                            class="px-3 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer"
                                        >
                                            {{ $foundClient['nombre'] }}
                                            @if(isset($foundClient['rut']))
                                                <span class="text-zinc-400 text-xs italic ml-2">- {{ $foundClient['rut'] }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="absolute z-20 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-md mt-1 px-3 py-2 text-sm text-zinc-500">
                                    No se encontraron clientes.
                                </div>
                            @endif
                        @endif
                    </div>

                    @if($clienteSeleccionado)
                        <div class="flex items-start justify-between gap-3 flex-wrap pt-2">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-sm font-medium">
                                    {{ strtoupper(mb_substr($clienteSeleccionado['nombre'], 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium">{{ $clienteSeleccionado['nombre'] }}</p>
                                    <div class="flex items-center gap-3 text-sm text-zinc-500">
                                        <span>{{ $clienteSeleccionado['telefono'] }}</span>
                                        @if($clienteSeleccionado['email'])
                                            <span>{{ $clienteSeleccionado['email'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button type="button" wire:click="limpiarCliente" class="px-2 py-1.5 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700">Cambiar cliente</button>
                        </div>

                    @endif

                </div>
            </div>

            <div class="p-4 md:p-6 space-y-4 dark:text-zinc-100">
                <!-- Asunto / descripción corta con tipo de servicio en la misma fila del label -->
                <div class="flex items-center justify-between gap-3 md:gap-4">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Falla o requerimiento</label>
                    <div class="flex items-center gap-4 text-sm">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="tipo" value="reparacion" wire:model.live="tipoServicio" class="h-4 w-4 text-emerald-600 border-zinc-300 dark:border-zinc-600">
                            <span>Reparación</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="tipo" value="mantenimiento" wire:model.live="tipoServicio" class="h-4 w-4 text-emerald-600 border-zinc-300 dark:border-zinc-600">
                            <span>Mantenimiento</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="tipo" value="garantia" wire:model.live="tipoServicio" class="h-4 w-4 text-emerald-600 border-zinc-300 dark:border-zinc-600">
                            <span>Garantía</span>
                        </label>
                    </div>
                </div>

                <input
                    type="text"
                    wire:model.blur="asunto"
                    placeholder="Ej: Cambio de pantalla iPhone"
                    class="w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                @error('asunto')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                @if(strlen($asunto) > 0 && strlen($asunto) < 3)
                    <p class="text-xs text-red-600 mt-1">Ingrese al menos 3 caracteres.</p>
                @endif

                <!-- Tabla conceptos -->
                <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400">
                            <tr>
                                <th class="text-left font-medium p-3">Servicio o producto</th>
                                <th class="text-right font-medium p-3">Cant</th>
                                <th class="text-right font-medium p-3">Precio</th>
                                <th class="text-right font-medium p-3">Desc %</th>
                                <th class="text-right font-medium p-3">Total</th>
                                <th class="p-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr class="border-t border-zinc-200 dark:border-zinc-700" wire:key="item-{{ $index }}">
                                    <td class="p-3">
                                        {{ $item['nombre'] }}
                                        <span class="text-xs text-zinc-500">
                                            ({{ $item['tipo'] === 'servicio' ? 'Servicio' : 'Producto' }})
                                        </span>
                                    </td>
                                    <td class="p-3 text-right">
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.300ms="items.{{ $index }}.cantidad"
                                            wire:change="actualizarCantidad({{ $index }}, $event.target.value)"
                                            class="w-16 px-2 py-1 text-right border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 dark:text-zinc-100"
                                            min="1"
                                        >
                                    </td>
                                    <td class="p-3 text-right">
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.300ms="items.{{ $index }}.precio"
                                            wire:change="actualizarPrecio({{ $index }}, $event.target.value)"
                                            class="w-24 px-2 py-1 text-right border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 dark:text-zinc-100"
                                            min="0"
                                            step="0.01"
                                        >
                                    </td>
                                    <td class="p-3 text-right">
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.300ms="items.{{ $index }}.descuento"
                                            wire:change="actualizarDescuento({{ $index }}, $event.target.value)"
                                            class="w-16 px-2 py-1 text-right border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 dark:text-zinc-100"
                                            min="0"
                                            max="100"
                                        >
                                    </td>
                                    <td class="p-3 text-right font-medium">
                                        ${{ number_format($this->calcularSubtotalItem($item), 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" wire:click="eliminarItem({{ $index }})" wire:loading.attr="disabled" class="px-2 py-1 text-xs rounded-md border border-zinc-200 dark:border-zinc-700 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-zinc-500">
                                        No hay servicios o productos agregados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-2">
                    <button type="button" wire:click="abrirModalAgregarItem('servicio')" class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                        Agregar servicio o producto
                    </button>
                    @error('items')
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Totales -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-2">
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Descuento</p>
                        <p class="text-lg font-semibold text-red-600 dark:text-red-500">
                            $ -{{ number_format($totalDescuentos, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Subtotal</p>
                        <p class="text-lg font-semibold">
                            $ {{ number_format($subtotalConDescuento, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">IVA ({{ $porcentajeIva }}%)</p>
                        <p class="text-lg font-semibold">
                            $ {{ number_format($montoIva, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-emerald-200 dark:border-emerald-900 p-4 bg-emerald-50 dark:bg-emerald-950">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Total</p>
                        <p class="text-lg font-semibold text-emerald-600">
                            $ {{ number_format($total, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Anticipo y Saldo -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                        <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-2">Anticipo</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">$</span>
                            <input
                                type="number"
                                wire:model.live.debounce.300ms="anticipo"
                                min="0"
                                step="0.01"
                                max="999999.99"
                                class="flex-1 px-3 py-2 text-sm rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="0.00"
                            >
                        </div>
                        @error('anticipo')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="rounded-lg border border-amber-200 dark:border-amber-900 p-4 bg-amber-50 dark:bg-amber-950">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Saldo pendiente</p>
                        <p class="text-lg font-semibold text-amber-600 dark:text-amber-500">
                            $ {{ number_format($this->calcularSaldo(), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mini panel: acciones inferiores del panel izquierdo -->
            <div class="border-t border-zinc-100 dark:border-zinc-700 p-4 md:p-6">
                <div class="space-y-3">
                    <!-- Mensajes de error generales -->
                    @if($errors->hasAny(['selectedClientId', 'selectedDeviceId', 'items']))
                        <div class="rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3">
                            <div class="flex items-start gap-2">
                                <flux:icon.exclamation-triangle class="size-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" />
                                <div class="flex-1 space-y-1">
                                    @error('selectedClientId')
                                        <p class="text-sm text-red-800 dark:text-red-300">{{ $message }}</p>
                                    @enderror
                                    @error('selectedDeviceId')
                                        <p class="text-sm text-red-800 dark:text-red-300">{{ $message }}</p>
                                    @enderror
                                    @error('items')
                                        <p class="text-sm text-red-800 dark:text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-start gap-2 md:gap-3">
                        <button disabled class="px-3 py-1.5 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 disabled:opacity-50">Imprimir orden</button>
                        <button disabled class="px-3 py-1.5 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 disabled:opacity-50">Imprimir etiqueta</button>
                        <button type="button"
                                wire:click="guardarOrden"
                                @disabled(!$dispositivoSeleccionado || strlen($asunto) < 3)
                                title="{{ !$dispositivoSeleccionado ? 'Seleccione un dispositivo' : (strlen($asunto) < 3 ? 'Ingrese una descripción (mín. 3 caracteres)' : 'Guardar orden') }}"
                                class="px-3 py-1.5 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
                            Guardar orden
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel derecho: acciones y pestañas -->
        <div class="lg:sticky lg:top-6 lg:self-start lg:max-h-[calc(100vh-3rem)]" x-data="{ activeTab: 'equipo' }">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm lg:flex lg:flex-col lg:max-h-[calc(100vh-3rem)]">
                <!-- Botones de acciones en el header -->
                <div class="p-4 md:p-6 border-b border-zinc-100 dark:border-zinc-700">
                    <div class="flex items-center gap-2 md:gap-3 flex-wrap w-full justify-between">
                        <!-- Checklist de progreso -->
                        <div class="flex items-center gap-3 text-xs">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded border {{ $selectedClientId ? 'border-emerald-300 text-emerald-700 bg-emerald-50' : 'border-zinc-200 text-zinc-600' }}">
                                <flux:icon.check class="size-3" /> Cliente
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded border {{ $dispositivoSeleccionado ? 'border-emerald-300 text-emerald-700 bg-emerald-50' : 'border-zinc-200 text-zinc-600' }}">
                                <flux:icon.check class="size-3" /> Dispositivo
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded border {{ strlen($asunto) >= 3 ? 'border-emerald-300 text-emerald-700 bg-emerald-50' : 'border-zinc-200 text-zinc-600' }}">
                                <flux:icon.check class="size-3" /> Problema reportado
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded border {{ count($items) > 0 ? 'border-emerald-300 text-emerald-700 bg-emerald-50' : 'border-zinc-200 text-zinc-600' }}">
                                <flux:icon.check class="size-3" /> Items
                            </span>
                        </div>
                        
                        
                    </div>
                </div>
                
                <!-- Pestañas -->
                <div class="p-4 md:p-6 border-b border-zinc-100 dark:border-zinc-700">
                    <div class="flex items-center gap-2 md:gap-4 flex-wrap">
                        <button @click="activeTab = 'equipo'"
                                :class="activeTab === 'equipo' ? 'font-semibold text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'"
                                class="px-3 py-1.5 text-sm transition-colors">
                            Equipo
                        </button>
                        <button @click="activeTab = 'notas'"
                                :class="activeTab === 'notas' ? 'font-semibold text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'"
                                class="px-3 py-1.5 text-sm transition-colors">
                            Notas
                        </button>
                    </div>
                </div>

                <!-- Contenido de la pestaña Equipo -->
                <div x-show="activeTab === 'equipo'" class="p-4 md:p-6 lg:overflow-y-auto lg:flex-1 lg:min-h-0" @keydown.window.ctrl.s.prevent="$wire.guardarOrden()" @keydown.window.escape="$wire.set('mostrarModalCrearDispositivo', false)">
                    <!-- Estado: dispositivo seleccionado -->
                    @if($dispositivoSeleccionado)
                        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 mb-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
                                        <flux:icon.device-phone-mobile class="size-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold truncate">
                                            @if($dispositivoSeleccionado['modelo'])
                                                {{ $dispositivoSeleccionado['modelo']['marca'] }} {{ $dispositivoSeleccionado['modelo']['modelo'] }}
                                                @if($dispositivoSeleccionado['modelo']['anio']) ({{ $dispositivoSeleccionado['modelo']['anio'] }}) @endif
                                            @else
                                                Sin modelo
                                            @endif
                                        </p>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                            @if($dispositivoSeleccionado['imei']) N° Serie/IMEI: {{ $dispositivoSeleccionado['imei'] }} @endif
                                            @if($dispositivoSeleccionado['color'])
                                                <span class="mx-1">•</span> Color: {{ $dispositivoSeleccionado['color'] }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="abrirModalEditarDispositivo" class="px-3 py-1.5 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Editar dispositivo</button>
                                    <button type="button" wire:click="limpiarDispositivo" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800" title="Quitar">
                                        <flux:icon.minus class="size-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @error('selectedDeviceId')
                        <div class="mt-2 rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3">
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        </div>
                    @enderror

                    <!-- Estado: con cliente seleccionado y SIN dispositivo seleccionado -->
                    @if($selectedClientId && !$dispositivoSeleccionado)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold">Dispositivos de {{ $clienteSeleccionado['nombre'] ?? 'cliente seleccionado' }}</h3>
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="abrirModalCrearDispositivo('rapido')" class="px-3 py-1.5 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Agregar nuevo dispositivo</button>
                                </div>
                            </div>

                            

                            @if(count($dispositivosCliente) > 0)
                                <div class="space-y-2">
                                    @foreach($dispositivosCliente as $dc)
                                        <div class="flex items-center justify-between border border-zinc-200 dark:border-zinc-700 rounded-md px-3 py-2">
                                            <div>
                                                <p class="font-medium">
                                                    @if($dc['modelo'])
                                                        {{ $dc['modelo']['marca'] }} {{ $dc['modelo']['modelo'] }}
                                                        @if($dc['modelo']['anio']) ({{ $dc['modelo']['anio'] }}) @endif
                                                    @else
                                                        Sin modelo
                                                    @endif
                                                </p>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-300">
                                                    @if($dc['imei']) IMEI {{ $dc['imei'] }} @endif
                                                    @if($dc['color']) <span class="ml-2">Color {{ $dc['color'] }}</span> @endif
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if($suggestedDeviceId && $suggestedDeviceId === $dc['id'] && !$dispositivoSeleccionado)
                                                    <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-700">Sugerido</span>
                                                @endif
                                                <button type="button" wire:click="selectDevice({{ $dc['id'] }})" class="px-3 py-1.5 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Seleccionar</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-zinc-500">Este cliente no tiene dispositivos registrados.</p>
                            @endif
                        </div>
                    @else
                        <!-- Estado: sin cliente seleccionado -->
                        <div class="space-y-4">
                            <p class="text-zinc-500 dark:text-zinc-400">
                                Seleccione primero un cliente o busque un dispositivo existente.
                            </p>
                            

                            <div>
                                <button type="button" wire:click="abrirModalCrearDispositivo('rapido')" class="px-3 py-1.5 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Crear dispositivo nuevo</button>
                            </div>
                        </div>
                    @endif

                    <!-- Accesorios y estado del equipo (tab Equipo) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Accesorios (dinámico) -->
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                            <h4 class="font-semibold mb-3">Accesorios</h4>
                            <div class="space-y-2 text-sm">
                                @forelse($accesoriosDisponibles as $acc)
                                    <label class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            wire:click="toggleAccesorio('{{ $acc['clave'] }}')"
                                            @checked(isset($accesoriosSeleccionados[$acc['clave']]) && $accesoriosSeleccionados[$acc['clave']])
                                            @disabled(!$dispositivoSeleccionado)
                                            class="h-4 w-4 rounded border-zinc-300 dark:border-zinc-600 text-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                        <span>{{ $acc['nombre'] }}</span>
                                    </label>
                                @empty
                                    <p class="text-zinc-500 dark:text-zinc-400">Sin accesorios configurados.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Estado del equipo -->
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                            <h4 class="font-semibold mb-3">Estado del equipo</h4>
                            <textarea 
                                rows="6" 
                                wire:model.live.debounce.500ms="observacionesDispositivo"
                                @disabled(!$dispositivoSeleccionado)
                                class="w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed" 
                                placeholder="{{ $dispositivoSeleccionado ? 'Detalles visibles: rayas, golpes, pantalla, conectores, etc.' : 'Seleccione un dispositivo primero' }}"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Contenido de la pestaña Notas -->
                <div x-show="activeTab === 'notas'" class="p-4 md:p-6 lg:overflow-y-auto lg:flex-1 lg:min-h-0">
                    <p class="text-zinc-500 dark:text-zinc-400">Contenido de notas aquí...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar items -->
    @if($mostrarModalAgregarItem)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" aria-hidden="true"></div>
            <div class="relative w-full max-w-lg sm:max-w-xl mx-4 sm:mx-0 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 shadow-xl max-h-[85vh] overflow-y-auto">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold">Agregar {{ $tipoItemAgregar === 'servicio' ? 'servicio' : 'producto' }}</h2>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Busque y seleccione el {{ $tipoItemAgregar }} que desea agregar a la orden.</p>
                    </div>

                    <!-- Alternar entre servicio y producto -->
                    <div class="flex gap-2">
                        <button type="button" wire:click="$set('tipoItemAgregar', 'servicio')" class="px-3 py-1.5 text-sm rounded-md border {{ $tipoItemAgregar === 'servicio' ? 'bg-emerald-600 text-white border-emerald-600' : 'border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">Servicios</button>
                        <button type="button" wire:click="$set('tipoItemAgregar', 'producto')" class="px-3 py-1.5 text-sm rounded-md border {{ $tipoItemAgregar === 'producto' ? 'bg-emerald-600 text-white border-emerald-600' : 'border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">Productos</button>
                    </div>

                    <input
                        type="text"
                        wire:model.live.debounce.300ms="busquedaItem"
                        placeholder="Buscar {{ $tipoItemAgregar }}..."
                        class="w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >

                    <div class="max-h-96 overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
                        @if(strlen($busquedaItem) >= 2)
                            @forelse($itemsDisponibles as $item)
                                <button
                                    type="button"
                                    wire:click="agregarItem({{ $item['id'] }})"
                                    class="w-full px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors border-b border-zinc-200 dark:border-zinc-700 last:border-b-0"
                                    wire:loading.attr="disabled"
                                    wire:target="agregarItem({{ $item['id'] }})"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium">{{ $item['nombre'] }}</p>
                                            @if($tipoItemAgregar === 'producto')
                                                <p class="text-sm text-zinc-500">
                                                    {{ $item['marca'] }} • Stock: {{ $item['stock'] }}
                                                </p>
                                            @endif
                                            @if($item['descripcion'])
                                                <p class="text-sm text-zinc-500 mt-1">{{ Str::limit($item['descripcion'], 60) }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold">
                                                ${{ number_format($tipoItemAgregar === 'servicio' ? $item['precio_base'] : $item['precio_venta'], 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </button>
                            @empty
                                <div class="p-6 text-center text-zinc-500">
                                    No se encontraron {{ $tipoItemAgregar === 'servicio' ? 'servicios' : 'productos' }}
                                </div>
                            @endforelse
                        @else
                            <div class="p-6 text-center text-zinc-500">
                                Escriba al menos 2 caracteres para buscar
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end">
                        <button type="button" wire:click="$set('mostrarModalAgregarItem', false)" class="px-3 py-2 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal unificado de creación/edición de dispositivo -->
    @if($mostrarModalCrearDispositivo || $mostrarModalEditarDispositivo)
        @php
            $esModoEdicion = $mostrarModalEditarDispositivo;
            $nombreVariableModal = $esModoEdicion ? 'mostrarModalEditarDispositivo' : 'mostrarModalCrearDispositivo';
            $metodoGuardar = $esModoEdicion ? 'guardarEdicionDispositivo' : 'crearDispositivoRapido';
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" aria-hidden="true"></div>
            <div class="relative w-full max-w-lg sm:max-w-xl mx-4 sm:mx-0 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 shadow-xl max-h-[85vh] overflow-y-auto">
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $esModoEdicion ? 'Editar dispositivo' : 'Crear dispositivo' }}</h2>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                            @if($esModoEdicion)
                                Actualice los datos del equipo.
                            @else
                                @if($selectedClientId)
                                    Este dispositivo se asociará automáticamente al cliente {{ $clienteSeleccionado['nombre'] ?? '' }}.
                                @else
                                    Puede crear el dispositivo sin cliente asociado.
                                @endif
                            @endif
                        </p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Modelo (marca, modelo o año) <span class="text-red-500">*</span></label>
                            <button type="button" wire:click="abrirModalCrearModelo" class="text-xs px-2 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Nuevo modelo</button>
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="modeloSearchTerm"
                               placeholder="Ej: Samsung A52 2021"
                               class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">

                        @error('modeloSeleccionadoId')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror

                        @if(strlen($modeloSearchTerm) >= 2 && count($modelosFound) > 0)
                            <ul class="mt-2 max-h-48 overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-md">
                                @foreach($modelosFound as $m)
                                    <li>
                                        <button type="button" class="w-full text-left px-3 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-700"
                                                wire:click="selectModelo({{ $m['id'] }})">
                                            {{ $m['marca'] }} {{ $m['modelo'] }} @if($m['anio']) ({{ $m['anio'] }}) @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if($modeloSeleccionadoId)
                            <p class="mt-2 text-xs text-emerald-600">Modelo seleccionado.</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">IMEI (opcional)</label>
                            <input type="text" wire:model.live="imeiDispositivo" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm">
                            @error('imeiDispositivo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Color (opcional)</label>
                            <input type="text" wire:model.live="colorDispositivo" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm">
                            @error('colorDispositivo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Estado del dispositivo (opcional)</label>
                        <textarea wire:model.live="observacionesDispositivo" rows="3" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm" placeholder="Detalles visibles: rayas, golpes, pantalla, conectores, etc."></textarea>
                        @error('observacionesDispositivo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" wire:click="$set('{{ $nombreVariableModal }}', false)" class="px-3 py-2 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Cancelar</button>
                        <button type="button" wire:click="{{ $metodoGuardar }}" @disabled(!$modeloSeleccionadoId) class="px-3 py-2 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
                            {{ $esModoEdicion ? 'Guardar' : 'Crear y seleccionar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal: Crear nuevo modelo -->
    <div x-data="{ open: @entangle('mostrarModalCrearModelo') }" x-cloak x-show="open" class="fixed inset-0 z-50 flex items-center justify-center">
        <div x-show="open" @click="open = false" class="absolute inset-0 bg-black/50" x-transition.opacity aria-hidden="true"></div>
        <div x-show="open"
             @keydown.window.escape="open = false"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             class="relative w-full max-w-md sm:max-w-lg mx-4 sm:mx-0 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 shadow-xl max-h-[85vh] overflow-y-auto">
            <div class="space-y-4">
                <div>
                    <h2 class="text-lg font-semibold">Nuevo modelo de dispositivo</h2>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Cree un modelo y se seleccionará automáticamente.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Marca <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="modeloNuevoMarca" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm">
                        @error('modeloNuevoMarca') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Modelo <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="modeloNuevoModelo" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm">
                        @error('modeloNuevoModelo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Año</label>
                        <input type="number" wire:model.live="modeloNuevoAnio" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm">
                        @error('modeloNuevoAnio') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Descripción</label>
                        <input type="text" wire:model.live="modeloNuevoDescripcion" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm">
                        @error('modeloNuevoDescripcion') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button type="button" @click="open = false" class="px-3 py-2 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Cancelar</button>
                    <button type="button" wire:click="crearModeloRapido" class="px-3 py-2 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700">Crear y seleccionar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal: Crear nuevo cliente -->
    @if($mostrarModalCrearCliente)
        <div class="fixed inset-0 z-50 flex items-center justify-center" @keydown.window.escape="$wire.set('mostrarModalCrearCliente', false)">
            <div class="absolute inset-0 bg-black/50" aria-hidden="true" wire:click="$set('mostrarModalCrearCliente', false)"></div>
            <div class="relative w-full max-w-md sm:max-w-lg mx-4 sm:mx-0 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 shadow-xl max-h-[85vh] overflow-y-auto">
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold">Nuevo cliente</h2>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Cree un cliente rápidamente. Solo el nombre es obligatorio.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="clienteNuevoNombre" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Ej: Juan Pérez">
                        @error('clienteNuevoNombre') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Teléfono</label>
                            <input type="text" wire:model.live="clienteNuevoTelefono" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Ej: +56 9 1234 5678">
                            @error('clienteNuevoTelefono') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">RUT</label>
                            <input type="text" wire:model.live="clienteNuevoRut" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Ej: 12.345.678-9">
                            @error('clienteNuevoRut') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Email</label>
                        <input type="email" wire:model.live="clienteNuevoEmail" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Ej: juan@ejemplo.com">
                        @error('clienteNuevoEmail') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Dirección</label>
                        <input type="text" wire:model.live="clienteNuevoDireccion" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Ej: Av. Principal 123">
                        @error('clienteNuevoDireccion') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" wire:click="$set('mostrarModalCrearCliente', false)" class="px-3 py-2 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Cancelar</button>
                        <button type="button" wire:click="crearClienteRapido" @disabled(empty(trim($clienteNuevoNombre))) class="px-3 py-2 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
                            Crear y seleccionar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Toast de éxito al actualizar equipo -->
    <div x-data="{ open: @entangle('mostrarToastEquipoActualizado') }" x-show="open" x-transition.opacity class="fixed bottom-4 right-4 z-50">
        <div x-init="$watch('open', value => { if(value){ setTimeout(() => open = false, 2500) } })" class="flex items-center gap-2 px-4 py-2 rounded-md bg-emerald-600 text-white shadow-lg">
            <flux:icon.check class="size-4" />
            <span class="text-sm font-medium">Equipo actualizado correctamente</span>
        </div>
    </div>

</div>

