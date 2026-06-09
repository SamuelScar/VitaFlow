# Visao geral

O VitaFlow e um sistema web para organizar campanhas de doacao de sangue, aproximando administradores, doadores e visitantes em um unico fluxo.

No estado atual, administradores gerenciam locais de coleta, campanhas e usuarios. Doadores podem criar conta, emitir e editar a carteirinha e consultar a home publica de campanhas. Visitantes podem visualizar informacoes publicas e acessar login, cadastro ou recuperacao de senha.

Estado atual do monolito:

- Laravel com PostgreSQL.
- Views Blade renderizadas no servidor.
- Autenticacao web por sessao.
- Perfis `admin` e `doador` no model `User`.
- Home publica com campanhas ativas cadastradas no banco, login, cadastro, recuperacao de senha, logout e dashboards por perfil.
- Modelagem inicial do dominio de doacao de sangue.
- Bootstrap e Bootstrap Icons para componentes visuais.
- Tema claro, escuro e automatico conforme o sistema.
- Feedback visual com SweetAlert2.
- Validacao client-side generica para formularios.
- Consulta de CEP no cadastro e edicao de locais de coleta.
- Vite para assets em desenvolvimento.

Funcionalidades ja cobertas:

- Cadastro, login, recuperacao de senha, logout e redirecionamento por perfil.
- Edicao de dados da conta e exclusao da propria conta com confirmacao por senha.
- Emissao e edicao da carteirinha de doador.
- Cadastro, edicao e exclusao de locais de coleta.
- Cadastro, edicao e exclusao de campanhas.
- Listagem de usuarios e promocao de doadores para administradores.
- Campanhas com multiplos tipos sanguineos alvo.
- Exibicao publica de campanhas ativas dentro do periodo vigente.

## Arquitetura

Fluxo principal do monolito:

```text
rota web -> controller -> regra de negocio -> view blade
```

## Organização de Controllers

Os controllers são organizados por fluxo ou caso de uso, não obrigatoriamente por model. Exemplos:

- `Auth\LoginController`: fluxo de login.
- `Auth\RegisterController`: fluxo de cadastro.
- `ContaController`: fluxo de edição e exclusão da própria conta.
- `Admin\UserController`: listagem administrativa de usuários.
- `Admin\UserPromotionController`: fluxo administrativo de promoção de usuário.
- `Admin\LocalColetaController`: fluxo administrativo de locais de coleta.
- `Admin\CampanhaController`: fluxo administrativo de campanhas.

Essa decisão evita controllers grandes demais e deixa cada entrada HTTP com uma responsabilidade clara.

## Frontend

O frontend usa Blade, Bootstrap, Bootstrap Icons, SweetAlert2, Alpine.js, Alpine Mask e JavaScript iniciado em `resources/js/app.js`.

Comportamentos globais importantes:

- `data-validate-form`: ativa validação client-side, alerta lateral e rolagem até o primeiro campo inválido.
- `data-cep-lookup`: ativa consulta de CEP para preencher campos de endereço.
- `data-theme-value`: controla a seleção de tema `system`, `light` ou `dark`.
- `x-mask`: aplica máscaras simples nos campos de CPF, telefone e CEP.
- Alertas de sucesso, erro e aviso devem usar os helpers globais do SweetAlert2.
- Livewire é usado em interfaces administrativas que precisam de interação dinâmica sem recarregar a página.
- Livewire e Alpine são iniciados juntos em `resources/js/app.js` a partir do bundle ESM do Livewire.
- A paginação padrão do Laravel usa a renderização Bootstrap 5 configurada no `AppServiceProvider`.
