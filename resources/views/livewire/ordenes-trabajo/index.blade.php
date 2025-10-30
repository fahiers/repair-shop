<div class="max-w-screen-2xl mx-auto p-4 md:p-6 lg:p-8">
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
        <h1 class="text-xl md:text-2xl font-semibold">Listado de Órdenes de Trabajo</h1>
        
        <flux:button 
            icon="plus" 
            variant="primary" 
            :href="route('ordenes-trabajo.crear')" 
            wire:navigate
        >
            Nueva Orden
        </flux:button>
    </div>
</div>

