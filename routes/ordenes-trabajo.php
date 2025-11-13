<?php

use App\Http\Controllers\OrdenTrabajoPdfController;
use App\Livewire\OrdenesTrabajo\CrearOrden;
use App\Livewire\OrdenesTrabajo\EditarOrden;
use App\Livewire\OrdenesTrabajo\Index as OrdenesTrabajoIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', OrdenesTrabajoIndex::class)
    ->name('ordenes-trabajo.index')
    ->middleware(['auth', 'web']);

Route::get('/crear', CrearOrden::class)
    ->name('ordenes-trabajo.crear')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/pdf', [OrdenTrabajoPdfController::class, 'preview'])
    ->name('ordenes-trabajo.pdf')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/pdf/download', [OrdenTrabajoPdfController::class, 'download'])
    ->name('ordenes-trabajo.pdf.download')
    ->middleware(['auth', 'web']);

Route::get('/{id}/editar', EditarOrden::class)
    ->name('ordenes-trabajo.editar')
    ->middleware(['auth', 'web']);
