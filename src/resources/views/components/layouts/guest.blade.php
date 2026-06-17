@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }} - {{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        @livewireScriptConfig
        <script>
            (() => {
                const storageKey = 'vitaflow-theme';
                const allowedThemes = ['light', 'dark', 'system'];
                let storedTheme = null;

                try {
                    storedTheme = localStorage.getItem(storageKey);
                } catch {
                    storedTheme = null;
                }

                const preference = allowedThemes.includes(storedTheme) ? storedTheme : 'system';
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                const resolvedTheme = preference === 'system' ? systemTheme : preference;

                document.documentElement.dataset.themePreference = preference;
                document.documentElement.dataset.bsTheme = resolvedTheme;
            })();
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @php
            $alertError = collect($errors->getBags())
                ->flatMap(fn ($bag) => $bag->all())
                ->first();
        @endphp

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
        @elseif ($alertError)
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.alertError({
                        text: @json($alertError),
                    });
                });
            </script>
        @endif

        <main class="min-vh-100 d-flex align-items-center justify-content-center p-3">
            {{ $slot }}
        </main>
    </body>
</html>
