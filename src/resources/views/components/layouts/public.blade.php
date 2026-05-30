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
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.alertSuccess({
                        text: @json(session('success')),
                        redirectUrl: @json(session('alert_redirect')),
                        timer: @json(session('alert_timer', 3000)),
                    });
                });
            </script>
        @endif

        <nav class="navbar navbar-expand-lg bg-white border-bottom">
            <div class="container py-2">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('home') }}">
                    <span class="brand d-inline-flex align-items-center justify-content-center rounded bg-primary text-white fw-bold">VF</span>
                    {{ config('app.name') }}
                </a>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        <div class="dropdown">
                            <button
                                class="btn btn-outline-secondary dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <i class="bi bi-person-circle me-1" aria-hidden="true"></i>
                                {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-grid me-2" aria-hidden="true"></i>
                                        Minha area
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('conta.edit') }}">
                                        <i class="bi bi-person-gear me-2" aria-hidden="true"></i>
                                        Dados da conta
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>
                                            Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                            Entrar
                        </a>
                        <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="{{ route('register') }}">
                            <i class="bi bi-person-plus" aria-hidden="true"></i>
                            Criar conta
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
