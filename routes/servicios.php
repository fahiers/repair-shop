<?php

use App\Livewire\Servicios\CrearServicio;
use App\Livewire\Servicios\EditarServicio;
use App\Livewire\Servicios\Index as ServiciosIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', ServiciosIndex::class)
    ->name('servicios.index')
    ->middleware(['auth', 'web']);

Route::get('/crear', CrearServicio::class)
    ->name('servicios.crear')
    ->middleware(['auth', 'web']);

Route::get('/{id}/editar', EditarServicio::class)
    ->name('servicios.editar')
    ->middleware(['auth', 'web']);
