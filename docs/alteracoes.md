# Alterações implementadas

Este documento registra decisões e alterações relevantes realizadas no VitaFlow.

## 13/06/2026 - Responsabilidades de usuário, carteirinha e agendamento

### Decisão

- Dados pessoais e de doador pertencem ao `User`, mesmo quando são informados durante a emissão da carteirinha.
- A `CarteiraDoacao` representa somente a credencial emitida para o usuário, com status e data de emissão.
- O `Agendamento` pertence diretamente ao usuário.
- A existência de uma carteirinha ativa continua obrigatória para realizar agendamentos.

### Alterações realizadas

- Movidos `cpf`, `telefone`, `data_nascimento`, `tipo_sanguineo`, `peso` e `cidade` de `carteiras_doacao` para `users`.
- Mantidos em `carteiras_doacao` apenas `user_id`, `status`, `emitida_em` e timestamps.
- Substituído `carteira_doacao_id` por `user_id` em `agendamentos`.
- Alterada a restrição de agendamento único por campanha para usar `user_id` e `campanha_id`.
- Adicionado `User::podeAgendarDoacao()` para validar que o usuário é doador e possui carteirinha ativa.
- Mantido o preenchimento e a edição dos dados pela tela da carteirinha, mas agora os dados são salvos no usuário.
- Atualizados os dados demonstrativos para refletir os novos relacionamentos.

### Migração de dados

A migration `2026_06_13_000001_move_donor_data_to_users.php` transfere os dados existentes das carteirinhas para os usuários e preserva os agendamentos existentes ao trocar o vínculo para `user_id`.

### Regra para o futuro fluxo de agendamento

Antes de criar um agendamento, o sistema deve chamar `User::podeAgendarDoacao()`. Um usuário sem carteirinha ou com carteirinha `bloqueada` ou `inativa` não pode realizar novos agendamentos.

## 13/06/2026 - Navegação nas telas de autenticação

- Adicionados botões `Voltar` e `Home` nas telas de login, cadastro e recuperação de senha.
- O botão `Voltar` direciona para a página anterior e usa a home quando não existe uma origem diferente da tela atual.
- O botão `Home` direciona diretamente para a página inicial pública.
- A navegação compartilhada foi centralizada em `auth/partials/navigation.blade.php`.
