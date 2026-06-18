<x-layouts.public title="Agendamentos">
    <x-page-header
        label="Administracao"
        title="Agendamentos"
        description="Acompanhe agendamentos por campanha, local, data e status."
        icon="bi-shield-check"
        :back-href="route('admin.dashboard')"
    />

    <section class="container py-5">
        <livewire:admin.agendamento-list />
    </section>
</x-layouts.public>
