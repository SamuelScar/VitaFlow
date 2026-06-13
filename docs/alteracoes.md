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

## 13/06/2026 - Limpeza da infraestrutura padrão do Laravel

- Removidas as migrations das tabelas `cache`, `cache_locks`, `jobs`, `job_batches` e `failed_jobs`, que não são utilizadas pelo projeto.
- Mantidas as tabelas `sessions` e `password_reset_tokens`, necessárias para autenticação e recuperação de senha.
- Alterado o cache padrão para arquivos locais e a fila padrão para execução síncrona.
- Removido o worker de filas do script de desenvolvimento.
- Removidas variáveis de cache e filas dos arquivos de ambiente e configurações SMTP sem uso do `.env` local.
- Bancos existentes devem ser recriados para remover fisicamente as tabelas antigas; nenhuma operação destrutiva foi executada automaticamente.

## 13/06/2026 - Convites para administradores

- Removido o fluxo que promovia doadores existentes para administradores.
- Criada a entidade `ConviteAdmin` com token armazenado em hash, validade, aceite e cancelamento.
- Adicionados envio, reenvio e cancelamento de convites na tela administrativa de usuarios.
- Adicionado o aceite publico que cria diretamente uma conta `admin` com e-mail verificado.
- Administradores e doadores passaram a ser tratados como perfis exclusivos.
- A listagem de usuarios ganhou filtro por perfil e deixou de exibir a acao de promocao.
- E-mails passaram a ser normalizados no cadastro e na atualizacao da conta para preservar a unicidade entre perfis.

## 13/06/2026 - Gerenciamento administrativo de carteirinhas

- Adicionado o status da carteirinha dos doadores na listagem administrativa de usuarios.
- Administradores podem ativar ou inativar carteirinhas ja emitidas.
- Doadores sem carteirinha emitida e usuarios administradores nao possuem acao de alteracao de status.
- A alteracao ocorre pelo componente Livewire sem recarregar a pagina e usa SweetAlert para confirmacao e feedback.

## 13/06/2026 - Padronizacao de alertas e confirmacoes

- Substituidas confirmacoes nativas de exclusao e cancelamento por SweetAlert.
- Erros de operacao e validacao retornados pelo backend passaram a usar SweetAlert nos layouts publico e de autenticacao.
- Mantidas mensagens inline somente nos campos invalidos para orientar a correcao.

## 13/06/2026 - Bolsas de sangue e estoque calculado

- Criada a entidade `BolsaSangue` para representar o sangue armazenado apos uma doacao confirmada.
- Doacoes confirmadas geram automaticamente uma bolsa vinculada ao local da campanha e ao tipo sanguineo do doador.
- A validade da bolsa foi definida em 42 dias e o vencimento passou a ser calculado automaticamente.
- Adicionados os fluxos administrativos de consulta, utilizacao, descarte e transferencia de bolsas.
- Bolsas transferidas continuam disponiveis e passam a compor o estoque do local de destino.
- Removidos os saldos editaveis de `estoques_sangue`; a tabela permanece apenas com a configuracao de estoque minimo.
- A configuracao de estoque minimo pode ser atualizada pelo administrador na tela de bolsas e estoque.
- O estoque passou a ser calculado a partir das bolsas disponiveis ou transferidas e dentro da validade.
- Atualizados o `DemoSeeder`, os relacionamentos, o painel administrativo e a documentacao tecnica.
