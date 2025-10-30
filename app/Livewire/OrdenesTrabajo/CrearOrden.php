<?php

namespace App\Livewire\OrdenesTrabajo;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Servicio;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CrearOrden extends Component
{
    // Cliente y búsqueda
    public $clienteSeleccionado = null;

    public $busquedaCliente = '';

    public $mostrarModalCliente = false;

    // Tipo de servicio
    #[Validate('required|in:reparacion,mantenimiento,garantia')]
    public $tipoServicio = 'reparacion';

    // Asunto/Descripción
    #[Validate('required|min:3|max:255')]
    public $asunto = '';

    // Items (servicios/productos)
    public $items = [];

    public $mostrarModalAgregarItem = false;

    public $tipoItemAgregar = 'servicio'; // servicio o producto

    // Busqueda de items
    public $busquedaItem = '';

    // IVA
    public $aplicarIva = true;

    public $porcentajeIva = 19;

    public function mount(): void
    {
        // Inicializar con datos vacíos
    }

    #[Computed]
    public function clientesBuscados()
    {
        if (strlen($this->busquedaCliente) < 2) {
            return [];
        }

        return Cliente::query()
            ->where('nombre', 'like', '%'.$this->busquedaCliente.'%')
            ->orWhere('telefono', 'like', '%'.$this->busquedaCliente.'%')
            ->orWhere('email', 'like', '%'.$this->busquedaCliente.'%')
            ->orWhere('rut', 'like', '%'.$this->busquedaCliente.'%')
            ->limit(5)
            ->get();
    }

    public function seleccionarCliente($clienteId): void
    {
        $this->clienteSeleccionado = Cliente::find($clienteId);
        $this->busquedaCliente = '';
    }

    public function limpiarCliente(): void
    {
        $this->clienteSeleccionado = null;
        $this->busquedaCliente = '';
    }

    #[Computed]
    public function itemsDisponibles()
    {
        if (strlen($this->busquedaItem) < 2) {
            return [];
        }

        if ($this->tipoItemAgregar === 'servicio') {
            return Servicio::query()
                ->where('estado', 'activo')
                ->where('nombre', 'like', '%'.$this->busquedaItem.'%')
                ->limit(10)
                ->get();
        }

        return Producto::query()
            ->where('estado', 'activo')
            ->where(function ($query) {
                $query->where('nombre', 'like', '%'.$this->busquedaItem.'%')
                    ->orWhere('marca', 'like', '%'.$this->busquedaItem.'%');
            })
            ->limit(10)
            ->get();
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
        $this->mostrarModalAgregarItem = false;
    }

    public function actualizarCantidad($index, $cantidad): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        $this->items[$index]['cantidad'] = max(1, (int) $cantidad);
    }

    public function actualizarPrecio($index, $precio): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        $this->items[$index]['precio'] = max(0, (float) $precio);
    }

    public function actualizarDescuento($index, $descuento): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        $this->items[$index]['descuento'] = min(100, max(0, (float) $descuento));
    }

    public function eliminarItem($index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Reindexar
        }
    }

    #[Computed]
    public function subtotalItems(): float
    {
        return collect($this->items)->sum(function ($item) {
            return $item['cantidad'] * $item['precio'];
        });
    }

    #[Computed]
    public function totalDescuentos(): float
    {
        return collect($this->items)->sum(function ($item) {
            $subtotal = $item['cantidad'] * $item['precio'];

            return $subtotal * ($item['descuento'] / 100);
        });
    }

    #[Computed]
    public function subtotalConDescuento(): float
    {
        return $this->subtotalItems - $this->totalDescuentos;
    }

    #[Computed]
    public function montoIva(): float
    {
        if (! $this->aplicarIva) {
            return 0;
        }

        return $this->subtotalConDescuento * ($this->porcentajeIva / 100);
    }

    #[Computed]
    public function total(): float
    {
        return $this->subtotalConDescuento + $this->montoIva;
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
        $this->mostrarModalAgregarItem = true;
    }

    public function render()
    {
        return view('livewire.ordenes-trabajo.crear-orden');
    }
}
