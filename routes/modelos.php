<?php

use App\Livewire\ModelosDispositivos\CrearModeloDispositivo;
use App\Livewire\ModelosDispositivos\EditarModeloDispositivo;
use App\Livewire\ModelosDispositivos\Index as ModelosDispositivosIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', ModelosDispositivosIndex::class)
    ->name('modelos.index')
    ->middleware(['auth', 'web']);

Route::get('/crear', CrearModeloDispositivo::class)
    ->name('modelos.crear')
    ->middleware(['auth', 'web']);

Route::get('/{id}/editar', EditarModeloDispositivo::class)
    ->name('modelos.editar')
    ->middleware(['auth', 'web']);
