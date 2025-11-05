<?php

namespace App\Livewire\OrdenesTrabajo;

use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\ModeloDispositivo;
use App\Models\Producto;
use App\Models\Servicio;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Services\OrderNumberGenerator;

class CrearOrden extends Component
{
    // Cliente y búsqueda
    public $clienteSeleccionado = null; // array{id:int,nombre:string,telefono:?string,email:?string} | null

    // Nueva UX de selección (según documentación)
    public string $clientSearchTerm = '';

    public array $clientsFound = [];

    public ?int $selectedClientId = null;

    public bool $loadingClients = false;

    public bool $showClientSearchResults = false;

    private string $lastSelectedClientName = '';

    public bool $ignoringBlur = false;

    public string $rut = '';

    // Tipo de servicio
    #[Validate('required|in:reparacion,mantenimiento,garantia')]
    public string $tipoServicio = 'reparacion';

    // Fechas y estado de la orden
    public string $fechaIngreso = '';

    public ?string $fechaEntregaEstimada = null;

    public string $estado = 'pendiente';

    // Asunto/Descripción
    #[Validate('required|min:3|max:255')]
    public string $asunto = '';

    // Items (servicios/productos)
    public array $items = [];

    public bool $mostrarModalAgregarItem = false;

    public string $tipoItemAgregar = 'servicio';

    public string $busquedaItem = '';

    public array $itemsDisponibles = [];

    // IVA
    public bool $aplicarIva = true;

    public int $porcentajeIva = 19;

    // Totales calculados
    public float $subtotalItems = 0;

    public float $totalDescuentos = 0;

    public float $subtotalConDescuento = 0;

    public float $montoIva = 0;

    public float $total = 0;

    // Tabs (sin Alpine)
    public string $activeTab = 'equipo';

    public function mount(): void
    {
        $this->fechaIngreso = now()->toDateString();
        $this->recalcularTotales();
    }

    // Dispositivo y búsqueda
    public ?int $selectedDeviceId = null;

    // Búsqueda de dispositivos removida (UI simplificada)

    public bool $mostrarModalCrearDispositivo = false;

    public string $modoCreacionDispositivo = 'rapido'; // rapido | completo

    // Creación rápida de dispositivo
    public ?int $modeloSeleccionadoId = null;

    public string $modeloSearchTerm = '';

    public array $modelosFound = [];

    public ?string $imeiDispositivo = null;

    public ?string $colorDispositivo = null;

    public ?string $observacionesDispositivo = null;

    // Sugerencias
    public ?int $suggestedDeviceId = null;

    // Edición de dispositivo
    public bool $mostrarModalEditarDispositivo = false;
    public bool $mostrarToastEquipoActualizado = false;

    // Crear nuevo modelo (desde modal de dispositivo)
    public bool $mostrarModalCrearModelo = false;
    public string $modeloNuevoMarca = '';
    public string $modeloNuevoModelo = '';
    public ?int $modeloNuevoAnio = null;
    public ?string $modeloNuevoDescripcion = null;

    #[On('ignoreBlur')]
    public function setIgnoreBlur(): void
    {
        $this->ignoringBlur = true;
    }

    #[On('processBlur')]
    public function setProcessBlur(): void
    {
        $this->ignoringBlur = false;
    }

    #[On('clientSearchBlurred')]
    public function handleClientSearchBlur(): void
    {
        if ($this->ignoringBlur) {
            return;
        }

        if ($this->showClientSearchResults && $this->selectedClientId === null) {
            $this->clientSearchTerm = $this->lastSelectedClientName;
        } elseif ($this->showClientSearchResults && $this->selectedClientId !== null) {
            $client = Cliente::find($this->selectedClientId);
            if ($client && $this->clientSearchTerm !== $client->nombre) {
                $this->clientSearchTerm = $client->nombre;
            }
        }

        $this->showClientSearchResults = false;
    }

    public function updatedClientSearchTerm($value): void
    {
        $trimmedValue = trim((string) $value);

        if ($trimmedValue === '') {
            $this->clientsFound = [];
            $this->loadingClients = false;
            $this->showClientSearchResults = false;
            $this->selectedClientId = null;
            $this->rut = '';
            $this->lastSelectedClientName = '';

            return;
        }

        if (strlen($trimmedValue) < 2) {
            $this->clientsFound = [];
            $this->loadingClients = false;
            $this->showClientSearchResults = true;

            return;
        }

        $this->loadingClients = true;
        $this->showClientSearchResults = true;

        $query = Cliente::query()->where(function ($q) use ($trimmedValue) {
            $q->where('nombre', 'like', '%' . $trimmedValue . '%')
                ->orWhere('rut', 'like', '%' . $trimmedValue . '%')
                ->orWhere('telefono', 'like', '%' . $trimmedValue . '%')
                ->orWhere('email', 'like', '%' . $trimmedValue . '%');
        });

        $this->clientsFound = $query->take(10)->get()->toArray();
        $this->loadingClients = false;
    }

    public function selectClient(int $clientId): void
    {
        $client = Cliente::find($clientId);

        if ($client) {
            $this->selectedClientId = $client->id;
            $this->clientSearchTerm = $client->nombre;
            $this->rut = (string) ($client->rut ?? '');
            $this->lastSelectedClientName = $client->nombre;

            // Mantener compatibilidad con la UI existente
            $this->clienteSeleccionado = [
                'id' => $client->id,
                'nombre' => $client->nombre,
                'telefono' => $client->telefono,
                'email' => $client->email,
            ];

            // Sugerir dispositivo reciente o seleccionar automáticamente si tiene uno
            $this->suggestedDeviceId = $this->calcularDispositivoSugerido($client->id);
            if ($this->suggestedDeviceId && Dispositivo::where('cliente_id', $client->id)->count() === 1) {
                $this->selectDevice($this->suggestedDeviceId);
            }
        }

        $this->clientsFound = [];
        $this->loadingClients = false;
        $this->showClientSearchResults = false;
    }

    public function clearClientSearchResults(): void
    {
        if ($this->selectedClientId) {
            $client = Cliente::find($this->selectedClientId);
            if ($client) {
                $this->clientSearchTerm = $client->nombre;
                $this->lastSelectedClientName = $client->nombre;
            } else {
                $this->clientSearchTerm = '';
                $this->selectedClientId = null;
                $this->rut = '';
                $this->lastSelectedClientName = '';
                $this->clienteSeleccionado = null;
            }
        } else {
            $this->clientSearchTerm = '';
            $this->rut = '';
            $this->lastSelectedClientName = '';
            $this->clienteSeleccionado = null;
        }

        $this->clientsFound = [];
        $this->loadingClients = false;
        $this->showClientSearchResults = false;
    }

    public function limpiarCliente(): void
    {
        $this->clienteSeleccionado = null;
        $this->clientSearchTerm = '';
        $this->selectedClientId = null;
        $this->clientsFound = [];
        $this->rut = '';
        $this->lastSelectedClientName = '';
        $this->showClientSearchResults = false;
    }

    public function updatedBusquedaItem($valor): void
    {
        if (strlen($valor) < 2) {
            $this->itemsDisponibles = [];

            return;
        }

        if ($this->tipoItemAgregar === 'servicio') {
            $this->itemsDisponibles = Servicio::query()
                ->where('estado', 'activo')
                ->where('nombre', 'like', '%' . $valor . '%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->itemsDisponibles = Producto::query()
                ->where('estado', 'activo')
                ->where(function ($query) use ($valor) {
                    $query->where('nombre', 'like', '%' . $valor . '%')
                        ->orWhere('marca', 'like', '%' . $valor . '%');
                })
                ->limit(10)
                ->get()
                ->toArray();
        }
    }

    public function updatedTipoItemAgregar(): void
    {
        $this->busquedaItem = '';
        $this->itemsDisponibles = [];
    }

    public function agregarItem($itemId): void
    {
        if ($this->tipoItemAgregar === 'servicio') {
            $servicio = Servicio::find($itemId);
            if (! $servicio) {
                return;
            }

            // Verificar si ya existe
            $existe = collect($this->items)->contains(function ($item) use ($itemId) {
                return $item['tipo'] === 'servicio' && $item['id'] === $itemId;
            });

            if ($existe) {
                return;
            }

            $this->items[] = [
                'id' => $servicio->id,
                'tipo' => 'servicio',
                'nombre' => $servicio->nombre,
                'cantidad' => 1,
                'precio' => (float) $servicio->precio_base,
                'descuento' => 0,
            ];
        } else {
            $producto = Producto::find($itemId);
            if (! $producto) {
                return;
            }

            // Verificar si ya existe
            $existe = collect($this->items)->contains(function ($item) use ($itemId) {
                return $item['tipo'] === 'producto' && $item['id'] === $itemId;
            });

            if ($existe) {
                return;
            }

            $this->items[] = [
                'id' => $producto->id,
                'tipo' => 'producto',
                'nombre' => $producto->nombre,
                'cantidad' => 1,
                'precio' => (float) $producto->precio_venta,
                'descuento' => 0,
            ];
        }

        $this->busquedaItem = '';
        $this->itemsDisponibles = [];
        $this->mostrarModalAgregarItem = false;
        $this->recalcularTotales();
    }

    public function actualizarCantidad($index, $cantidad): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        $this->items[$index]['cantidad'] = max(1, (int) $cantidad);
        $this->recalcularTotales();
    }

    public function actualizarPrecio($index, $precio): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        $this->items[$index]['precio'] = max(0, (float) $precio);
        $this->recalcularTotales();
    }

    public function actualizarDescuento($index, $descuento): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        $this->items[$index]['descuento'] = min(100, max(0, (float) $descuento));
        $this->recalcularTotales();
    }

    public function eliminarItem($index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->recalcularTotales();
        }
    }

    public function recalcularTotales(): void
    {
        // Calcular subtotal de items
        $this->subtotalItems = collect($this->items)->sum(function ($item) {
            return $item['cantidad'] * $item['precio'];
        });

        // Calcular descuentos
        $this->totalDescuentos = collect($this->items)->sum(function ($item) {
            $subtotal = $item['cantidad'] * $item['precio'];

            return $subtotal * ($item['descuento'] / 100);
        });

        // Calcular subtotal con descuento
        $this->subtotalConDescuento = $this->subtotalItems - $this->totalDescuentos;

        // Calcular IVA
        $this->montoIva = $this->aplicarIva ? $this->subtotalConDescuento * ($this->porcentajeIva / 100) : 0;

        // Calcular total
        $this->total = $this->subtotalConDescuento + $this->montoIva;
    }

    public function updatedAplicarIva(): void
    {
        $this->recalcularTotales();
    }

    public function updatedPorcentajeIva(): void
    {
        $this->recalcularTotales();
    }

    public function calcularSubtotalItem($item): float
    {
        $subtotal = $item['cantidad'] * $item['precio'];
        $descuento = $subtotal * ($item['descuento'] / 100);

        return $subtotal - $descuento;
    }

    public function abrirModalAgregarItem($tipo = 'servicio'): void
    {
        $this->tipoItemAgregar = $tipo;
        $this->busquedaItem = '';
        $this->itemsDisponibles = [];
        $this->mostrarModalAgregarItem = true;
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['equipo', 'fotos', 'notas'], true)) {
            $this->activeTab = $tab;
        }
    }

    // === Dispositivos: búsqueda y selección ===
    // UI de búsqueda de dispositivos fue eliminada

    public function selectDevice(int $deviceId): void
    {
        $device = Dispositivo::with(['modelo', 'cliente'])->find($deviceId);
        if (! $device) {
            return;
        }

        $this->selectedDeviceId = $device->id;
        // Mantener solo la selección
    }

    

    public function limpiarDispositivo(): void
    {
        $this->selectedDeviceId = null;
        // limpiar solo selección actual
    }

    public function abrirModalEditarDispositivo(): void
    {
        if (! $this->selectedDeviceId) {
            return;
        }

        $device = Dispositivo::with('modelo')->find($this->selectedDeviceId);
        if (! $device) {
            return;
        }

        $this->modeloSeleccionadoId = $device->modelo_id;
        $this->modeloSearchTerm = $device->modelo ? trim($device->modelo->marca . ' ' . $device->modelo->modelo . ' ' . ($device->modelo->anio ?: '')) : '';
        $this->modelosFound = [];
        $this->imeiDispositivo = $device->imei;
        $this->colorDispositivo = $device->color;
        $this->observacionesDispositivo = $device->observaciones;
        $this->mostrarModalEditarDispositivo = true;
    }

    public function guardarEdicionDispositivo(): void
    {
        if (! $this->selectedDeviceId) {
            return;
        }

        $this->validate([
            'modeloSeleccionadoId' => 'required|exists:modelos_dispositivos,id',
            'imeiDispositivo' => 'nullable|string|max:191',
            'colorDispositivo' => 'nullable|string|max:100',
            'observacionesDispositivo' => 'nullable|string|max:500',
        ]);

        $device = Dispositivo::find($this->selectedDeviceId);
        if (! $device) {
            return;
        }

        $device->update([
            'modelo_id' => $this->modeloSeleccionadoId,
            'imei' => $this->imeiDispositivo,
            'color' => $this->colorDispositivo,
            'observaciones' => $this->observacionesDispositivo,
        ]);

        $this->mostrarModalEditarDispositivo = false;
        $this->mostrarToastEquipoActualizado = true;
    }

    public function getDispositivosDelCliente(): array
    {
        if (! $this->selectedClientId) {
            return [];
        }

        return Dispositivo::query()
            ->where('cliente_id', $this->selectedClientId)
            ->with(['modelo'])
            ->latest('id')
            ->take(20)
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'imei' => $d->imei,
                    'color' => $d->color,
                    'modelo' => $d->modelo ? [
                        'id' => $d->modelo->id,
                        'marca' => $d->modelo->marca,
                        'modelo' => $d->modelo->modelo,
                        'anio' => $d->modelo->anio,
                    ] : null,
                ];
            })->toArray();
    }

    

    protected function calcularDispositivoSugerido(int $clientId): ?int
    {
        // Primero por última orden asociada, sino por más reciente creado
        $ultimoPorOrden = Dispositivo::where('cliente_id', $clientId)
            ->whereHas('ordenes')
            ->withMax('ordenes as last_order_created_at', 'created_at')
            ->orderByDesc('last_order_created_at')
            ->first();

        if ($ultimoPorOrden) {
            return $ultimoPorOrden->id;
        }

        $ultimo = Dispositivo::where('cliente_id', $clientId)->latest('id')->first();

        return $ultimo?->id;
    }

    public function getDispositivoSeleccionado(): ?array
    {
        if (! $this->selectedDeviceId) {
            return null;
        }

        $d = Dispositivo::with(['modelo', 'cliente'])->find($this->selectedDeviceId);
        if (! $d) {
            return null;
        }

        return [
            'id' => $d->id,
            'imei' => $d->imei,
            'color' => $d->color,
            'cliente' => $d->cliente ? [
                'id' => $d->cliente->id,
                'nombre' => $d->cliente->nombre,
            ] : null,
            'modelo' => $d->modelo ? [
                'id' => $d->modelo->id,
                'marca' => $d->modelo->marca,
                'modelo' => $d->modelo->modelo,
                'anio' => $d->modelo->anio,
            ] : null,
        ];
    }

    // === Creación de dispositivos ===
    public function abrirModalCrearDispositivo(string $modo = 'rapido'): void
    {
        $this->modoCreacionDispositivo = in_array($modo, ['rapido', 'completo'], true) ? $modo : 'rapido';
        $this->modeloSeleccionadoId = null;
        $this->modeloSearchTerm = '';
        $this->modelosFound = [];
        $this->imeiDispositivo = null;
        $this->colorDispositivo = null;
        $this->observacionesDispositivo = null;
        $this->mostrarModalCrearDispositivo = true;
    }

    public function abrirModalCrearModelo(): void
    {
        $this->modeloNuevoMarca = '';
        $this->modeloNuevoModelo = '';
        $this->modeloNuevoAnio = null;
        $this->modeloNuevoDescripcion = null;
        $this->mostrarModalCrearModelo = true;
    }

    public function crearModeloRapido(): void
    {
        $this->validate([
            'modeloNuevoMarca' => 'required|string|max:100',
            'modeloNuevoModelo' => 'required|string|max:150',
            'modeloNuevoAnio' => 'nullable|integer|min:1990|max:' . ((int) date('Y') + 2),
            'modeloNuevoDescripcion' => 'nullable|string|max:500',
        ]);

        $marca = trim((string) $this->modeloNuevoMarca);
        $modeloStr = trim((string) $this->modeloNuevoModelo);
        $anio = $this->modeloNuevoAnio;

        // Buscar duplicados por marca+modelo(+año) sin importar mayúsculas/minúsculas
        $exists = ModeloDispositivo::query()
            ->whereRaw('LOWER(marca) = ?', [mb_strtolower($marca)])
            ->whereRaw('LOWER(modelo) = ?', [mb_strtolower($modeloStr)])
            ->when($anio !== null, function ($q) use ($anio) {
                $q->where('anio', (int) $anio);
            }, function ($q) {
                $q->whereNull('anio');
            })
            ->first();

        if ($exists) {
            // Sugerir/usar existente: seleccionarlo automáticamente
            $this->modeloSeleccionadoId = $exists->id;
            $this->modeloSearchTerm = trim($exists->marca . ' ' . $exists->modelo . ' ' . ($exists->anio ?: ''));
            $this->modelosFound = [];
            $this->mostrarModalCrearModelo = false;

            return; // No crear duplicado
        }

        $modelo = ModeloDispositivo::create([
            'marca' => $marca,
            'modelo' => $modeloStr,
            'anio' => $anio,
            'descripcion' => $this->modeloNuevoDescripcion,
        ]);

        $this->modeloSeleccionadoId = $modelo->id;
        $this->modeloSearchTerm = trim($modelo->marca . ' ' . $modelo->modelo . ' ' . ($modelo->anio ?: ''));
        $this->modelosFound = [];
        $this->mostrarModalCrearModelo = false;
    }

    public function updatedModeloSearchTerm($value): void
    {
        $term = trim((string) $value);
        if ($term === '' || strlen($term) < 2) {
            $this->modelosFound = [];

            return;
        }

        $this->modelosFound = ModeloDispositivo::query()
            ->where(function ($q) use ($term) {
                $q->where('marca', 'like', '%' . $term . '%')
                  ->orWhere('modelo', 'like', '%' . $term . '%')
                  ->orWhere('anio', 'like', '%' . $term . '%');
            })
            ->orderByRaw("CASE WHEN marca LIKE ? THEN 0 ELSE 1 END", [$term . '%'])
            ->orderBy('marca')
            ->orderBy('modelo')
            ->take(10)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'marca' => $m->marca,
                    'modelo' => $m->modelo,
                    'anio' => $m->anio,
                ];
            })->toArray();
    }

    public function selectModelo(int $modeloId): void
    {
        $modelo = ModeloDispositivo::find($modeloId);
        if (! $modelo) {
            return;
        }

        $this->modeloSeleccionadoId = $modelo->id;
        $this->modeloSearchTerm = trim($modelo->marca . ' ' . $modelo->modelo . ' ' . ($modelo->anio ?: ''));
        $this->modelosFound = [];
    }

    public function crearDispositivoRapido(): void
    {
        $this->validate([
            'modeloSeleccionadoId' => 'required|exists:modelos_dispositivos,id',
            'imeiDispositivo' => 'nullable|string|max:191',
            'colorDispositivo' => 'nullable|string|max:100',
            'observacionesDispositivo' => 'nullable|string|max:500',
        ]);

        $dispositivo = Dispositivo::create([
            'cliente_id' => $this->selectedClientId,
            'modelo_id' => $this->modeloSeleccionadoId,
            'imei' => $this->imeiDispositivo,
            'color' => $this->colorDispositivo,
            'observaciones' => $this->observacionesDispositivo,
        ]);

        $this->selectedDeviceId = $dispositivo->id;
        // No hay campo de búsqueda que mostrar
        $this->mostrarModalCrearDispositivo = false;
    }

    public function crearDispositivoCompleto(): void
    {
        // Placeholder para modo completo si se requiere más adelante
        $this->crearDispositivoRapido();
    }

    // === Guardado de orden ===
    public function guardarOrden()
    {
        if ($this->fechaEntregaEstimada === '') {
            $this->fechaEntregaEstimada = null;
        }

        $estadoKeys = implode(',', array_keys($this->estadosDisponibles()));

        $this->validate([
            'selectedDeviceId' => 'required|exists:dispositivos,id',
            'asunto' => 'required|min:3|max:255',
            'tipoServicio' => 'required|in:reparacion,mantenimiento,garantia',
            'fechaIngreso' => 'required|date',
            'fechaEntregaEstimada' => 'nullable|date|after_or_equal:fechaIngreso',
            'estado' => 'required|in:' . $estadoKeys,
        ]);

        // Generar número de orden único (service)
        $numero = OrderNumberGenerator::generate();

        $dispositivo = Dispositivo::find($this->selectedDeviceId);

        $orden = \App\Models\OrdenTrabajo::create([
            'numero_orden' => $numero,
            'dispositivo_id' => $dispositivo->id,
            'tecnico_id' => auth()->id(),
            'fecha_ingreso' => $this->fechaIngreso,
            'fecha_entrega_estimada' => $this->fechaEntregaEstimada,
            'problema_reportado' => $this->asunto,
            'costo_estimado' => $this->total,
            'estado' => $this->estado,
        ]);

        // Guardar items en pivots
        foreach ($this->items as $item) {
            $cantidad = (int) ($item['cantidad'] ?? 1);
            $precio = (float) ($item['precio'] ?? 0);
            $descuento = (float) ($item['descuento'] ?? 0);
            $subtotal = max(0, $cantidad * $precio * (1 - ($descuento / 100)));

            if (($item['tipo'] ?? '') === 'servicio') {
                $orden->servicios()->attach($item['id'], [
                    'descripcion' => $item['nombre'] ?? null,
                    'precio_unitario' => $precio,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                ]);
            } elseif (($item['tipo'] ?? '') === 'producto') {
                $orden->productos()->attach($item['id'], [
                    'precio_unitario' => $precio,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                ]);
            }
        }

        // Redirigir a índice o detalle (por ahora índice)
        return redirect()->route('ordenes-trabajo.index');
    }

    // Método legacy eliminado: numeración delegada al service

    public function render()
    {
        return view('livewire.ordenes-trabajo.crear-orden', [
            'itemsDisponibles' => $this->itemsDisponibles,
            // Support data for equipo tab
            'dispositivosCliente' => $this->getDispositivosDelCliente(),
            'dispositivoSeleccionado' => $this->getDispositivoSeleccionado(),
            'estadosDisponibles' => $this->estadosDisponibles(),
        ]);
    }

    protected function estadosDisponibles(): array
    {
        return [
            'pendiente' => 'Pendiente',
            'diagnostico' => 'Diagnóstico',
            'en_reparacion' => 'En reparación',
            'espera_repuesto' => 'En espera de repuesto',
            'listo' => 'Listo',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
        ];
    }
}
