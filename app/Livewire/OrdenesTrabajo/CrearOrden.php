<?php

namespace App\Livewire\OrdenesTrabajo;

use App\Enums\EstadoOrden;
use App\Models\AccesorioConfig;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\ModeloDispositivo;
use App\Models\OrdenComentario;
use App\Models\OrdenTrabajo;
use App\Models\Producto;
use App\Models\Servicio;
use App\Services\OrderNumberGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

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

    private string $lastSelectedModeloName = '';

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

    // Anticipo y saldo
    public float $anticipo = 0;

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

    // Comentarios temporales (se guardarán cuando se cree la orden)
    public array $comentariosTemporales = [];

    public string $nuevoComentario = '';

    public string $tipoNuevoComentario = 'nota_interna';

    // Informe técnico
    public string $informeTecnico = '';

    // Accesorios configurables y seleccionados
    public array $accesoriosDisponibles = [];

    // Estructura: ['bolso' => true, 'cargador' => false, ...]
    public array $accesoriosSeleccionados = [];

    public function mount(): void
    {
        $this->fechaIngreso = now()->toDateString();
        $this->recalcularTotales();

        // Cargar accesorios disponibles (activos) e inicializar selecciones
        $this->cargarAccesoriosDisponibles();
        $this->inicializarAccesoriosSeleccionados();
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

    public bool $showModeloSearchResults = false;

    public ?string $imeiDispositivo = null;

    public ?string $colorDispositivo = null;

    public ?string $observacionesDispositivo = null;

    public string $tipoBloqueoDispositivo = 'ninguno'; // 'ninguno', 'patron', 'contraseña'

    public ?string $contraseñaDispositivo = null;

    public string $patronDispositivo = '';

    // Sugerencias
    public ?int $suggestedDeviceId = null;

    // Edición de dispositivo
    public bool $mostrarModalEditarDispositivo = false;

    public bool $mostrarToastDispositivoActualizado = false;

    public bool $mostrarToastEquipoActualizado = false;

    // Crear nuevo modelo (desde modal de dispositivo)
    public bool $mostrarModalCrearModelo = false;

    public string $modeloNuevoMarca = '';

    public string $modeloNuevoModelo = '';

    public ?int $modeloNuevoAnio = null;

    public ?string $modeloNuevoDescripcion = null;

    // Crear cliente inline
    public bool $mostrarModalCrearCliente = false;

    public string $clienteNuevoNombre = '';

    public string $clienteNuevoTelefono = '';

    public string $clienteNuevoEmail = '';

    public string $clienteNuevoDireccion = '';

    public string $clienteNuevoRut = '';

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

    public function mostrarClientesAlFocus(): void
    {
        // Si hay un cliente seleccionado, no mostrar resultados al hacer focus
        if ($this->selectedClientId) {
            $this->showClientSearchResults = false;

            return;
        }

        // Si hay texto, mostrar resultados si es válido
        $trimmedValue = trim($this->clientSearchTerm);
        if (strlen($trimmedValue) >= 2) {
            $this->showClientSearchResults = true;
        }
    }

    #[On('modeloSearchBlurred')]
    public function handleModeloSearchBlur(): void
    {
        if ($this->ignoringBlur) {
            return;
        }

        if ($this->showModeloSearchResults && $this->modeloSeleccionadoId === null) {
            $this->modeloSearchTerm = $this->lastSelectedModeloName;
        } elseif ($this->showModeloSearchResults && $this->modeloSeleccionadoId !== null) {
            $modelo = ModeloDispositivo::find($this->modeloSeleccionadoId);
            if ($modelo) {
                $modeloName = trim($modelo->marca.' '.$modelo->modelo.' '.($modelo->anio ?: ''));
                if ($this->modeloSearchTerm !== $modeloName) {
                    $this->modeloSearchTerm = $modeloName;
                }
            }
        }

        $this->showModeloSearchResults = false;
    }

    public function updatedClientSearchTerm($value): void
    {
        // Si hay un cliente seleccionado, no permitir cambios manuales
        if ($this->selectedClientId) {
            // Restaurar el valor del cliente seleccionado
            $client = Cliente::find($this->selectedClientId);
            if ($client && $this->clientSearchTerm !== $client->nombre) {
                $this->clientSearchTerm = $client->nombre;
            }
            $this->showClientSearchResults = false;
            $this->clientsFound = [];
            $this->loadingClients = false;

            return;
        }

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
            $q->where('nombre', 'like', '%'.$trimmedValue.'%')
                ->orWhere('rut', 'like', '%'.$trimmedValue.'%')
                ->orWhere('telefono', 'like', '%'.$trimmedValue.'%')
                ->orWhere('email', 'like', '%'.$trimmedValue.'%');
        });

        $this->clientsFound = $query->take(10)->get()->toArray();
        $this->loadingClients = false;
    }

    public function selectClient(int $clientId): void
    {
        $client = Cliente::find($clientId);

        if ($client) {
            // Resetear dispositivo si cambia el cliente
            $clienteAnteriorId = $this->selectedClientId;
            if ($clienteAnteriorId !== null && $clienteAnteriorId !== $client->id) {
                $this->limpiarDispositivo();
            }

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

            // Si hay un dispositivo seleccionado sin cliente, asociarlo al cliente recién seleccionado
            if ($this->selectedDeviceId) {
                $dispositivo = Dispositivo::find($this->selectedDeviceId);
                if ($dispositivo && ! $dispositivo->cliente_id) {
                    $dispositivo->update(['cliente_id' => $client->id]);
                }
            }

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
        // Resetear dispositivo al limpiar cliente
        $this->limpiarDispositivo();

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
                ->where('nombre', 'like', '%'.$valor.'%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->itemsDisponibles = Producto::query()
                ->where('estado', 'activo')
                ->where(function ($query) use ($valor) {
                    $query->where('nombre', 'like', '%'.$valor.'%')
                        ->orWhere('marca', 'like', '%'.$valor.'%');
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

        // Calcular total (costo estimado)
        $this->total = $this->subtotalConDescuento + $this->montoIva;

        // Validar anticipo después de recalcular totales (por si el total cambió)
        $this->validarAnticipo();
    }

    /**
     * Calcula el saldo pendiente basado en el total y el anticipo.
     */
    public function calcularSaldo(): float
    {
        $anticipo = $this->anticipo ?? 0;

        return max(0, round($this->total - $anticipo, 2));
    }

    public function updatedAplicarIva(): void
    {
        $this->recalcularTotales();
    }

    public function updatedPorcentajeIva(): void
    {
        $this->recalcularTotales();
    }

    public function updatedAnticipo($value): void
    {
        // Convertir NULL o valores vacíos a 0
        if ($value === null || $value === '') {
            $this->anticipo = 0;
        } else {
            $this->anticipo = max(0, (float) $value);
        }

        // Recalcular totales para actualizar el saldo visual
        $this->recalcularTotales();

        // Validar que el anticipo no sea mayor al total
        $this->validarAnticipo();
    }

    protected function validarAnticipo(): void
    {
        $this->resetErrorBag('anticipo');

        if ($this->anticipo > $this->total) {
            $this->addError('anticipo', 'El anticipo no puede ser mayor al total de la orden ($'.number_format($this->total, 0, ',', '.').').');
        }
    }

    public function updatedObservacionesDispositivo(): void
    {
        // Guardar automáticamente el estado del dispositivo cuando se actualiza
        if ($this->selectedDeviceId) {
            $device = Dispositivo::find($this->selectedDeviceId);
            if ($device) {
                $device->update([
                    'estado_dispositivo' => $this->observacionesDispositivo,
                ]);
            }
        }
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
        if (in_array($tab, ['equipo', 'notas', 'informe-tecnico'], true)) {
            $this->activeTab = $tab;
        }
    }

    protected function cargarAccesoriosDisponibles(): void
    {
        $this->accesoriosDisponibles = AccesorioConfig::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($acc) {
                return [
                    'id' => $acc->id,
                    'nombre' => $acc->nombre,
                    'clave' => $this->claveAccesorio($acc->nombre),
                ];
            })
            ->toArray();
    }

    protected function inicializarAccesoriosSeleccionados(?array $desde = null): void
    {
        $map = [];
        foreach ($this->accesoriosDisponibles as $acc) {
            $clave = $acc['clave'];
            $map[$clave] = (bool) ($desde[$clave] ?? false);
        }
        if ($desde) {
            foreach ($desde as $k => $v) {
                if (! array_key_exists($k, $map)) {
                    $map[$k] = (bool) $v;
                }
            }
        }
        $this->accesoriosSeleccionados = $map;
    }

    protected function claveAccesorio(string $nombre): string
    {
        return Str::slug($nombre, '_');
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
        // Cargar el estado del dispositivo en el textarea
        $this->observacionesDispositivo = $device->estado_dispositivo;

        // Cargar accesorios seleccionados del dispositivo
        $this->cargarAccesoriosDisponibles();
        $this->inicializarAccesoriosSeleccionados((array) ($device->accesorios ?? []));
    }

    public function limpiarDispositivo(): void
    {
        $this->selectedDeviceId = null;
        $this->observacionesDispositivo = null;
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
        $modeloName = $device->modelo ? trim($device->modelo->marca.' '.$device->modelo->modelo.' '.($device->modelo->anio ?: '')) : '';
        $this->modeloSearchTerm = $modeloName;
        $this->lastSelectedModeloName = $modeloName;
        $this->modelosFound = [];
        $this->showModeloSearchResults = false;
        $this->imeiDispositivo = $device->imei;
        $this->colorDispositivo = $device->color;
        $this->observacionesDispositivo = $device->estado_dispositivo;

        // Cargar tipo de bloqueo y valores
        if ($device->pattern_encrypted) {
            $this->tipoBloqueoDispositivo = 'patron';
            try {
                $this->patronDispositivo = \Illuminate\Support\Facades\Crypt::decryptString($device->pattern_encrypted);
            } catch (\Exception $e) {
                $this->patronDispositivo = '';
            }
        } elseif ($device->contraseña) {
            $this->tipoBloqueoDispositivo = 'contraseña';
            $this->contraseñaDispositivo = $device->contraseña;
        } else {
            $this->tipoBloqueoDispositivo = 'ninguno';
            $this->patronDispositivo = '';
            $this->contraseñaDispositivo = null;
        }

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
            'tipoBloqueoDispositivo' => 'required|in:ninguno,patron,contraseña',
            'contraseñaDispositivo' => 'nullable|string|max:255',
            'patronDispositivo' => 'nullable|string',
        ]);

        // Validar patrón si se selecciona
        if ($this->tipoBloqueoDispositivo === 'patron' && $this->patronDispositivo !== '' && count(explode('-', $this->patronDispositivo)) < 3) {
            $this->addError('patronDispositivo', 'El patrón debe tener al menos 3 puntos.');

            return;
        }

        $device = Dispositivo::find($this->selectedDeviceId);
        if (! $device) {
            return;
        }

        $updateData = [
            'modelo_id' => $this->modeloSeleccionadoId,
            'imei' => $this->imeiDispositivo,
            'color' => $this->colorDispositivo,
            'estado_dispositivo' => $this->observacionesDispositivo,
            'accesorios' => $this->accesoriosSeleccionados,
        ];

        // Manejar tipo de bloqueo
        if ($this->tipoBloqueoDispositivo === 'patron') {
            if ($this->patronDispositivo === '') {
                $updateData['pattern_encrypted'] = null;
            } else {
                $updateData['pattern_encrypted'] = \Illuminate\Support\Facades\Crypt::encryptString($this->patronDispositivo);
            }
            $updateData['contraseña'] = null;
        } elseif ($this->tipoBloqueoDispositivo === 'contraseña') {
            $updateData['contraseña'] = $this->contraseñaDispositivo;
            $updateData['pattern_encrypted'] = null;
        } else {
            $updateData['contraseña'] = null;
            $updateData['pattern_encrypted'] = null;
        }

        $device->update($updateData);

        // Disparar evento para refrescar el componente de patrón
        $this->dispatch('patronActualizado');

        $this->mostrarModalEditarDispositivo = false;
        $this->mostrarToastEquipoActualizado = true;
        $this->mostrarToastDispositivoActualizado = true;
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
        $this->lastSelectedModeloName = '';
        $this->modelosFound = [];
        $this->showModeloSearchResults = false;
        $this->imeiDispositivo = null;
        $this->colorDispositivo = null;
        $this->observacionesDispositivo = null;
        $this->tipoBloqueoDispositivo = 'ninguno';
        $this->contraseñaDispositivo = null;
        $this->patronDispositivo = '';
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
            'modeloNuevoAnio' => 'nullable|integer|min:1990|max:'.((int) date('Y') + 2),
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
            $this->modeloSearchTerm = trim($exists->marca.' '.$exists->modelo.' '.($exists->anio ?: ''));
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
        $modeloName = trim($modelo->marca.' '.$modelo->modelo.' '.($modelo->anio ?: ''));
        $this->modeloSearchTerm = $modeloName;
        $this->lastSelectedModeloName = $modeloName;
        $this->modelosFound = [];
        $this->showModeloSearchResults = false;
        $this->mostrarModalCrearModelo = false;
    }

    public function updatedModeloSearchTerm($value): void
    {
        // Si hay un modelo seleccionado, no permitir cambios manuales
        if ($this->modeloSeleccionadoId) {
            // Restaurar el valor del modelo seleccionado
            $modelo = ModeloDispositivo::find($this->modeloSeleccionadoId);
            if ($modelo) {
                $modeloName = trim($modelo->marca.' '.$modelo->modelo.' '.($modelo->anio ?: ''));
                if ($this->modeloSearchTerm !== $modeloName) {
                    $this->modeloSearchTerm = $modeloName;
                }
            }
            $this->showModeloSearchResults = false;

            return;
        }

        $term = trim((string) $value);

        if ($term === '' || strlen($term) < 2) {
            // Si está vacío o tiene menos de 2 caracteres, cargar los primeros 10
            $this->cargarModelosIniciales();

            return;
        }

        $this->showModeloSearchResults = true;

        $this->modelosFound = ModeloDispositivo::query()
            ->where(function ($q) use ($term) {
                $q->where('marca', 'like', '%'.$term.'%')
                    ->orWhere('modelo', 'like', '%'.$term.'%')
                    ->orWhere('anio', 'like', '%'.$term.'%');
            })
            ->orderByRaw('CASE WHEN marca LIKE ? THEN 0 ELSE 1 END', [$term.'%'])
            ->orderBy('marca')
            ->orderBy('modelo')
            ->take(20)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'marca' => $m->marca,
                    'modelo' => $m->modelo,
                    'anio' => $m->anio,
                    'label' => trim($m->marca.' '.$m->modelo.($m->anio ? ' ('.$m->anio.')' : '')),
                ];
            })->toArray();
    }

    public function cargarModelosIniciales(): void
    {
        $this->showModeloSearchResults = true;

        $this->modelosFound = ModeloDispositivo::query()
            ->orderBy('marca')
            ->orderBy('modelo')
            ->orderBy('anio')
            ->take(10)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'marca' => $m->marca,
                    'modelo' => $m->modelo,
                    'anio' => $m->anio,
                    'label' => trim($m->marca.' '.$m->modelo.($m->anio ? ' ('.$m->anio.')' : '')),
                ];
            })->toArray();
    }

    public function mostrarModelosAlFocus(): void
    {
        // Si hay un modelo seleccionado, no mostrar resultados al hacer focus
        if ($this->modeloSeleccionadoId) {
            $this->showModeloSearchResults = false;

            return;
        }

        if (empty(trim($this->modeloSearchTerm)) || strlen(trim($this->modeloSearchTerm)) < 2) {
            $this->cargarModelosIniciales();
        } else {
            $this->showModeloSearchResults = true;
        }
    }

    public function clearModeloSearchResults(): void
    {
        $this->showModeloSearchResults = false;
    }

    public function selectModelo(int $modeloId): void
    {
        $modelo = ModeloDispositivo::find($modeloId);
        if (! $modelo) {
            return;
        }

        $this->modeloSeleccionadoId = $modelo->id;
        $modeloName = trim($modelo->marca.' '.$modelo->modelo.' '.($modelo->anio ?: ''));
        $this->modeloSearchTerm = $modeloName;
        $this->lastSelectedModeloName = $modeloName;
        $this->modelosFound = [];
        $this->showModeloSearchResults = false;
    }

    public function limpiarModelo(): void
    {
        $this->modeloSeleccionadoId = null;
        $this->modeloSearchTerm = '';
        $this->lastSelectedModeloName = '';
        $this->modelosFound = [];
        $this->showModeloSearchResults = false;
    }

    public function getModelosDisponiblesProperty(): array
    {
        return ModeloDispositivo::query()
            ->orderBy('marca')
            ->orderBy('modelo')
            ->orderBy('anio')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'marca' => $m->marca,
                    'modelo' => $m->modelo,
                    'anio' => $m->anio,
                    'label' => trim($m->marca.' '.$m->modelo.($m->anio ? ' ('.$m->anio.')' : '')),
                ];
            })->toArray();
    }

    public function crearDispositivoRapido(): void
    {
        $this->validate([
            'modeloSeleccionadoId' => 'required|exists:modelos_dispositivos,id',
            'imeiDispositivo' => 'nullable|string|max:191',
            'colorDispositivo' => 'nullable|string|max:100',
            'observacionesDispositivo' => 'nullable|string|max:500',
            'tipoBloqueoDispositivo' => 'required|in:ninguno,patron,contraseña',
            'contraseñaDispositivo' => 'nullable|string|max:255',
            'patronDispositivo' => 'nullable|string',
        ]);

        // Validar patrón si se selecciona
        if ($this->tipoBloqueoDispositivo === 'patron' && $this->patronDispositivo !== '' && count(explode('-', $this->patronDispositivo)) < 3) {
            $this->addError('patronDispositivo', 'El patrón debe tener al menos 3 puntos.');

            return;
        }

        $createData = [
            'cliente_id' => $this->selectedClientId,
            'modelo_id' => $this->modeloSeleccionadoId,
            'imei' => $this->imeiDispositivo,
            'color' => $this->colorDispositivo,
            'estado_dispositivo' => $this->observacionesDispositivo,
            'accesorios' => $this->accesoriosSeleccionados,
        ];

        // Manejar tipo de bloqueo
        if ($this->tipoBloqueoDispositivo === 'patron' && $this->patronDispositivo !== '') {
            $createData['pattern_encrypted'] = \Illuminate\Support\Facades\Crypt::encryptString($this->patronDispositivo);
        } elseif ($this->tipoBloqueoDispositivo === 'contraseña') {
            $createData['contraseña'] = $this->contraseñaDispositivo;
        }

        $dispositivo = Dispositivo::create($createData);

        $this->selectedDeviceId = $dispositivo->id;
        $this->mostrarModalCrearDispositivo = false;

        // Disparar evento para refrescar el componente de patrón si se creó con patrón
        if ($this->tipoBloqueoDispositivo === 'patron' && $this->patronDispositivo !== '') {
            $this->dispatch('patronActualizado');
        }
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

        // Recalcular totales antes de guardar para asegurar que estén actualizados
        $this->recalcularTotales();

        // Validaciones personalizadas con mensajes descriptivos
        $this->validarClienteYDispositivo();
        $this->validarItems();
        $this->validarAnticipo();

        // Si hay errores de validación personalizados, detener la ejecución
        if ($this->getErrorBag()->hasAny(['selectedClientId', 'selectedDeviceId', 'items', 'anticipo'])) {
            return;
        }

        $estadoKeys = implode(',', array_keys(OrdenTrabajo::estadosDisponibles()));

        $this->validate([
            'selectedDeviceId' => 'required|exists:dispositivos,id',
            'asunto' => 'required|min:3|max:255',
            'tipoServicio' => 'required|in:reparacion,mantenimiento,garantia',
            'fechaIngreso' => 'required|date',
            'fechaEntregaEstimada' => 'nullable|date|after_or_equal:fechaIngreso',
            'estado' => 'required|in:'.$estadoKeys,
            'anticipo' => 'nullable|numeric|min:0|max:999999.99',
        ]);

        // Generar número de orden único (service)
        $numero = OrderNumberGenerator::generate();

        $dispositivo = Dispositivo::find($this->selectedDeviceId);

        // Calcular saldo: costo estimado menos anticipo
        $saldo = $this->calcularSaldo();

        // Encapsular la creación de la orden y los registros pivot en una transacción
        $orden = DB::transaction(function () use ($numero, $dispositivo, $saldo) {
            // Crear la orden
            $orden = OrdenTrabajo::create([
                'numero_orden' => $numero,
                'dispositivo_id' => $dispositivo->id,
                'tecnico_id' => auth()->id(),
                'fecha_ingreso' => $this->fechaIngreso,
                'fecha_entrega_estimada' => $this->fechaEntregaEstimada,
                'problema_reportado' => $this->asunto,
                'costo_estimado' => round($this->total, 2),
                'anticipo' => round($this->anticipo, 2),
                'saldo' => $saldo,
                'estado' => EstadoOrden::from($this->estado),
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

            // Crear comentarios temporales si existen
            foreach ($this->comentariosTemporales as $comentarioTemp) {
                OrdenComentario::create([
                    'orden_id' => $orden->id,
                    'user_id' => auth()->id(),
                    'comentario' => $comentarioTemp['comentario'],
                    'tipo' => $comentarioTemp['tipo'],
                ]);
            }

            // Crear informe técnico si existe
            if (! empty(trim($this->informeTecnico))) {
                OrdenComentario::create([
                    'orden_id' => $orden->id,
                    'user_id' => auth()->id(),
                    'comentario' => trim($this->informeTecnico),
                    'tipo' => 'informe_tecnico',
                ]);
            }

            return $orden;
        });

        // Redirigir a índice o detalle (por ahora índice)
        return redirect()->route('ordenes-trabajo.index');
    }

    public function getPuedeEnviarComentarioProperty(): bool
    {
        return strlen(trim($this->nuevoComentario ?? '')) >= 3;
    }

    public function agregarComentario(): void
    {
        $this->validate([
            'nuevoComentario' => 'required|min:3|max:1000',
            'tipoNuevoComentario' => 'required|in:nota_interna,comentario_cliente,informe_tecnico',
        ]);

        // Agregar comentario temporal (se guardará cuando se cree la orden)
        $this->comentariosTemporales[] = [
            'comentario' => trim($this->nuevoComentario),
            'tipo' => $this->tipoNuevoComentario,
            'user' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
            ],
            'created_at' => now()->format('d/m/Y H:i'),
        ];

        $this->nuevoComentario = '';
    }

    // Método legacy eliminado: numeración delegada al service

    public function cambiarPatron(): void
    {
        if (! $this->selectedDeviceId) {
            return;
        }

        $device = Dispositivo::find($this->selectedDeviceId);
        if (! $device) {
            return;
        }

        // Determinar el tipo de bloqueo actual
        if ($device->pattern_encrypted) {
            $this->tipoBloqueoDispositivo = 'patron';
            try {
                $this->patronDispositivo = \Illuminate\Support\Facades\Crypt::decryptString($device->pattern_encrypted);
            } catch (\Exception $e) {
                $this->patronDispositivo = '';
            }
            $this->contraseñaDispositivo = null;
        } elseif ($device->contraseña) {
            $this->tipoBloqueoDispositivo = 'contraseña';
            $this->contraseñaDispositivo = $device->contraseña;
            $this->patronDispositivo = '';
        } else {
            $this->tipoBloqueoDispositivo = 'patron';
            $this->patronDispositivo = '';
            $this->contraseñaDispositivo = null;
        }

        // Disparar evento para refrescar el componente
        $this->dispatch('patronActualizado');

        // Abrir modal de editar dispositivo
        $this->abrirModalEditarDispositivo();
    }

    public function render()
    {
        return view('livewire.ordenes-trabajo.crear-orden', [
            'itemsDisponibles' => $this->itemsDisponibles,
            // Support data for equipo tab
            'dispositivosCliente' => $this->getDispositivosDelCliente(),
            'dispositivoSeleccionado' => $this->getDispositivoSeleccionado(),
            'estadosDisponibles' => OrdenTrabajo::estadosDisponibles(),
        ]);
    }

    // Guardado en caliente de accesorios cuando hay dispositivo seleccionado
    public function updatedAccesoriosSeleccionados(): void
    {
        if ($this->selectedDeviceId) {
            $device = Dispositivo::find($this->selectedDeviceId);
            if ($device) {
                $device->update([
                    'accesorios' => $this->accesoriosSeleccionados,
                ]);
            }
        }
    }

    public function toggleAccesorio(string $clave): void
    {
        if (! $this->selectedDeviceId) {
            return;
        }

        // Toggle del estado del accesorio
        $this->accesoriosSeleccionados[$clave] = ! ($this->accesoriosSeleccionados[$clave] ?? false);

        // Guardar en caliente si hay dispositivo seleccionado
        $device = Dispositivo::find($this->selectedDeviceId);
        if ($device) {
            $device->update([
                'accesorios' => $this->accesoriosSeleccionados,
            ]);
        }
    }

    /**
     * Valida que el cliente exista (si está seleccionado) y que el dispositivo pertenezca al mismo cliente.
     * Nota: Se permite guardar órdenes con dispositivo sin cliente seleccionado.
     */
    protected function validarClienteYDispositivo(): void
    {
        // Validar que el cliente exista SOLO si hay un cliente seleccionado
        if ($this->selectedClientId !== null) {
            $cliente = Cliente::find($this->selectedClientId);
            if (! $cliente) {
                $this->addError('selectedClientId', 'El cliente seleccionado no existe en el sistema.');

                return;
            }

            // Si hay cliente seleccionado, debe haber un dispositivo seleccionado
            if ($this->selectedDeviceId === null) {
                $this->addError('selectedDeviceId', 'Debe seleccionar un dispositivo para el cliente seleccionado antes de guardar la orden.');

                return;
            }
        }

        // Validar que el dispositivo exista
        if ($this->selectedDeviceId !== null) {
            $dispositivo = Dispositivo::find($this->selectedDeviceId);
            if (! $dispositivo) {
                $this->addError('selectedDeviceId', 'El dispositivo seleccionado no existe en el sistema.');

                return;
            }

            // Solo validar pertenencia si AMBOS cliente y dispositivo están seleccionados
            // Si hay dispositivo sin cliente seleccionado, se permite (caso válido)
            if ($this->selectedClientId !== null && $dispositivo->cliente_id !== $this->selectedClientId) {
                $this->addError('selectedDeviceId', 'El dispositivo seleccionado no pertenece al cliente seleccionado. Por favor, seleccione un dispositivo del cliente correcto.');

                return;
            }
        }
    }

    /**
     * Valida que haya al menos un ítem en la orden.
     */
    protected function validarItems(): void
    {
        if (empty($this->items) || count($this->items) === 0) {
            $this->addError('items', 'Debe agregar al menos un servicio o producto a la orden antes de guardarla.');
        }
    }

    // === Creación de clientes inline ===
    public function abrirModalCrearCliente(): void
    {
        $this->clienteNuevoNombre = '';
        $this->clienteNuevoTelefono = '';
        $this->clienteNuevoEmail = '';
        $this->clienteNuevoDireccion = '';
        $this->clienteNuevoRut = '';
        $this->mostrarModalCrearCliente = true;
    }

    public function crearClienteRapido(): void
    {
        $email = trim($this->clienteNuevoEmail);
        $rut = trim($this->clienteNuevoRut);

        $rules = [
            'clienteNuevoNombre' => 'required|string|max:255',
            'clienteNuevoTelefono' => 'nullable|string|max:255',
            'clienteNuevoDireccion' => 'nullable|string|max:255',
        ];

        // Solo validar email si no está vacío
        if ($email !== '') {
            $rules['clienteNuevoEmail'] = 'email|max:255|unique:clientes,email';
        }

        // Solo validar RUT si no está vacío
        if ($rut !== '') {
            $rules['clienteNuevoRut'] = 'string|max:255|unique:clientes,rut';
        }

        $this->validate($rules, [
            'clienteNuevoNombre.required' => 'El nombre es obligatorio.',
            'clienteNuevoEmail.email' => 'El email debe ser válido.',
            'clienteNuevoEmail.unique' => 'Este email ya está registrado.',
            'clienteNuevoRut.unique' => 'Este RUT ya está registrado.',
        ]);

        $cliente = Cliente::create([
            'nombre' => trim($this->clienteNuevoNombre),
            'telefono' => trim($this->clienteNuevoTelefono) ?: null,
            'email' => $email ?: null,
            'direccion' => trim($this->clienteNuevoDireccion) ?: null,
            'rut' => $rut ?: null,
        ]);

        $this->mostrarModalCrearCliente = false;

        // Seleccionar automáticamente el cliente creado
        $this->selectClient($cliente->id);
    }
}
