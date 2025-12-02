@props([
    'heading',
    'subheading',
    'maxWidth' => 'max-w-lg',
])

<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Perfil') }}</flux:navlist.item>
            <flux:navlist.item :href="route('user-password.edit')" wire:navigate>{{ __('Contraseña') }}</flux:navlist.item>
            <flux:navlist.item :href="route('company.edit')" wire:navigate>{{ __('Configurar mi empresa') }}</flux:navlist.item>
            <flux:navlist.item :href="route('condiciones-garantia.edit')" wire:navigate>{{ __('Condiciones y garantía') }}</flux:navlist.item>
            <flux:navlist.item :href="route('terminos-recibo-ingreso.edit')" wire:navigate>{{ __('Términos Recibo Ingreso') }}</flux:navlist.item>
            <flux:navlist.item :href="route('accesorios.index')" wire:navigate>{{ __('Accesorios') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.descargar-mis-datos')" wire:navigate>{{ __('Descargar reportes') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.backup-database')" wire:navigate>{{ __('Copia de Seguridad') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full {{ $maxWidth }}">
            {{ $slot }}
        </div>
    </div>
</div>
