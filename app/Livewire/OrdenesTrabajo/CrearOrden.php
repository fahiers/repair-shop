<?php

namespace App\Livewire\OrdenesTrabajo;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Servicio;
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

    public bool $ignoringBlur = false;

    public string $rut = '';

    // Tipo de servicio
    #[Validate('required|in:reparacion,mantenimiento,garantia')]
    public string $tipoServicio = 'reparacion';

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
        $this->recalcularTotales();
    }

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

    public function render()
    {
        return view('livewire.ordenes-trabajo.crear-orden', [
            'itemsDisponibles' => $this->itemsDisponibles,
        ]);
    }
}
