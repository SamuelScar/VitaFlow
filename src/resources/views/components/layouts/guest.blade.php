@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }} - {{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
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
        @elseif ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.alertError();
                });
            </script>
        @endif

        <main class="min-vh-100 d-flex align-items-center justify-content-center p-3">
            {{ $slot }}
        </main>
    </body>
</html>
