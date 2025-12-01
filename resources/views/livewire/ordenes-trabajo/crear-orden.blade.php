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
                    <div class="flex flex-col min-w-[150px] relative">
                        <span class="text-xs uppercase tracking-wide text-zinc-500">Entrega estimada</span>
                        <input
                            type="date"
                            wire:model.live="fechaEntregaEstimada"
                            class="mt-1 h-9 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('fechaEntregaEstimada') border-red-500 focus:ring-red-500 @enderror"
                        >
                        @error('fechaEntregaEstimada')
                            <p class="text-[10px] text-red-600 dark:text-red-400 absolute -bottom-4 left-0 w-full truncate" title="{{ $message }}">{{ $message }}</p>
                        @enderror
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
                            <div class="flex items-center gap-2">
                                @if($selectedClientId)
                                    <button type="button" wire:click="limpiarCliente" class="text-xs px-2 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Cambiar cliente</button>
                                @endif
                                <button type="button" wire:click="abrirModalCrearCliente" class="text-xs px-2 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 flex items-center gap-1">
                                    <flux:icon.plus class="size-3" />
                                    Nuevo cliente
                                </button>
                            </div>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="clientSearchTerm"
                            wire:keydown.escape="clearClientSearchResults"
                            wire:blur="$dispatch('clientSearchBlurred')"
                            wire:focus="mostrarClientesAlFocus"
                            wire:mousedown="$dispatch('ignoreBlur')"
                            placeholder="Buscar por nombre, teléfono, email o RUT..."
                            autocomplete="off"
                            @if($selectedClientId) readonly @endif
                            class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 @if($selectedClientId) bg-zinc-50 dark:bg-zinc-800 cursor-not-allowed @endif"
                        >
                        @error('selectedClientId')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        @if($showClientSearchResults && !$selectedClientId)
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
                                    <div class="flex items-center gap-3 text-sm text-zinc-500 mb-1">
                                        <span>{{ $clienteSeleccionado['telefono'] }}</span>
                                        @if($clienteSeleccionado['email'])
                                            <span>{{ $clienteSeleccionado['email'] }}</span>
                                        @endif
                                    </div>
                                    
                                    {{-- Indicadores de historial del cliente --}}
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ ($clienteSeleccionado['total_dispositivos'] ?? 0) > 1 ? 'bg-blue-50 text-blue-700 ring-blue-700/10 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-zinc-50 text-zinc-600 ring-zinc-500/10 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                            <flux:icon.device-phone-mobile class="size-3" />
                                            {{ $clienteSeleccionado['total_dispositivos'] ?? 0 }} Dispositivo{{ ($clienteSeleccionado['total_dispositivos'] ?? 0) !== 1 ? 's' : '' }}
                                        </span>
                                        
                                        @if(($clienteSeleccionado['total_ordenes'] ?? 0) > 0)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10 dark:bg-purple-900/30 dark:text-purple-400">
                                                <flux:icon.clipboard-document-list class="size-3" />
                                                {{ $clienteSeleccionado['total_ordenes'] }} Orden{{ ($clienteSeleccionado['total_ordenes'] ?? 0) !== 1 ? 'es' : '' }} previa{{ ($clienteSeleccionado['total_ordenes'] ?? 0) !== 1 ? 's' : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
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
                                        {{ Number::currency($this->calcularSubtotalItem($item), precision: 0) }}
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" wire:click="eliminarItem({{ $index }})" wire:confirm="¿Está seguro de que desea eliminar este {{ $item['tipo'] === 'servicio' ? 'servicio' : 'producto' }}?" wire:loading.attr="disabled" class="px-2 py-1 text-xs rounded-md border border-zinc-200 dark:border-zinc-700 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Eliminar</button>
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
                            -{{ Number::currency($totalDescuentos, precision: 0) }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Subtotal</p>
                        <p class="text-lg font-semibold">
                            {{ Number::currency($subtotalConDescuento, precision: 0) }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">IVA ({{ $porcentajeIva }}%)</p>
                        <p class="text-lg font-semibold">
                            {{ Number::currency($montoIva, precision: 0) }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-emerald-200 dark:border-emerald-900 p-4 bg-emerald-50 dark:bg-emerald-950">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Total</p>
                        <p class="text-lg font-semibold text-emerald-600">
                            {{ Number::currency($total, precision: 0) }}
                        </p>
                    </div>
                </div>

                <!-- Anticipo y Saldo -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                    <div class="rounded-lg border {{ $errors->has('anticipo') ? 'border-red-300 dark:border-red-700' : 'border-zinc-200 dark:border-zinc-700' }} p-4 bg-white dark:bg-zinc-900">
                        <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-2">Anticipo</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">$</span>
                            <input
                                type="number"
                                wire:model.blur="anticipo"
                                min="0"
                                step="0.01"
                                max="999999.99"
                                class="flex-1 px-3 py-2 text-sm rounded-md border {{ $errors->has('anticipo') ? 'border-red-300 dark:border-red-600' : 'border-zinc-300 dark:border-zinc-600' }} bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 {{ $errors->has('anticipo') ? 'focus:ring-red-500' : 'focus:ring-emerald-500' }}"
                                placeholder="0.00"
                            >
                        </div>
                        @error('anticipo')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1 flex items-center gap-1">
                                <flux:icon.exclamation-triangle class="size-3" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div class="rounded-lg border border-amber-200 dark:border-amber-900 p-4 bg-amber-50 dark:bg-amber-950">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Saldo pendiente</p>
                        <p class="text-lg font-semibold text-amber-600 dark:text-amber-500">
                            {{ Number::currency($this->calcularSaldo(), precision: 0) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mini panel: acciones inferiores del panel izquierdo -->
            <div class="border-t border-zinc-100 dark:border-zinc-700 p-4 md:p-6">
                <div class="space-y-3">
                    <!-- Mensajes de error generales -->
                    @if($errors->hasAny(['selectedClientId', 'selectedDeviceId', 'items', 'fechaEntregaEstimada']))
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
                                    @error('fechaEntregaEstimada')
                                        <p class="text-sm text-red-800 dark:text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-2">
                        <button type="button"
                                wire:click="guardarOrden"
                                @disabled(!$dispositivoSeleccionado || strlen($asunto) < 3)
                                title="{{ !$dispositivoSeleccionado ? 'Seleccione un dispositivo' : (strlen($asunto) < 3 ? 'Ingrese una descripción (mín. 3 caracteres)' : 'Guardar orden') }}"
                                class="px-3 py-1.5 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
                            Guardar
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
                        <button @click="activeTab = 'informe-tecnico'"
                                :class="activeTab === 'informe-tecnico' ? 'font-semibold text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'"
                                class="px-3 py-1.5 text-sm transition-colors">
                            Informe Técnico
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
                                        @php
                                            $nombreCompletoDispositivo = $dispositivoSeleccionado['modelo'] 
                                                ? trim($dispositivoSeleccionado['modelo']['marca'] . ' ' . $dispositivoSeleccionado['modelo']['modelo'])
                                                : 'Sin modelo';
                                        @endphp
                                        <p class="font-semibold truncate" title="{{ $nombreCompletoDispositivo }}">
                                            {{ $nombreCompletoDispositivo }}
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

                    <!-- Patrón de desbloqueo -->
                    @if($dispositivoSeleccionado && $selectedDeviceId)
                        <div class="mt-4 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4"
                             x-data
                             @cambiar-patron.window="$wire.cambiarPatron()"
                        >
                            @php
                                $dispositivo = \App\Models\Dispositivo::find($selectedDeviceId);
                            @endphp
                            @if($dispositivo)
                                <livewire:dispositivos.pattern-form :dispositivo="$dispositivo" :key="'pattern-'.$selectedDeviceId" />
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Contenido de la pestaña Notas -->
                <div x-show="activeTab === 'notas'" 
                     class="flex flex-col lg:flex-1 lg:min-h-0 lg:overflow-hidden">
                    <!-- Formulario para agregar comentario (fijo arriba) -->
                    <div class="p-4 md:p-6 border-b border-zinc-100 dark:border-zinc-700 flex-shrink-0">
                        <div class="space-y-3">
                            <!-- Campo de entrada con botón de envío -->
                            <div class="flex items-start gap-2">
                                <input
                                    type="text"
                                    wire:model.live="nuevoComentario"
                                    wire:keydown.enter.prevent="$wire.agregarComentario()"
                                    placeholder="Escribe una nota"
                                    class="flex-1 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                                <button 
                                    type="button" 
                                    wire:click="agregarComentario" 
                                    wire:disabled="!$this->puedeEnviarComentario"
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shrink-0"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('nuevoComentario')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Lista de comentarios con scroll propio -->
                    <div class="flex-1 overflow-y-auto p-4 md:p-6 min-h-0">
                        @if(count($comentariosTemporales) > 0)
                            <div class="space-y-4">
                                @foreach($comentariosTemporales as $comentario)
                                    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                                        <div class="flex items-start justify-between gap-3 mb-2">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <span class="text-sm text-zinc-500 dark:text-zinc-400 truncate">{{ $comentario['user']['name'] }}</span>
                                                <span class="text-xs text-zinc-400 dark:text-zinc-500 shrink-0">{{ $comentario['created_at'] }}</span>
                                            </div>
                                            <button type="button" class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 shrink-0">
                                                <flux:icon.ellipsis-vertical class="size-4" />
                                            </button>
                                        </div>
                                        <p class="text-sm text-zinc-900 dark:text-zinc-100 whitespace-pre-wrap">{{ $comentario['comentario'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">No hay notas aún.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Contenido de la pestaña Informe Técnico -->
                <div x-show="activeTab === 'informe-tecnico'" 
                     class="flex flex-col lg:flex-1 lg:min-h-0 lg:overflow-hidden">
                    <div class="p-4 md:p-6 flex-1 overflow-y-auto min-h-0">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Informe Técnico
                                </label>
                                <textarea
                                    wire:model.live.debounce.500ms="informeTecnico"
                                    rows="15"
                                    placeholder="Escribe aquí el informe técnico detallado sobre la reparación realizada..."
                                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                                ></textarea>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                    El informe técnico se guardará cuando crees la orden de trabajo.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar items -->
    @if($mostrarModalAgregarItem)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 font-sans" style="z-index: 100;">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('mostrarModalAgregarItem', false)"></div>

            <!-- Modal Principal -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden relative flex flex-col max-h-[90vh] transition-all transform scale-100">
                
                <!-- Header -->
                <div class="p-6 pb-2">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Agregar {{ $tipoItemAgregar === 'servicio' ? 'servicio' : 'producto' }}</h2>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">
                        Busque y seleccione el {{ $tipoItemAgregar === 'servicio' ? 'servicio' : 'producto' }} que desea agregar a la orden.
                    </p>
                </div>

                <!-- Tabs & Actions -->
                <div class="px-6 py-2 flex flex-col gap-4">
                    <!-- Toggle Tabs -->
                    <div class="flex gap-2">
                        <button
                            wire:click="$set('tipoItemAgregar', 'servicio')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $tipoItemAgregar === 'servicio' ? 'bg-teal-600 text-white shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}"
                        >
                            Servicios
                        </button>
                        <button
                            wire:click="$set('tipoItemAgregar', 'producto')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $tipoItemAgregar === 'producto' ? 'bg-teal-600 text-white shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}"
                        >
                            Productos
                        </button>
                    </div>

                    <!-- Search Row + Add Button -->
                    <div class="flex gap-2 items-center">
                        <div class="relative flex-1">
                            <flux:icon.magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 size-5" />
                            <input 
                                type="text"
                                wire:model.live.debounce.300ms="busquedaItem"
                                placeholder="Buscar {{ $tipoItemAgregar === 'servicio' ? 'servicio' : 'producto' }}..."
                                class="w-full pl-10 pr-4 py-2.5 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all text-zinc-700 dark:text-zinc-200 placeholder-zinc-400 bg-white dark:bg-zinc-800"
                                autofocus
                            />
                        </div>
                        
                        <!-- Botón Nuevo -->
                        <button 
                            wire:click="abrirMiniModalCrearItem"
                            class="flex items-center gap-2 px-4 py-2.5 bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/50 transition-colors whitespace-nowrap font-medium text-sm group"
                            title="Crear nuevo {{ $tipoItemAgregar === 'servicio' ? 'servicio' : 'producto' }}"
                        >
                            <flux:icon.plus class="size-5 group-hover:scale-110 transition-transform" />
                            <span class="hidden sm:inline">Nuevo</span>
                        </button>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="p-6 flex-1 min-h-[200px] flex flex-col border-t border-zinc-100 dark:border-zinc-800 mt-2 bg-zinc-50/50 dark:bg-zinc-900/50 overflow-y-auto">
                    
                    @if(strlen($busquedaItem) > 0)
                        @if(count($itemsDisponibles) > 0)
                             <div class="grid grid-cols-1 gap-2 w-full">
                                @foreach($itemsDisponibles as $item)
                                    <button
                                        type="button"
                                        wire:click="agregarItem({{ $item['id'] }})"
                                        class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm hover:shadow-md hover:border-teal-300 dark:hover:border-teal-700 transition-all text-left flex justify-between items-center group"
                                    >
                                        <div>
                                            <p class="font-semibold text-zinc-800 dark:text-zinc-100 group-hover:text-teal-700 dark:group-hover:text-teal-400">{{ $item['nombre'] }}</p>
                                            @if($tipoItemAgregar === 'producto')
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                                    {{ $item['marca'] ?? 'Sin marca' }} • Stock: {{ $item['stock'] ?? 0 }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-teal-600 dark:text-teal-400">
                                                {{ Number::currency($tipoItemAgregar === 'servicio' ? $item['precio_base'] : $item['precio_venta'], precision: 0) }}
                                            </p>
                                        </div>
                                    </button>
                                @endforeach
                             </div>
                        @else
                            <div class="flex-1 flex items-center justify-center">
                                <div class="text-center p-8 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl w-full bg-white dark:bg-zinc-800">
                                    <p class="text-zinc-500 dark:text-zinc-400 font-medium">
                                        No se encontraron resultados para "{{ $busquedaItem }}"
                                    </p>
                                    <button 
                                        wire:click="abrirMiniModalCrearItem"
                                        class="mt-3 text-teal-600 dark:text-teal-400 text-sm hover:underline font-medium"
                                    >
                                        ¿Desea crearlo ahora?
                                    </button>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="flex-1 flex items-center justify-center">
                            <div class="text-center p-8 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl w-full bg-white dark:bg-zinc-800">
                                <p class="text-zinc-500 dark:text-zinc-400 font-medium">
                                    Escriba al menos 2 caracteres para buscar
                                </p>
                            </div>
                        </div>
                    @endif
                    
                </div>

                <!-- Footer -->
                <div class="p-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end bg-white dark:bg-zinc-900">
                    <button 
                        wire:click="$set('mostrarModalAgregarItem', false)"
                        class="px-6 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 font-medium transition-colors"
                    >
                        Cerrar
                    </button>
                </div>
            </div>

            <!-- MINIMODAL (Overlay secundario) -->
            @if($mostrarMiniModalCrearItem)
                <div class="absolute inset-0 z-[60] flex items-center justify-center p-4">
                     <!-- Backdrop oscuro específico para el minimodal -->
                    <div 
                        class="absolute inset-0 bg-black/40 backdrop-blur-[1px]" 
                        wire:click="$set('mostrarMiniModalCrearItem', false)"
                    ></div>

                    <!-- Contenido del Minimodal -->
                    <div class="bg-white dark:bg-zinc-900 w-full max-w-sm rounded-xl shadow-2xl relative z-10 overflow-hidden transform scale-100 border border-zinc-100 dark:border-zinc-700 animate-in zoom-in-95 duration-200">
                        
                        <!-- Mini Header -->
                        <div class="bg-zinc-50 dark:bg-zinc-800 px-5 py-4 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                @if($tipoItemAgregar === 'servicio')
                                    <flux:icon.square-3-stack-3d class="text-teal-600 dark:text-teal-400 size-5"/>
                                @else
                                    <flux:icon.cube class="text-teal-600 dark:text-teal-400 size-5"/>
                                @endif
                                <h3 class="font-bold text-zinc-800 dark:text-zinc-100">Nuevo {{ $tipoItemAgregar === 'servicio' ? 'Servicio' : 'Producto' }}</h3>
                            </div>
                            <button wire:click="$set('mostrarMiniModalCrearItem', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 p-1 rounded-full transition-colors border border-transparent hover:border-zinc-200 dark:hover:border-zinc-600">
                                <flux:icon.x-mark class="size-4" />
                            </button>
                        </div>

                        <!-- Mini Form -->
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                                    Nombre del {{ $tipoItemAgregar === 'servicio' ? 'Servicio' : 'Producto' }}
                                    @if($tipoItemAgregar === 'servicio' || $tipoItemAgregar === 'producto')
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <input 
                                    wire:model="newItemName"
                                    type="text" 
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                                    placeholder="Ej: {{ $tipoItemAgregar === 'servicio' ? 'Mantenimiento General' : 'Aceite de Motor' }}"
                                    autofocus
                                />
                                @error('newItemName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            @if($tipoItemAgregar === 'servicio')
                                <!-- Campos específicos para Servicio -->
                                <div>
                                    <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                                        Descripción
                                    </label>
                                    <textarea 
                                        wire:model="newItemDescription"
                                        rows="3"
                                        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 resize-none"
                                        placeholder="Descripción del servicio (opcional)"
                                    ></textarea>
                                    @error('newItemDescription') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                                            Precio
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 dark:text-zinc-400 text-sm">$</span>
                                            <input 
                                                wire:model="newItemPrice"
                                                type="number" 
                                                class="w-full pl-7 pr-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                                                placeholder="0.00"
                                            />
                                        </div>
                                        @error('newItemPrice') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                                            Categoría
                                        </label>
                                        <input 
                                            wire:model="newItemCategoria"
                                            type="text" 
                                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                                            placeholder="Ej: Reparación, Mantenimiento"
                                        />
                                        @error('newItemCategoria') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            @else
                                <!-- Campos para Producto -->
                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                                            Precio de Venta
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 dark:text-zinc-400 text-sm">$</span>
                                            <input 
                                                wire:model="newItemPrice"
                                                type="number" 
                                                step="0.01"
                                                class="w-full pl-7 pr-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                                                placeholder="0.00"
                                            />
                                        </div>
                                        @error('newItemPrice') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                                            Precio de Compra
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 dark:text-zinc-400 text-sm">$</span>
                                            <input 
                                                wire:model="newItemPrecioCompra"
                                                type="number" 
                                                step="0.01"
                                                class="w-full pl-7 pr-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                                                placeholder="0.00"
                                            />
                                        </div>
                                        @error('newItemPrecioCompra') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                                            Stock
                                        </label>
                                        <input 
                                            wire:model="newItemStock"
                                            type="number" 
                                            min="0"
                                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                                            placeholder="0"
                                        />
                                        @error('newItemStock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                                            Stock Mínimo
                                        </label>
                                        <input 
                                            wire:model="newItemStockMinimo"
                                            type="number" 
                                            min="0"
                                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                                            placeholder="0"
                                        />
                                        @error('newItemStockMinimo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5">
                                        Categoría
                                    </label>
                                    <input 
                                        wire:model="newItemCategoriaProducto"
                                        type="text" 
                                        class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none text-sm bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                                        placeholder="Ej: Pantallas, Baterías"
                                    />
                                    @error('newItemCategoriaProducto') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <div class="pt-2">
                                <button 
                                    wire:click="guardarNuevoItem"
                                    class="w-full bg-teal-600 text-white py-2.5 rounded-lg hover:bg-teal-700 font-medium text-sm flex items-center justify-center gap-2 shadow-lg shadow-teal-600/20 transition-all active:scale-[0.98]"
                                >
                                    <flux:icon.arrow-down-tray class="size-4" />
                                    Guardar {{ $tipoItemAgregar === 'servicio' ? 'Servicio' : 'Producto' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Modelo <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                @if($modeloSeleccionadoId)
                                    <button type="button" wire:click="limpiarModelo" class="text-xs px-2 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Cambiar modelo</button>
                                @endif
                                <button type="button" wire:click="abrirModalCrearModelo" class="text-xs px-2 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">Nuevo modelo</button>
                            </div>
                        </div>
                        <div class="relative mt-1">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="modeloSearchTerm"
                                wire:keydown.escape="clearModeloSearchResults"
                                wire:blur="$dispatch('modeloSearchBlurred')"
                                wire:focus="mostrarModelosAlFocus"
                                wire:mousedown="$dispatch('ignoreBlur')"
                                placeholder="Buscar por marca, modelo o año..."
                                autocomplete="off"
                                @if($modeloSeleccionadoId) readonly @endif
                                class="w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 @if($modeloSeleccionadoId) bg-zinc-50 dark:bg-zinc-800 cursor-not-allowed @endif"
                            >
                            @error('modeloSeleccionadoId')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            @if($showModeloSearchResults && !$modeloSeleccionadoId)
                                @if(count($modelosFound) > 0)
                                    <ul class="absolute z-20 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg">
                                        @foreach($modelosFound as $modelo)
                                            <li
                                                wire:mousedown="$dispatch('ignoreBlur')"
                                                wire:click="selectModelo({{ $modelo['id'] }})"
                                                wire:mouseup="$dispatch('processBlur')"
                                                class="px-3 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-700 cursor-pointer border-b border-zinc-100 dark:border-zinc-700 last:border-b-0"
                                            >
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="font-medium">{{ $modelo['marca'] }} {{ $modelo['modelo'] }}</span>
                                                        @if($modelo['anio'])
                                                            <span class="text-zinc-400 text-xs ml-2">({{ $modelo['anio'] }})</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="absolute z-20 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-md mt-1 px-3 py-2 text-sm text-zinc-500">
                                        @if(strlen(trim($modeloSearchTerm)) >= 2)
                                            No se encontraron modelos con ese criterio de búsqueda.
                                        @else
                                            No hay modelos disponibles.
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
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

                    <!-- Tipo de bloqueo -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Tipo de bloqueo</label>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="tipoBloqueo" value="ninguno" wire:model.live="tipoBloqueoDispositivo" class="h-4 w-4 text-emerald-600 border-zinc-300 dark:border-zinc-600">
                                <span class="text-sm">Ninguno</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="tipoBloqueo" value="patron" wire:model.live="tipoBloqueoDispositivo" class="h-4 w-4 text-emerald-600 border-zinc-300 dark:border-zinc-600">
                                <span class="text-sm">Patrón</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="tipoBloqueo" value="contraseña" wire:model.live="tipoBloqueoDispositivo" class="h-4 w-4 text-emerald-600 border-zinc-300 dark:border-zinc-600">
                                <span class="text-sm">Contraseña</span>
                            </label>
                        </div>
                        @error('tipoBloqueoDispositivo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Campo de contraseña -->
                    @if($tipoBloqueoDispositivo === 'contraseña')
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Contraseña</label>
                            <input type="text" wire:model.live="contraseñaDispositivo" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm" placeholder="Ingrese la contraseña">
                            @error('contraseñaDispositivo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <!-- Componente de patrón -->
                    @if($tipoBloqueoDispositivo === 'patron')
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Dibuja el patrón</h4>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Dibuja el patrón tal como está configurado en el dispositivo.</p>
                                </div>

                                <div
                                    x-data="patternLock({
                                        onFinish(pattern) {
                                            @this.set('patronDispositivo', pattern)
                                        }
                                    })"
                                    class="space-y-3"
                                >
                                    <div
                                        class="relative mx-auto h-48 w-48 select-none"
                                        @mousedown="start($event)"
                                        @mousemove="move($event)"
                                        @mouseup="end()"
                                        @touchstart.prevent="start($event)"
                                        @touchmove.prevent="move($event)"
                                        @touchend="end()"
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
                                                    class="h-10 w-10 rounded-full border-2 border-zinc-400 dark:border-zinc-600 bg-white dark:bg-zinc-800 flex items-center justify-center transition"
                                                    :class="dotsActive.includes({{ $i }}) ? 'bg-emerald-600 border-emerald-800 dark:bg-emerald-500 dark:border-emerald-700 text-white' : ''"
                                                    data-id="{{ $i }}"
                                                >
                                                    <span class="text-xs font-medium">{{ $i }}</span>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 text-center">
                                        Patrón capturado: <span class="font-mono text-zinc-700 dark:text-zinc-300">{{ $patronDispositivo ?: 'Ninguno' }}</span>
                                    </div>

                                    @error('patronDispositivo')
                                        <div class="text-xs text-red-600 dark:text-red-400 text-center">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    @if($patronDispositivo)
                                        <div class="pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2 text-center">Patrón guardado:</p>
                                            <div
                                                x-data="patternViewer(@js($patronDispositivo))"
                                                x-init="draw()"
                                                class="relative mx-auto h-32 w-32 select-none"
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

                                                <div class="grid grid-cols-3 gap-3 h-full w-full items-center justify-items-center">
                                                    @for ($i = 1; $i <= 9; $i++)
                                                        <div
                                                            class="h-6 w-6 rounded-full border-2 border-zinc-400 dark:border-zinc-600 bg-white dark:bg-zinc-800 flex items-center justify-center transition"
                                                            data-id="{{ $i }}"
                                                            :class="isActive({{ $i }}) ? 'bg-emerald-600 border-emerald-800 dark:bg-emerald-500 dark:border-emerald-700 text-white' : ''"
                                                        >
                                                            <span class="text-[9px] font-medium">{{ $i }}</span>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

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
                        <input type="number" wire:model.blur="modeloNuevoAnio" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm">
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
                            <input type="text" wire:model.live="clienteNuevoRut" class="mt-1 w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="12345678-9">
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

    <!-- Toast de éxito al crear orden -->
    <div 
        x-data="{ 
            open: @entangle('mostrarToastOrdenCreada'),
            redirectUrl: null
        }" 
        x-show="open" 
        x-transition.opacity 
        class="fixed bottom-4 right-4 z-50"
        @orden-creada.window="redirectUrl = $event.detail.url; setTimeout(() => { if(redirectUrl) window.location.href = redirectUrl; }, 1500)"
    >
        <div x-init="$watch('open', value => { if(value){ setTimeout(() => open = false, 2500) } })" class="flex items-center gap-2 px-4 py-2 rounded-md bg-emerald-600 text-white shadow-lg">
            <flux:icon.check class="size-4" />
            <span class="text-sm font-medium">Orden creada exitosamente</span>
        </div>
    </div>

</div>

