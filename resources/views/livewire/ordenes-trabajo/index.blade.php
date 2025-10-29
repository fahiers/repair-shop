<div class="max-w-screen-2xl mx-auto p-4 md:p-6 lg:p-8">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <flux:button icon="chevron-left" variant="ghost"/>
            <div>
                <h1 class="text-xl md:text-2xl font-semibold">Orden de reparación</h1>
                <p class="text-sm text-zinc-500">Orden #3 · 24 SEPT 2023 — 27 SEPT 2023</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:button icon="printer" variant="ghost">Imprimir orden</flux:button>
            <flux:button icon="tag" variant="ghost">Imprimir etiqueta</flux:button>
            <flux:button icon="credit-card" variant="primary">Ingresar pago</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <!-- Panel izquierdo: detalles y conceptos -->
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm">
            <div class="p-4 md:p-6 border-b border-zinc-100 flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <flux:avatar name="Daniela Martínez"/>
                    <div>
                        <p class="font-medium">Daniela Martínez</p>
                        <div class="flex items-center gap-3 text-sm text-zinc-500">
                            <span>+56 983 478 148</span>
                            <flux:badge soft>7:25 PM</flux:badge>
                        </div>
                    </div>
                </div>
                <flux:dropdown>
                    <flux:button icon="ellipsis-vertical" variant="ghost"/>
                    <flux:menu>
                        <flux:menu.item>Editar cliente</flux:menu.item>
                        <flux:menu.item>Enviar por WhatsApp</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>

            <div class="p-4 md:p-6 space-y-4">
                <!-- Tipo de servicio -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-4">
                        <flux:radio name="tipo" value="reparacion" checked>Reparación</flux:radio>
                        <flux:radio name="tipo" value="mantenimiento">Mantenimiento</flux:radio>
                        <flux:radio name="tipo" value="garantia">Garantía</flux:radio>
                    </div>
                </div>

                <!-- Asunto / descripción corta -->
                <flux:input placeholder="Cambio de pantalla iPhone" class="w-full"/>

                <!-- Tabla conceptos -->
                <div class="overflow-hidden rounded-lg border border-zinc-200">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 text-zinc-600">
                            <tr>
                                <th class="text-left font-medium p-3">Servicio o producto</th>
                                <th class="text-right font-medium p-3">Cant</th>
                                <th class="text-right font-medium p-3">Precio</th>
                                <th class="text-right font-medium p-3">Desc</th>
                                <th class="text-right font-medium p-3">Total</th>
                                <th class="p-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t">
                                <td class="p-3">Cambio de pantalla</td>
                                <td class="p-3 text-right">1</td>
                                <td class="p-3 text-right">$35.000</td>
                                <td class="p-3 text-right"><flux:badge soft>10%</flux:badge></td>
                                <td class="p-3 text-right">$31.500</td>
                                <td class="p-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="xs" icon="pencil" variant="ghost"/>
                                        <flux:button size="xs" icon="trash" variant="ghost"/>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-t">
                                <td class="p-3">Pantalla iPhone 13 Pro Max</td>
                                <td class="p-3 text-right">1</td>
                                <td class="p-3 text-right">$121.990</td>
                                <td class="p-3 text-right"><flux:badge soft>5%</flux:badge></td>
                                <td class="p-3 text-right">$115.891</td>
                                <td class="p-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="xs" icon="pencil" variant="ghost"/>
                                        <flux:button size="xs" icon="trash" variant="ghost"/>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <flux:button variant="ghost" icon="plus">Agregar nuevo producto o servicio</flux:button>

                <!-- Totales -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-2">
                    <div class="rounded-lg border p-4">
                        <p class="text-xs text-zinc-500 mb-1">Descuento</p>
                        <p class="text-lg font-semibold">$ -9.600</p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <p class="text-xs text-zinc-500 mb-1">Subtotal</p>
                        <p class="text-lg font-semibold">$ 147.391</p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <p class="text-xs text-zinc-500 mb-1">IVA</p>
                        <p class="text-lg font-semibold">$ 28.004</p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <p class="text-xs text-zinc-500 mb-1">Total</p>
                        <p class="text-lg font-semibold text-emerald-600">$ 175.395</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho: equipo / pestañas -->
        <div class="space-y-4">
            <div class="rounded-xl border border-zinc-200 bg-white shadow-sm">
                <div class="p-4 md:p-6 border-b border-zinc-100">
                    <div class="flex items-center gap-4">
                        <flux:button size="sm" variant="ghost" class="data-[active=true]:font-medium" data-active="true">Equipo</flux:button>
                        <flux:button size="sm" variant="ghost">Fotos</flux:button>
                        <flux:button size="sm" variant="ghost">Notas</flux:button>
                        <flux:button size="sm" variant="ghost">Informe</flux:button>
                        <flux:button size="sm" variant="ghost">Tareas</flux:button>
                        <flux:button size="sm" variant="ghost">Citas</flux:button>
                        <flux:button size="sm" variant="ghost">Pago</flux:button>
                        <flux:button size="sm" variant="ghost">Info</flux:button>
                    </div>
                </div>

                <div class="p-4 md:p-6 grid grid-cols-1 gap-6">
                    <!-- Tarjeta equipo -->
                    <div class="rounded-lg border p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <flux:avatar icon="device-phone-mobile"/>
                                <div>
                                    <p class="font-medium">Móvil Apple iPhone 13 Pro Space Gray</p>
                                    <p class="text-xs text-zinc-500">N° Serie: 542895852511424 · Contraseña: 1313</p>
                                </div>
                            </div>
                            <flux:button size="sm" variant="secondary">Editar equipo</flux:button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Accesorios -->
                        <div class="rounded-lg border p-4">
                            <p class="font-medium mb-3">Accesorios</p>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2"><flux:checkbox checked/>Bolso</label>
                                <label class="flex items-center gap-2"><flux:checkbox checked/>Cargador</label>
                                <label class="flex items-center gap-2"><flux:checkbox checked/>Funda</label>
                                <label class="flex items-center gap-2"><flux:checkbox/>Lámina protectora</label>
                            </div>
                        </div>

                        <!-- Estado del equipo -->
                        <div class="rounded-lg border p-4">
                            <p class="font-medium mb-3">Estado del equipo</p>
                            <flux:textarea rows="5" placeholder="Raya en el borde inferior al costado del conector Lightning"/>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:button variant="secondary" icon="plus">Añadir inspección</flux:button>
                            <flux:badge soft>1 inspección(es) realizada(s)</flux:badge>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button variant="ghost" icon="printer">Imprimir orden</flux:button>
                            <flux:button variant="ghost" icon="tag">Imprimir etiqueta</flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
