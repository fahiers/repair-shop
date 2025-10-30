<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Clientes\Index;
use App\Livewire\Clientes\CrearCliente;
use App\Livewire\Clientes\EditarCliente;

Route::get('/', Index::class)
    ->name('clientes.index')
    ->middleware(['auth', 'web']);

Route::get('/crear', CrearCliente::class)
    ->name('clientes.crear')
    ->middleware(['auth', 'web']);

Route::get('/{cliente}/editar', EditarCliente::class)
    ->name('clientes.editar')
    ->middleware(['auth', 'web']);

