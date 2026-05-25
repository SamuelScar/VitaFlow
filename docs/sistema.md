# Documentacao do sistema

Documentacao funcional e tecnica do VitaFlow/DoeVida.

O sistema segue como um monolito Laravel com paginas renderizadas no servidor. A documentacao fica separada por assunto para facilitar manutencao.

## Organizacao dos controllers

Os controllers sao organizados por fluxo ou caso de uso, nao obrigatoriamente por model.

Exemplos:

- `Auth\LoginController`: fluxo de login.
- `Auth\RegisterController`: fluxo de cadastro.
- `Admin\UserPromotionController`: fluxo administrativo de promocao de usuario.

Essa decisao evita controllers grandes demais e deixa cada entrada HTTP com uma responsabilidade clara.

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
- controller ou componente Livewire responsavel;
- view principal;
- regras de validacao;
- comportamento atual;
- status envolvidos.
