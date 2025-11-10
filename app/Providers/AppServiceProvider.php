<?php

namespace App\Providers;

use App\Models\FacturaPago;
use App\Models\OrdenTrabajo;
use App\Observers\FacturaPagoObserver;
use App\Observers\OrdenTrabajoObserver;
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
        FacturaPago::observe(FacturaPagoObserver::class);
    }
}
