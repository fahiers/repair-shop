<?php

use App\Livewire\Dispositivos\Edit;
use App\Livewire\Dispositivos\HistorialClinico;
use App\Livewire\Dispositivos\Index;
use Illuminate\Support\Facades\Route;

Route::get('/', Index::class)->name('dispositivos.index');
Route::get('/{dispositivo}/historial-clinico', HistorialClinico::class)->name('dispositivos.historial-clinico');
Route::get('/{dispositivo}/edit', Edit::class)->name('dispositivos.edit');
