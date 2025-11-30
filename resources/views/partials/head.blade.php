<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? (($empresa && $empresa->nombre && trim($empresa->nombre) !== '') ? $empresa->nombre : 'Taller Tecnico') }}</title>

@if($empresa?->logo_url)
    <link rel="icon" href="{{ $empresa->logo_url }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ $empresa->logo_url }}">
@else
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
@endif

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<script>
    // Establecer modo claro como predeterminado si no hay preferencia guardada
    if (!window.localStorage.getItem('flux.appearance')) {
        window.localStorage.setItem('flux.appearance', 'light');
    }
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
