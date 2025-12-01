<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout 
        :heading="__('Copia de Seguridad')" 
        :subheading="__('Genera y descarga copias de seguridad de tu base de datos')"
    >
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                {{ __('Exportar Base de Datos SQL') }}
            </h3>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-6">
                {{ __('Esta acción generará un archivo .sql completo con todos los datos actuales del sistema. Te recomendamos realizar esta acción periódicamente y guardar el archivo en un lugar seguro y externo al servidor.') }}
            </p>
            
            <div class="flex items-center gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button wire:click="descargarBackup" variant="primary" icon="arrow-down-tray" class="w-full">
                    {{ __('Generar y Descargar Backup') }}
                </flux:button>
            </div>
        </div>
    </x-settings.layout>
</section>

