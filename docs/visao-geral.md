# Visao geral

O VitaFlow/DoeVida e um sistema para apoiar fluxos relacionados a doacao de sangue, como campanhas, agendamentos, doadores e acompanhamento de status.

Por enquanto, o projeto esta na base inicial do monolito:

- Laravel com PostgreSQL.
- Views Blade.
- Livewire disponivel para telas interativas.
- Bootstrap para componentes visuais.
- Vite para assets em desenvolvimento.

## Arquitetura

Fluxo principal do monolito:

```text
rota web -> controller/livewire -> regra de negocio -> view blade
```
