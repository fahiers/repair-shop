<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('ordenes-trabajo')
                ->middleware(['auth', 'web'])
                ->group(base_path('routes/ordenes-trabajo.php'));

            Route::prefix('clientes')
                ->middleware(['auth', 'web'])
                ->group(base_path('routes/clientes.php'));

            Route::prefix('dispositivos')
                ->middleware(['auth', 'web'])
                ->group(base_path('routes/dispositivos.php'));

            Route::prefix('modelos')
                ->middleware(['auth', 'web'])
                ->group(base_path('routes/modelos.php'));

            Route::prefix('inventario')
                ->middleware(['auth', 'web'])
                ->group(base_path('routes/inventario.php'));

            Route::prefix('facturacion')
                ->middleware(['auth', 'web'])
                ->group(base_path('routes/facturacion.php'));

            Route::prefix('servicios')
                ->middleware(['auth', 'web'])
                ->group(base_path('routes/servicios.php'));

            Route::prefix('productos')
                ->middleware(['auth', 'web'])
                ->group(base_path('routes/productos.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
