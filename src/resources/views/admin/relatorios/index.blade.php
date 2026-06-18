<x-layouts.public title="Relatórios Dinâmicos">
    <x-page-header
        label="Administracao"
        title="Relatorios dinamicos"
        description="Construa relatorios personalizados selecionando os modulos, colunas e filtros desejados."
        icon="bi-file-earmark-bar-graph"
        :back-href="route('admin.dashboard')"
    />

    <section class="container py-5">
        <livewire:admin.relatorio-builder />
    </section>
</x-layouts.public>
