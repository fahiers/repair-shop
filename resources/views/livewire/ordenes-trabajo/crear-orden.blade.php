<div class="max-w-screen-2xl mx-auto p-4 md:p-6 lg:p-8">
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
        <div class="flex items-center gap-3">
            <flux:button icon="chevron-left" variant="ghost" href="{{ route('ordenes-trabajo.index') }}"/>
            <div>
                <h1 class="text-xl md:text-2xl font-semibold">Nueva orden de reparación</h1>
                <p class="text-sm text-zinc-500">Complete los datos para crear la orden</p>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-3 flex-wrap w-full lg:w-auto justify-start lg:justify-end">
            <flux:button icon="printer" variant="ghost" disabled>Imprimir orden</flux:button>
            <flux:button icon="tag" variant="ghost" disabled>Imprimir etiqueta</flux:button>
            <flux:button icon="credit-card" variant="primary" disabled>Ingresar pago</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <!-- Panel izquierdo: detalles y conceptos -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
            <!-- Sección de cliente -->
            <div class="p-4 md:p-6 border-b border-zinc-100 dark:border-zinc-700">
                @if($clienteSeleccionado)
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-3">
                            <flux:avatar :name="$clienteSeleccionado->nombre"/>
                            <div>
                                <p class="font-medium">{{ $clienteSeleccionado->nombre }}</p>
                                <div class="flex items-center gap-3 text-sm text-zinc-500">
                                    <span>{{ $clienteSeleccionado->telefono }}</span>
                                    @if($clienteSeleccionado->email)
                                        <span>{{ $clienteSeleccionado->email }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <flux:dropdown>
                            <flux:button icon="ellipsis-vertical" variant="ghost"/>
                            <flux:menu>
                                <flux:menu.item wire:click="limpiarCliente">Cambiar cliente</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                @else
                    <div class="space-y-3">
                        <flux:field>
                            <flux:label>Buscar cliente</flux:label>
                            <flux:input 
                                wire:model.live.debounce.300ms="busquedaCliente"
                                placeholder="Buscar por nombre, teléfono, email o RUT..."
                                icon="magnifying-glass"
                            />
                        </flux:field>
                        
                        @if(strlen($busquedaCliente) >= 2 && $this->clientesBuscados->count() > 0)
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg divide-y divide-zinc-200 dark:divide-zinc-700 max-h-64 overflow-y-auto">
                                @foreach($this->clientesBuscados as $cliente)
                                    <button
                                        wire:click="seleccionarCliente({{ $cliente->id }})"
                                        class="w-full px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        <p class="font-medium">{{ $cliente->nombre }}</p>
                                        <p class="text-sm text-zinc-500">{{ $cliente->telefono }} • {{ $cliente->email }}</p>
                                    </button>
                                @endforeach
                            </div>
                        @elseif(strlen($busquedaCliente) >= 2)
                            <div class="text-sm text-zinc-500 text-center py-3">
                                No se encontraron clientes. 
                                <flux:button variant="ghost" size="sm" class="inline-flex">
                                    Crear nuevo cliente
                                </flux:button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="p-4 md:p-6 space-y-4 dark:text-zinc-100">
                <!-- Tipo de servicio -->
                <div class="flex flex-wrap items-center gap-3 md:gap-4">
                    <div class="flex items-center gap-4">
                        <flux:radio name="tipo" value="reparacion" wire:model.live="tipoServicio">Reparación</flux:radio>
                        <flux:radio name="tipo" value="mantenimiento" wire:model.live="tipoServicio">Mantenimiento</flux:radio>
                        <flux:radio name="tipo" value="garantia" wire:model.live="tipoServicio">Garantía</flux:radio>
                    </div>
                </div>

                <!-- Asunto / descripción corta -->
                <flux:input 
                    wire:model.blur="asunto"
                    placeholder="Ej: Cambio de pantalla iPhone" 
                    class="w-full"
                />
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
                                            wire:model.blur="items.{{ $index }}.cantidad"
                                            wire:change="actualizarCantidad({{ $index }}, $event.target.value)"
                                            class="w-16 px-2 py-1 text-right border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 dark:text-zinc-100"
                                            min="1"
                                        >
                                    </td>
                                    <td class="p-3 text-right">
                                        <input 
                                            type="number" 
                                            wire:model.blur="items.{{ $index }}.precio"
                                            wire:change="actualizarPrecio({{ $index }}, $event.target.value)"
                                            class="w-24 px-2 py-1 text-right border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 dark:text-zinc-100"
                                            min="0"
                                            step="0.01"
                                        >
                                    </td>
                                    <td class="p-3 text-right">
                                        <input 
                                            type="number" 
                                            wire:model.blur="items.{{ $index }}.descuento"
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
                                            <flux:button 
                                                size="xs" 
                                                icon="trash" 
                                                variant="ghost"
                                                wire:click="eliminarItem({{ $index }})"
                                                wire:loading.attr="disabled"
                                            />
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

                <flux:button 
                    variant="ghost" 
                    icon="plus"
                    wire:click="abrirModalAgregarItem('servicio')"
                >
                    Agregar servicio o producto
                </flux:button>

                <!-- Totales -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-2">
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Descuento</p>
                        <p class="text-lg font-semibold text-red-600 dark:text-red-500">
                            $ -{{ number_format($this->totalDescuentos, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Subtotal</p>
                        <p class="text-lg font-semibold">
                            $ {{ number_format($this->subtotalConDescuento, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">IVA ({{ $porcentajeIva }}%)</p>
                        <p class="text-lg font-semibold">
                            $ {{ number_format($this->montoIva, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-emerald-200 dark:border-emerald-900 p-4 bg-emerald-50 dark:bg-emerald-950">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Total</p>
                        <p class="text-lg font-semibold text-emerald-600">
                            $ {{ number_format($this->total, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho: equipo / pestañas -->
        <div class="space-y-4" x-data="{ activeTab: 'equipo' }">
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
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
    <flux:modal wire:model="mostrarModalAgregarItem" class="md:w-[600px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    Agregar {{ $tipoItemAgregar === 'servicio' ? 'servicio' : 'producto' }}
                </flux:heading>
                <flux:text class="mt-2">
                    Busque y seleccione el {{ $tipoItemAgregar }} que desea agregar a la orden.
                </flux:text>
            </div>

            <!-- Alternar entre servicio y producto -->
            <div class="flex gap-2">
                <flux:button 
                    wire:click="$set('tipoItemAgregar', 'servicio')"
                    :variant="$tipoItemAgregar === 'servicio' ? 'primary' : 'ghost'"
                    size="sm"
                >
                    Servicios
                </flux:button>
                <flux:button 
                    wire:click="$set('tipoItemAgregar', 'producto')"
                    :variant="$tipoItemAgregar === 'producto' ? 'primary' : 'ghost'"
                    size="sm"
                >
                    Productos
                </flux:button>
            </div>

            <flux:input 
                wire:model.live.debounce.300ms="busquedaItem"
                placeholder="Buscar {{ $tipoItemAgregar }}..."
                icon="magnifying-glass"
            />

            <div class="max-h-96 overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
                @if(strlen($busquedaItem) >= 2)
                    @forelse($this->itemsDisponibles as $item)
                        <button
                            wire:click="agregarItem({{ $item->id }})"
                            class="w-full px-4 py-3 text-left hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors border-b border-zinc-200 dark:border-zinc-700 last:border-b-0 data-[loading]:opacity-50"
                            wire:loading.attr="disabled"
                            wire:target="agregarItem({{ $item->id }})"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">{{ $item->nombre }}</p>
                                    @if($tipoItemAgregar === 'producto')
                                        <p class="text-sm text-zinc-500">
                                            {{ $item->marca }} • Stock: {{ $item->stock }}
                                        </p>
                                    @endif
                                    @if($item->descripcion)
                                        <p class="text-sm text-zinc-500 mt-1">{{ Str::limit($item->descripcion, 60) }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold">
                                        ${{ number_format($tipoItemAgregar === 'servicio' ? $item->precio_base : $item->precio_venta, 0, ',', '.') }}
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
                <flux:button variant="ghost" wire:click="$set('mostrarModalAgregarItem', false)">
                    Cerrar
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
