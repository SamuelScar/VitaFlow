<x-layouts.public title="Bolsas de sangue">
    <x-page-header
        label="Administracao"
        title="Bolsas e estoque de sangue"
        description="Consulte o estoque calculado e registre o ciclo de vida das bolsas."
        icon="bi-droplet-half"
        :back-href="route('admin.dashboard')"
    />

    <section class="container py-5">
        <livewire:admin.bolsa-sangue-list />
    </section>
</x-layouts.public>
