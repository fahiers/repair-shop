<?php

use App\Livewire\OrdenesTrabajo\Index as OrdenesTrabajoIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', OrdenesTrabajoIndex::class)
    ->name('ordenes-trabajo.index')
    ->middleware(['auth','web']);
