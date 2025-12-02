<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('finanzas', 'finanzas.index')->name('finanzas.index');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/company', 'settings.company')->name('company.edit');
    Route::get('settings/condiciones-garantia/preview', [App\Http\Controllers\CondicionesGarantiaPdfController::class, 'preview'])
        ->name('condiciones-garantia.preview');

    Volt::route('settings/condiciones-garantia', 'settings.condiciones-garantia')->name('condiciones-garantia.edit');
    Volt::route('settings/terminos-recibo-ingreso', 'settings.terminos-recibo-ingreso')->name('terminos-recibo-ingreso.edit');
    Volt::route('settings/accesorios', 'settings.accesorios')->name('accesorios.index');
    Volt::route('settings/descargar-mis-datos', 'settings.descargar-mis-datos')->name('settings.descargar-mis-datos');
    Route::get('settings/backup-database', \App\Livewire\Settings\BackupDatabase::class)->name('settings.backup-database');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
