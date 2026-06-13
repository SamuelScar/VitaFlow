<x-mail::message>
# Convite administrativo

Voce foi convidado para criar uma conta de administrador no {{ config('app.name') }}.

<x-mail::button :url="$urlAceite">
Aceitar convite
</x-mail::button>

O convite expira em {{ $convite->expira_em->format('d/m/Y H:i') }}.

Se voce nao esperava este convite, ignore este e-mail.

{{ config('app.name') }}
</x-mail::message>
