# Visao geral

O VitaFlow e um sistema web para organizar campanhas de doacao de sangue, aproximando administradores, doadores e visitantes em um unico fluxo.

O sistema permite que administradores gerenciem campanhas, locais de coleta, horarios, vagas, participacao de doadores, doacoes e relatorios. Doadores podem consultar campanhas, criar conta, agendar participacoes e acompanhar seu historico. Visitantes podem visualizar informacoes publicas e precisam se cadastrar para agendar.

Estado atual do monolito:

- Laravel com PostgreSQL.
- Views Blade renderizadas no servidor.
- Autenticacao web por sessao.
- Perfis `admin` e `doador` no model `User`.
- Home publica, login, cadastro, logout e dashboards por perfil.
- Modelagem inicial do dominio de doacao de sangue.
- Bootstrap para componentes visuais.
- Vite para assets em desenvolvimento.

## Arquitetura

Fluxo principal do monolito:

```text
rota web -> controller -> regra de negocio -> view blade
```
