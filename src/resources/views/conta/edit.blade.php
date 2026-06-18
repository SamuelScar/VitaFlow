<x-layouts.public title="Dados da conta">
    @php
        $usuario = auth()->user();
    @endphp

    <x-page-header
        label="Conta"
        title="Dados da conta"
        description="Atualize suas informacoes de acesso ao VitaFlow."
        icon="bi-person-gear"
        :back-href="route('dashboard')"
    />

    <section class="container py-5">
        <article class="card shadow-sm rounded-3">
            @include('conta.partials.update-form', ['usuario' => $usuario])
            @include('conta.partials.delete-account-panel')
        </article>
    </section>
</x-layouts.public>
