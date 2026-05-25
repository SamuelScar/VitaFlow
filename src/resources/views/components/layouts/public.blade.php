@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }} - {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-white border-bottom">
            <div class="container py-2">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('home') }}">
                    <span class="brand d-inline-flex align-items-center justify-content-center rounded text-white fw-bold">VF</span>
                    {{ config('app.name') }}
                </a>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Minha area</a>
                    @else
                        <a class="btn btn-outline-secondary" href="{{ route('login') }}">Entrar</a>
                        <a class="btn btn-primary" href="{{ route('register') }}">Criar conta</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
