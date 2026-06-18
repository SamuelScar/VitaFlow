@props([
    'title'       => config('app.name'),
    'description' => 'VitaFlow — Organize campanhas de doacao de sangue, acompanhe locais de coleta e conecte doadores a causas que precisam de apoio.',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light" data-theme-preference="system">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $description }}">
        <title>{{ $title }} - {{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        @livewireScriptConfig
        @include('components.layouts.partials.theme-script')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @include('components.layouts.partials.flash-alerts')

        <nav class="navbar navbar-expand-lg bg-body border-bottom">
            <div class="container py-2">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('home') }}">
                    <img class="brand-logo" src="{{ asset('assets/images/logo-vitaflow-drop.png') }}" alt="" aria-hidden="true">
                    <span>{{ config('app.name') }}</span>
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

                    <div class="dropdown">
                        <button
                            class="btn btn-link theme-toggle-button d-inline-flex align-items-center justify-content-center"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            aria-label="Selecionar tema"
                            title="Selecionar tema"
                            data-theme-toggle
                        >
                            <i class="bi bi-circle-half" aria-hidden="true" data-theme-toggle-icon></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button class="dropdown-item d-flex align-items-center gap-2" type="button" data-theme-value="system">
                                    <i class="bi bi-circle-half" aria-hidden="true"></i>
                                    Sistema
                                    <i class="bi bi-check-lg ms-auto d-none" aria-hidden="true" data-theme-check></i>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item d-flex align-items-center gap-2" type="button" data-theme-value="light">
                                    <i class="bi bi-sun" aria-hidden="true"></i>
                                    Claro
                                    <i class="bi bi-check-lg ms-auto d-none" aria-hidden="true" data-theme-check></i>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item d-flex align-items-center gap-2" type="button" data-theme-value="dark">
                                    <i class="bi bi-moon-stars" aria-hidden="true"></i>
                                    Escuro
                                    <i class="bi bi-check-lg ms-auto d-none" aria-hidden="true" data-theme-check></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        @auth
            @if (!auth()->user()->hasVerifiedEmail())
                <div class="alert alert-warning rounded-0 border-start-0 border-end-0 mb-0 py-2 text-center" role="alert">
                    Seu e-mail ainda não foi verificado. Algumas funcionalidades podem estar indisponíveis.
                    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline text-warning-emphasis fw-semibold">
                            Clique aqui para reenviar o link.
                        </button>
                    </form>
                </div>
            @endif
        @endauth

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
