# Alterações implementadas

Este documento registra decisões e alterações relevantes realizadas no VitaFlow.

## 16/06/2026 - Detalhe administrativo de campanha

- Criada a tela de detalhe administrativo de campanha.
- O titulo e a acao "Detalhes" na listagem de campanhas passaram a abrir a tela da campanha.
- O card publico da campanha passou a direcionar administradores para o detalhe administrativo da campanha.
- A tela exibe dados principais, local de coleta, tipos sanguineos alvo, periodo, horario, meta, vagas e criador.
- A tela exibe resumo dos agendamentos da campanha por status.
- A tela permite editar os dados da campanha sem voltar para a listagem.
- A tela permite excluir campanhas sem agendamentos vinculados.
- A tela incorpora o acompanhamento de agendamentos ja filtrado pela campanha atual.
- Criacoes, edicoes, exclusoes de campanha e alteracoes de comparecimento passaram a usar confirmacao com espera de 3 segundos antes de liberar a acao.

## 16/06/2026 - Filtros no estoque calculado

- O bloco "Estoque calculado" passou a exibir filtros por local e tipo sanguineo.
- Os filtros sao aplicados pelo componente Livewire sem recarregar a pagina.
- A paginacao do estoque e da lista de bolsas passou a ser reiniciada corretamente quando local ou tipo sanguineo mudam.

## 16/06/2026 - Registro administrativo de doacao

- Administradores passaram a registrar doacao a partir de agendamentos marcados como `realizado`.
- A acao fica disponivel somente quando o agendamento ainda nao possui doacao vinculada.
- A acao tambem exige que o horario do agendamento ja tenha chegado.
- Doacoes confirmadas exigem quantidade coletada em mililitros.
- Doacoes recusadas exigem motivo da recusa.
- O registro usa confirmacao com espera de 3 segundos antes de gravar.
- A gravacao bloqueia o agendamento durante a operacao para evitar duplicidade.
- Doacoes confirmadas continuam gerando bolsa de sangue automaticamente pelo modelo `Doacao`.

## 16/06/2026 - Registro administrativo de comparecimento

- Administradores passaram a registrar comparecimento na listagem administrativa de agendamentos.
- Agendamentos dentro da janela operacional podem ser marcados como `realizado`, `faltou` ou `cancelado`.
- Registros ja marcados podem ser corrigidos dentro da mesma janela operacional de 24 horas.
- A janela de registro abre no horario agendado e encerra 24 horas depois.
- Agendamentos futuros exibem o estado "Aguardando horario".
- Agendamentos que ultrapassaram a janela exibem o estado "Prazo encerrado".
- Agendamentos com doacao vinculada ou fora da janela operacional nao podem ser alterados pelo registro de comparecimento.
- A alteracao ocorre pelo componente Livewire, com confirmacao e feedback sem recarregar a pagina.
- O registro bloqueia o agendamento durante a operacao para evitar alteracoes simultaneas.

## 16/06/2026 - Acompanhamento administrativo de agendamentos

- Criada a tela administrativa `Agendamentos`.
- Administradores podem filtrar agendamentos por campanha, local de coleta, status e periodo.
- A listagem exibe data, horario, doador, campanha, local, status do agendamento e situacao da doacao vinculada.
- Adicionado resumo por status considerando os filtros aplicados.
- Adicionado atalho para agendamentos no painel administrativo.
- A tela passou a usar Livewire para aplicar e limpar filtros sem recarregar a pagina inteira.
- Adicionada opcao para escolher quantos agendamentos exibir por pagina.

## 16/06/2026 - Gerenciamento de agendamentos pelo doador

- Adicionada a tela "Meus agendamentos" na area do doador.
- O doador passou a consultar agendamentos ativos separados do historico.
- Criada tela de detalhe do agendamento para o doador.
- Agendamentos ativos e futuros podem ser cancelados pelo proprio doador.
- Agendamentos ativos e futuros podem ser reagendados pelo proprio doador.
- O reagendamento reaproveita as validacoes de periodo da campanha, janela de atendimento, intervalo de 30 minutos, vagas por horario e intervalo minimo entre doacoes.
- A verificacao de vagas e intervalo minimo ignora o proprio agendamento durante o reagendamento.

## 16/06/2026 - Campanhas demonstrativas com janela anual

- Campanhas ativas criadas pelo `DemoSeeder` passaram a iniciar na data atual.
- Campanhas ativas criadas pelo `DemoSeeder` passaram a ficar disponiveis por um ano.
- Os cenarios de campanhas encerradas e canceladas foram mantidos para preservar dados historicos da demonstracao.

## 16/06/2026 - Indicacao de campanha ja agendada

- Cards da home publica passaram a indicar quando o doador logado ja possui agendamento na campanha.
- A acao do card direciona o doador ja cadastrado para a tela de seus agendamentos.
- A verificacao usa uma consulta agregada no carregamento das campanhas para evitar consultas por card.

## 15/06/2026 - Intervalo minimo entre doacoes no agendamento

- Adicionado `sexo` aos dados de doador armazenados no `User`.
- A emissao e a atualizacao da carteirinha passaram a exigir sexo biologico.
- O agendamento passou a bloquear horarios dentro do intervalo minimo entre coletas.
- Para sexo masculino, o intervalo minimo aplicado e de 60 dias.
- Para sexo feminino, o intervalo minimo aplicado e de 90 dias.
- A validacao considera doacoes confirmadas e agendamentos ativos do proprio doador.
- Horarios bloqueados por intervalo aparecem como indisponiveis no calendario e tambem sao recusados no backend.

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
