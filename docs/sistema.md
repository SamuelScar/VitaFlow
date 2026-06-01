# Documentacao do sistema

Documentacao funcional e tecnica do VitaFlow.

O sistema segue como um monolito Laravel com paginas renderizadas no servidor. A documentacao fica separada por assunto para facilitar manutencao.

## Organizacao dos controllers

Os controllers sao organizados por fluxo ou caso de uso, nao obrigatoriamente por model.

Exemplos:

- `Auth\LoginController`: fluxo de login.
- `Auth\RegisterController`: fluxo de cadastro.
- `ContaController`: fluxo de edicao e exclusao da propria conta.
- `Admin\UserPromotionController`: fluxo administrativo de promocao de usuario.
- `Admin\LocalColetaController`: fluxo administrativo de locais de coleta.
- `Admin\CampanhaController`: fluxo administrativo de campanhas.

Essa decisao evita controllers grandes demais e deixa cada entrada HTTP com uma responsabilidade clara.

## Frontend

O frontend usa Blade, Bootstrap, Bootstrap Icons, SweetAlert2, Alpine.js, Alpine Mask e JavaScript iniciado em `resources/js/app.js`.

Comportamentos globais importantes:

- `data-validate-form`: ativa validacao client-side, alerta lateral e rolagem ate o primeiro campo invalido.
- `data-cep-lookup`: ativa consulta de CEP para preencher campos de endereco.
- `data-theme-value`: controla a selecao de tema `system`, `light` ou `dark`.
- `x-mask`: aplica mascaras simples nos campos de CPF, telefone e CEP.
- Alertas de sucesso, erro e aviso devem usar os helpers globais do SweetAlert2.

## Indice

- [Visao geral](visao-geral.md)
- [Rotas web](rotas-web.md)
- [Fluxos](fluxos.md)
- [Regras de negocio](regras-negocio.md)
- [Banco de dados](banco-dados.md)
- [Identidade visual](identidade-visual.md)

## Manutencao

Ao criar ou alterar uma tela importante, atualizar os arquivos relacionados com o que existe no sistema:

- rota web;
- controller responsavel;
- view principal;
- regras de validacao;
- comportamento atual;
- status envolvidos.
