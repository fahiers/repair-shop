<?php

use App\Livewire\Productos\CrearProducto;
use App\Livewire\Productos\EditarProducto;
use App\Livewire\Productos\Index as ProductosIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', ProductosIndex::class)
    ->name('productos.index')
    ->middleware(['auth', 'web']);

Route::get('/crear', CrearProducto::class)
    ->name('productos.crear')
    ->middleware(['auth', 'web']);

Route::get('/{id}/editar', EditarProducto::class)
    ->name('productos.editar')
    ->middleware(['auth', 'web']);
