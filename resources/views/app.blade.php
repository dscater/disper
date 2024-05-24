<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        .v-container.login {
            /* background-image: url('{{ asset('imgs/fondo_login.webp') }}') !important; */
            /* background-size: cover; */
            background-color: rgb(0, 143, 187);
            padding: 0px;
        }
    </style>
    <script>
        const url_assets = "{{ asset('') }}";
        var main_url = "{{ url('') }}";
        var mapa_id = "MAP_ID";
    </script>
    @php
        $api = App\Models\ApiMap::first();
    @endphp
    @if ($api)
        <script>
            mapa_id = "{{ $api->map_id }}";
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $api->api_key }}"></script>
    @else
        <script src="https://maps.googleapis.com/maps/api/js?key=INSERT_YOUR_API_KEY"></script>
    @endif

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
