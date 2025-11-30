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

Route::get('/{orden}/informe-tecnico', [OrdenTrabajoPdfController::class, 'previewInformeTecnico'])
    ->name('ordenes-trabajo.informe-tecnico')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/informe-tecnico/download', [OrdenTrabajoPdfController::class, 'downloadInformeTecnico'])
    ->name('ordenes-trabajo.informe-tecnico.download')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/sticker', [OrdenTrabajoPdfController::class, 'previewSticker'])
    ->name('ordenes-trabajo.sticker')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/sticker/download', [OrdenTrabajoPdfController::class, 'downloadSticker'])
    ->name('ordenes-trabajo.sticker.download')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/sticker-termico', [OrdenTrabajoPdfController::class, 'previewStickerTermico'])
    ->name('ordenes-trabajo.sticker-termico')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/sticker-termico/download', [OrdenTrabajoPdfController::class, 'downloadStickerTermico'])
    ->name('ordenes-trabajo.sticker-termico.download')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/condiciones-garantia', [App\Http\Controllers\CondicionesGarantiaPdfController::class, 'previewFromOrden'])
    ->name('ordenes-trabajo.condiciones-garantia')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/condiciones-garantia/download', [App\Http\Controllers\CondicionesGarantiaPdfController::class, 'downloadFromOrden'])
    ->name('ordenes-trabajo.condiciones-garantia.download')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/recibo', [OrdenTrabajoPdfController::class, 'previewRecibo'])
    ->name('ordenes-trabajo.recibo')
    ->middleware(['auth', 'web']);

Route::get('/{orden}/recibo/download', [OrdenTrabajoPdfController::class, 'downloadRecibo'])
    ->name('ordenes-trabajo.recibo.download')
    ->middleware(['auth', 'web']);

Route::get('/{id}/editar', EditarOrden::class)
    ->name('ordenes-trabajo.editar')
    ->middleware(['auth', 'web']);
