# Visao geral

O VitaFlow e um sistema web para organizar campanhas de doacao de sangue, aproximando administradores, doadores e visitantes em um unico fluxo.

No estado atual, administradores gerenciam locais de coleta e campanhas. Doadores podem criar conta, emitir e editar a carteirinha e consultar a home publica de campanhas. Visitantes podem visualizar informacoes publicas e acessar login ou cadastro.

Agendamentos, doacoes, estoque de sangue e historico do doador ja existem na modelagem inicial, mas ainda nao possuem fluxo web completo de cadastro, acompanhamento ou relatorio.

Estado atual do monolito:

- Laravel com PostgreSQL.
- Views Blade renderizadas no servidor.
- Autenticacao web por sessao.
- Perfis `admin` e `doador` no model `User`.
- Home publica com campanhas estaticas de destaque, login, cadastro, logout e dashboards por perfil.
- Modelagem inicial do dominio de doacao de sangue.
- Bootstrap e Bootstrap Icons para componentes visuais.
- Tema claro, escuro e automatico conforme o sistema.
- Feedback visual com SweetAlert2.
- Validacao client-side generica para formularios.
- Consulta de CEP no cadastro e edicao de locais de coleta.
- Vite para assets em desenvolvimento.

Funcionalidades ja cobertas:

- Cadastro, login, logout e redirecionamento por perfil.
- Edicao de dados da conta e exclusao da propria conta com confirmacao por senha.
- Emissao e edicao da carteirinha de doador.
- Cadastro, edicao e exclusao de locais de coleta.
- Cadastro, edicao e exclusao de campanhas.
- Campanhas com multiplos tipos sanguineos alvo.

## Arquitetura

Fluxo principal do monolito:

```text
rota web -> controller -> regra de negocio -> view blade
```
