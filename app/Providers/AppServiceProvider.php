<?php

namespace App\Providers;

use App\Models\Empresa;
use App\Models\OrdenPago;
use App\Models\OrdenTrabajo;
use App\Observers\OrdenPagoObserver;
use App\Observers\OrdenTrabajoObserver;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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

        // Validación personalizada de RUT chileno
        Validator::extend('cl_rut', function ($attribute, $value, $parameters, $validator) {
            // Si el valor es null o vacío, la validación pasa (para campos nullable)
            if (empty($value)) {
                return true;
            }

            if (!is_string($value) && !is_numeric($value)) {
                return false;
            }

            $value = preg_replace('/[^0-9kK]/', '', $value);

            if (strlen($value) < 2) {
                return false;
            }

            $body = substr($value, 0, -1);
            $dv = strtoupper(substr($value, -1));

            if (!ctype_digit($body)) {
                return false;
            }

            $sum = 0;
            $multiple = 2;

            for ($i = strlen($body) - 1; $i >= 0; $i--) {
                $sum += $body[$i] * $multiple;
                $multiple = ($multiple == 7) ? 2 : $multiple + 1;
            }

            $expectedDv = 11 - ($sum % 11);

            $expectedDv = ($expectedDv == 11) ? '0' : (($expectedDv == 10) ? 'K' : (string)$expectedDv);

            return $dv == $expectedDv;
        });

        Validator::replacer('cl_rut', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'El campo :attribute no es un RUT chileno válido.');
        });
    }
}
