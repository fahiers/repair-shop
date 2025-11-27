@if ($empresa?->logo_url)
    <div class="flex size-10 items-center justify-center rounded-md bg-white dark:bg-gray-800 p-1 border border-gray-200 dark:border-gray-700 shadow-sm">
        <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre ?? 'Logo' }}" class="max-h-full max-w-full object-contain" />
    </div>
@else
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
        <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
    </div>
@endif
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold">{{ $empresa?->nombre ?? 'Taller Técnico' }}</span>
</div>
