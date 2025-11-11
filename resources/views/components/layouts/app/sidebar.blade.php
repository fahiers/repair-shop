<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
                <x-app-logo />
            </a>
            <flux:sidebar.collapse tooltip="Alternar barra lateral" class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:sidebar.item>
            <flux:sidebar.item icon="wrench" :href="route('ordenes-trabajo.index')" :current="request()->routeIs('ordenes-trabajo.*')" wire:navigate>{{ __('Orden de Servicio') }}</flux:sidebar.item>
            <flux:sidebar.item icon="users" :href="route('clientes.index')" :current="request()->routeIs('clientes.*')" wire:navigate>{{ __('Clientes') }}</flux:sidebar.item>
            <flux:sidebar.item icon="device-tablet" href="#" :current="request()->is('dispositivos*')" wire:navigate>{{ __('Dispositivos') }}</flux:sidebar.item>
            <flux:sidebar.item icon="cpu-chip" :href="route('modelos.index')" :current="request()->routeIs('modelos.*')" wire:navigate>{{ __('Modelos de Dispositivos') }}</flux:sidebar.item>
            <flux:sidebar.item icon="archive-box" href="#" :current="request()->is('inventario*')" wire:navigate>{{ __('Inventario') }}</flux:sidebar.item>
            <flux:sidebar.item icon="document-text" href="#" :current="request()->is('facturacion*')" wire:navigate>{{ __('Facturación') }}</flux:sidebar.item>
            <flux:sidebar.item icon="wrench-screwdriver" :href="route('servicios.index')" :current="request()->routeIs('servicios.*')" wire:navigate>{{ __('Servicios') }}</flux:sidebar.item>
            <flux:sidebar.item icon="shopping-bag" :href="route('productos.index')" :current="request()->routeIs('productos.*')" wire:navigate>{{ __('Productos') }}</flux:sidebar.item>
            <flux:sidebar.item icon="cog-6-tooth" :href="route('company.edit')" :current="request()->routeIs('company.*')" wire:navigate>{{ __('Configurar mi empresa') }}</flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:sidebar.profile :name="auth()->user()->name" data-test="sidebar-menu-button" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                >
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Configuración') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                        {{ __('Cerrar Sesión') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Configuración') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Cerrar Sesión') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
