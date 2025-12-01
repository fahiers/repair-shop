<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout 
        :heading="__('Descarga de Reportes')" 
        :subheading="__('Descarga reportes completos del sistema con filtros de fecha opcionales')" 
    >
        <div class="mt-6 w-full space-y-6">
            <!-- Reporte Financiero -->
            <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    {{ __('Reporte Financiero y de Caja') }}
                </h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-6">
                    {{ __('Descarga un reporte completo de todos los pagos registrados en el sistema') }}
                </p>
                <form wire:submit="descargarReporteFinanciero" class="space-y-6">
                    <div class="space-y-4">
                        <flux:input
                            wire:model="fechaDesde"
                            :label="__('Fecha desde')"
                            type="date"
                            hint="Opcional: Selecciona la fecha inicial del reporte"
                        />

                        <flux:input
                            wire:model="fechaHasta"
                            :label="__('Fecha hasta')"
                            type="date"
                            hint="Opcional: Selecciona la fecha final del reporte"
                        />
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button variant="primary" type="submit" class="w-full">
                            {{ __('Descargar Reporte Excel') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Reporte de Rotación -->
            <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    {{ __('Reporte de Rotación de Repuestos y Servicios') }}
                </h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-6">
                    {{ __('Para saber qué es lo que más se vende o qué repuestos se consumen más. Ayuda a decidir qué stock comprar y qué servicios son los más rentables.') }}
                </p>
                <form wire:submit="descargarReporteRotacion" class="space-y-6">
                    <div class="space-y-4">
                        <flux:input
                            wire:model="fechaDesdeRotacion"
                            :label="__('Fecha desde')"
                            type="date"
                            hint="Opcional: Selecciona la fecha inicial del reporte"
                        />

                        <flux:input
                            wire:model="fechaHastaRotacion"
                            :label="__('Fecha hasta')"
                            type="date"
                            hint="Opcional: Selecciona la fecha final del reporte"
                        />
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button variant="primary" type="submit" class="w-full">
                            {{ __('Descargar Reporte Excel') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </x-settings.layout>
</section>
