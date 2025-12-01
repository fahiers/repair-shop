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

    protected $messages = [
        'nombre.required' => 'El campo nombre es obligatorio.',
        'nombre.string' => 'El campo nombre debe ser texto.',
        'nombre.max' => 'El campo nombre no puede tener más de 100 caracteres.',
        'descripcion.string' => 'El campo descripción debe ser texto.',
        'categoria.string' => 'El campo categoría debe ser texto.',
        'categoria.max' => 'El campo categoría no puede tener más de 50 caracteres.',
        'marca.string' => 'El campo marca debe ser texto.',
        'marca.max' => 'El campo marca no puede tener más de 50 caracteres.',
        'precio_compra.required' => 'El campo precio de compra es obligatorio.',
        'precio_compra.numeric' => 'El campo precio de compra debe ser un número.',
        'precio_compra.min' => 'El campo precio de compra debe ser mayor o igual a 0.',
        'precio_venta.required' => 'El campo precio de venta es obligatorio.',
        'precio_venta.numeric' => 'El campo precio de venta debe ser un número.',
        'precio_venta.min' => 'El campo precio de venta debe ser mayor o igual a 0.',
        'stock.required' => 'El campo stock es obligatorio.',
        'stock.integer' => 'El campo stock debe ser un número entero.',
        'stock.min' => 'El campo stock debe ser mayor o igual a 0.',
        'stock_minimo.required' => 'El campo stock mínimo es obligatorio.',
        'stock_minimo.integer' => 'El campo stock mínimo debe ser un número entero.',
        'stock_minimo.min' => 'El campo stock mínimo debe ser mayor o igual a 0.',
        'proveedor_id.integer' => 'El campo proveedor debe ser un número entero.',
        'proveedor_id.min' => 'El campo proveedor debe ser mayor o igual a 1.',
        'estado.required' => 'El campo estado es obligatorio.',
        'estado.in' => 'El campo estado debe ser activo o inactivo.',
        'fecha_ingreso.date' => 'El campo fecha de ingreso debe ser una fecha válida.',
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
