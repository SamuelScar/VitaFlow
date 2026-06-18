@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }} - {{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        @livewireScriptConfig
        @include('components.layouts.partials.theme-script')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @include('components.layouts.partials.flash-alerts')

        <main class="min-vh-100 d-flex align-items-center justify-content-center p-3">
            {{ $slot }}
        </main>
    </body>
</html>
