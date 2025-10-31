<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use Livewire\Component;

class CrearProducto extends Component
{
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

    public function save()
    {
        $this->validate();

        Producto::create([
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

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function render()
    {
        return view('livewire.productos.crear-producto');
    }
}
