<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use Livewire\Component;

class EditarProducto extends Component
{
    public int $productoId;

    public $nombre;
    public $descripcion;
    public $categoria;
    public $marca;
    public $precio_compra;
    public $precio_venta;
    public $stock = 0;
    public $stock_minimo = 0;
    public $proveedor_id;
    public $estado = 'activo';
    public $fecha_ingreso;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string',
        'categoria' => 'nullable|string|max:50',
        'marca' => 'nullable|string|max:50',
        'precio_compra' => 'required|numeric|min:0',
        'precio_venta' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'stock_minimo' => 'required|integer|min:0',
        'proveedor_id' => 'nullable|integer|min:1',
        'estado' => 'required|in:activo,inactivo',
        'fecha_ingreso' => 'nullable|date',
    ];

    public function mount(int $id): void
    {
        $this->productoId = $id;

        $producto = Producto::find($id);
        if (! $producto) {
            return; // permite mostrar la página aunque no exista
        }

        $this->nombre = $producto->nombre;
        $this->descripcion = $producto->descripcion;
        $this->categoria = $producto->categoria;
        $this->marca = $producto->marca;
        $this->precio_compra = $producto->precio_compra;
        $this->precio_venta = $producto->precio_venta;
        $this->stock = $producto->stock;
        $this->stock_minimo = $producto->stock_minimo;
        $this->proveedor_id = $producto->proveedor_id;
        $this->estado = $producto->estado ?? 'activo';
        $this->fecha_ingreso = optional($producto->fecha_ingreso)->format('Y-m-d');
    }

    public function update()
    {
        $this->validate();

        $producto = Producto::findOrFail($this->productoId);
        $producto->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'marca' => $this->marca,
            'precio_compra' => $this->precio_compra,
            'precio_venta' => $this->precio_venta,
            'stock' => $this->stock,
            'stock_minimo' => $this->stock_minimo,
            'proveedor_id' => $this->proveedor_id,
            'estado' => $this->estado,
            'fecha_ingreso' => $this->fecha_ingreso,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function render()
    {
        return view('livewire.productos.editar-producto');
    }
}
