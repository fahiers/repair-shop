<div class="max-w-screen-2xl mx-auto p-4 md:p-6 lg:p-8">
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
        <div class="flex items-center gap-3">
            <a href="{{ route('ordenes-trabajo.index') }}" class="inline-flex items-center gap-2 px-2 py-1 text-sm text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100">
                ← Volver
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-semibold">Nueva orden de reparación</h1>
                <p class="text-sm text-zinc-500">Complete los datos para crear la orden</p>
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
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Buscar cliente</label>
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
                <!-- Tipo de servicio -->
                <div class="flex flex-wrap items-center gap-3 md:gap-4">
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

                <!-- Asunto / descripción corta -->
                <input
                    type="text"
                    wire:model.blur="asunto"
                    placeholder="Ej: Cambio de pantalla iPhone"
                    class="w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                @error('asunto')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror

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

                <button type="button" wire:click="abrirModalAgregarItem('servicio')" class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                    Agregar servicio o producto
                </button>

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
            </div>
        </div>
        
        <!-- Panel derecho: acciones y pestañas -->
        <div class="lg:sticky lg:top-6 lg:self-start" x-data="{ activeTab: 'equipo' }">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
                <!-- Botones de acciones en el header -->
                <div class="p-4 md:p-6 border-b border-zinc-100 dark:border-zinc-700">
                    <div class="flex items-center gap-2 md:gap-3 flex-wrap w-full justify-end">
                        <button disabled class="px-3 py-1.5 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 disabled:opacity-50">Imprimir orden</button>
                        <button disabled class="px-3 py-1.5 text-sm rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 disabled:opacity-50">Imprimir etiqueta</button>
                        <button disabled class="px-3 py-1.5 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">Ingresar pago</button>
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
                        <button @click="activeTab = 'fotos'"
                                :class="activeTab === 'fotos' ? 'font-semibold text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'"
                                class="px-3 py-1.5 text-sm transition-colors">
                            Fotos
                        </button>
                        <button @click="activeTab = 'notas'"
                                :class="activeTab === 'notas' ? 'font-semibold text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300'"
                                class="px-3 py-1.5 text-sm transition-colors">
                            Notas
                        </button>
                    </div>
                </div>

                <!-- Contenido de la pestaña Equipo -->
                <div x-show="activeTab === 'equipo'" class="p-4 md:p-6">
                    <p class="text-zinc-500 dark:text-zinc-400">Seleccione primero un cliente para configurar el equipo...</p>
                </div>

                <!-- Contenido de la pestaña Fotos -->
                <div x-show="activeTab === 'fotos'" class="p-4 md:p-6">
                    <p class="text-zinc-500 dark:text-zinc-400">Contenido de fotos aquí...</p>
                </div>

                <!-- Contenido de la pestaña Notas -->
                <div x-show="activeTab === 'notas'" class="p-4 md:p-6">
                    <p class="text-zinc-500 dark:text-zinc-400">Contenido de notas aquí...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar items -->
    @if($mostrarModalAgregarItem)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" aria-hidden="true"></div>
            <div class="relative w-full max-w-[600px] mx-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 shadow-xl">
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
</div>
