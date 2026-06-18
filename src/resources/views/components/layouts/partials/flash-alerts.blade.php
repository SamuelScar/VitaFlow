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
