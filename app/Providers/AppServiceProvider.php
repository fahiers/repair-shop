<?php

namespace App\Providers;

use App\Models\Empresa;
use App\Models\OrdenPago;
use App\Models\OrdenTrabajo;
use App\Observers\OrdenPagoObserver;
use App\Observers\OrdenTrabajoObserver;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        OrdenTrabajo::observe(OrdenTrabajoObserver::class);
        OrdenPago::observe(OrdenPagoObserver::class);

        // Configurar moneda chilena (CLP) y locale español de Chile
        Number::useCurrency('CLP');
        Number::useLocale('es_CL');

        // Compartir datos de la empresa con todas las vistas
        view()->composer('*', function ($view) {
            $view->with('empresa', Empresa::first());
        });
    }
}
