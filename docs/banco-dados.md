# Banco de dados

Este documento registra as entidades principais, relacionamentos e decisoes de modelagem do banco.

## Estado atual

O projeto usa PostgreSQL.

Configuracao do ambiente local:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=vitaflow
DB_USERNAME=vitaflow
DB_PASSWORD=vitaflow
```

## Entidades atuais

### `users`

Representa usuarios do sistema.

Campos principais:

- `name`
- `email`
- `password`
- `tipo`
- `cpf`
- `telefone`
- `data_nascimento`
- `sexo`
- `tipo_sanguineo`
- `peso`
- `cidade`

Tipos atuais:

- `admin`
- `doador`

Relacionamentos:

- Um usuario pode ter uma carteira de doacao.
- Um usuario pode ter muitos agendamentos.
- Um usuario admin pode criar muitas campanhas.
- Um usuario admin pode enviar muitos convites administrativos.

Observacoes:

- Os perfis `admin` e `doador` sao exclusivos.
- No fluxo da aplicacao, administradores sao criados diretamente pelo aceite de convite e nao possuem dados ou direitos de doador.
- O campo `sexo` e usado para calcular o intervalo minimo entre doacoes no agendamento.
- O `AdminUserSeeder` cria somente o administrador inicial necessario para acessar o sistema.

### `password_reset_tokens`

Representa tokens temporarios usados na recuperacao de senha.

Campos principais:

- `email`
- `token`
- `created_at`

Observacoes:

- O token e gerenciado pelo broker de senhas do Laravel.
- A expiracao atual e de 60 minutos.

### `sessions`

Armazena as sessoes autenticadas porque o projeto utiliza `SESSION_DRIVER=database`.

Observacoes:

- As tabelas padrao de cache e filas nao sao criadas.
- O cache utiliza arquivos locais.
- Filas, quando utilizadas, executam de forma sincrona.

### `convites_admin`

Representa convites enviados para criacao de contas administrativas.

Campos principais:

- `email`
- `token_hash`
- `convidado_por_id`
- `expira_em`
- `aceito_em`
- `cancelado_em`

Relacionamentos:

- Pertence ao administrador que enviou o convite.

Observacoes:

- O token e armazenado somente como hash.
- O convite expira em 48 horas e pode ser aceito apenas uma vez.
- O aceite cria diretamente um usuario com tipo `admin`.

### `carteiras_doacao`

Representa a credencial emitida para autorizar o doador a realizar agendamentos.

Campos principais:

- `user_id`
- `status`
- `emitida_em`

Status atuais:

- `ativa`
- `bloqueada`
- `inativa`

Relacionamentos:

- Pertence a um usuario.
- Cada usuario pode ter apenas uma carteira.

### `locais_coleta`

Representa os locais onde campanhas e coletas acontecem.

Campos principais:

- `nome`
- `cep`
- `logradouro`
- `numero`
- `bairro`
- `cidade`
- `uf`
- `complemento`
- `capacidade_diaria`

Relacionamentos:

- Pode ter muitas campanhas.
- Pode ter muitas configuracoes de estoque minimo.
- Pode armazenar muitas bolsas de sangue.

Observacoes:

- `endereco` foi substituido por campos estruturados.
- O model `LocalColeta` expoe `endereco_completo` para exibicao formatada.
- O formulario administrativo pode preencher logradouro, bairro, cidade e UF a partir do CEP.

### `campanhas`

Representa campanhas de doacao de sangue.

Campos principais:

- `criada_por_id`
- `local_coleta_id`
- `titulo`
- `descricao`
- `tipos_sanguineos_alvo`
- `meta_bolsas`
- `agendamentos_por_horario`
- `horario_inicio`
- `horario_fim`
- `data_inicio`
- `data_fim`
- `status`

Status atuais:

- `ativa`
- `encerrada`
- `cancelada`

Relacionamentos:

- Pertence a um usuario criador.
- Pertence a um local de coleta.
- Pode ter muitos agendamentos.
- Define quantos agendamentos podem ocupar o mesmo horario de 30 minutos.
- Define a janela diaria de atendimento usada para gerar horarios disponiveis.

Observacoes:

- `tipos_sanguineos_alvo` e um campo JSON.
- Valor `null` em `tipos_sanguineos_alvo` significa que a campanha aceita todos os tipos sanguineos.

### `estoques_sangue`

Representa a configuracao de estoque minimo por local de coleta e tipo sanguineo.

Campos principais:

- `local_coleta_id`
- `tipo_sanguineo`
- `estoque_minimo_ml`

Relacionamentos:

- Pertence a um local de coleta.
- Cada local possui apenas um registro por tipo sanguineo.
- O saldo atual nao e armazenado nesta tabela; ele e calculado a partir das bolsas disponiveis.

### `agendamentos`

Representa a participacao agendada de um doador em uma campanha.

Campos principais:

- `user_id`
- `campanha_id`
- `data_hora`
- `status`

Status atuais:

- `agendado`
- `cancelado`
- `realizado`
- `faltou`

Relacionamentos:

- Pertence a um usuario.
- Pertence a uma campanha.
- Pode ter uma doacao registrada.
- O mesmo usuario nao pode repetir agendamento na mesma campanha.
- A criacao de um agendamento exige que o usuario possua uma carteira ativa.
- A criacao de um agendamento respeita o intervalo minimo entre doacoes: 60 dias para sexo masculino e 90 dias para sexo feminino.
- A regra considera doacoes confirmadas e agendamentos ativos do usuario.

### `doacoes`

Representa o resultado de uma coleta vinculada a um agendamento.

Campos principais:

- `agendamento_id`
- `data_coleta`
- `quantidade_ml`
- `status`
- `motivo_recusa`

Status atuais:

- `confirmada`
- `recusada`

Relacionamentos:

- Pertence a um agendamento.
- Cada agendamento pode ter apenas uma doacao registrada.
- Uma doacao confirmada gera uma bolsa de sangue.
- Uma doacao recusada nao gera bolsa.

### `bolsas_sangue`

Representa o sangue armazenado e seu ciclo de vida apos uma doacao confirmada.

Campos principais:

- `doacao_id`
- `local_coleta_id`
- `tipo_sanguineo`
- `quantidade_ml`
- `data_coleta`
- `validade_em`
- `status`

Status atuais:

- `disponivel`
- `utilizada`
- `vencida`
- `descartada`
- `transferida`

Relacionamentos:

- Pertence a uma doacao confirmada.
- Pertence ao local de coleta onde esta armazenada atualmente.

Observacoes:

- Cada doacao confirmada pode gerar apenas uma bolsa.
- O vencimento e determinado automaticamente pela data de validade.
- Bolsas transferidas continuam disponiveis no local de destino.
- O estoque soma somente bolsas disponiveis ou transferidas que ainda nao venceram.

### `relatorio_exports`

Representa o histórico de relatórios gerados pelo administrador.

Campos principais:

- `user_id`
- `arquivo_caminho`
- `arquivo_nome`
- `status`
- `tamanho_bytes`
- `is_arquivado`
- `deleted_at`

Status atuais:

- `processando`
- `concluido`
- `falha`
- `arquivando`
- `desarquivando`

Relacionamentos:

- Pertence a um usuário administrador (`user_id`).

Observacoes:

- Relatórios são gerados dinamicamente e armazenados fisicamente.
- Relatórios arquivados sofrem compressão `.zip` no disco.
- Utiliza Soft Deletes (`deleted_at`) para manter histórico de relatórios excluídos da fila principal.

### Filas em Segundo Plano (`jobs`, `failed_jobs`, `job_batches`)

Tabelas nativas do Laravel para execução assíncrona.

Observacoes:

- Utilizadas para as tarefas de arquivamento (`ArquivarRelatorioPdf`) e desarquivamento (`DesarquivarRelatorioPdf`).
- O processamento de compressão/extração de arquivos ocorre através de *workers*.

## Tipos sanguineos

Os tipos sanguineos aceitos ficam centralizados em `App\Support\TipoSanguineo`:

- `A+`
- `A-`
- `B+`
- `B-`
- `AB+`
- `AB-`
- `O+`
- `O-`

## Seeds

O sistema cria um usuario administrador padrao para permitir o primeiro acesso administrativo.

Credenciais do administrador padrao:

```txt
E-mail: admin@vitaflow.local
Senha: Admin@123
```

Usuarios criados pelo cadastro comum entram como `doador`. Novos administradores sao criados exclusivamente pelo fluxo de convite administrativo.

### Dados demonstrativos

O `DemoSeeder` cria um conjunto grande e previsivel de dados para apresentacoes do sistema:

- 150 usuarios doadores com carteirinhas;
- 30 locais de coleta;
- 240 configuracoes de estoque minimo;
- 60 campanhas ativas, futuras, encerradas e canceladas;
- mais de 1.600 agendamentos, cerca de 200 doacoes e bolsas em diferentes etapas do ciclo de vida.

As datas sao calculadas a partir do dia em que o seeder e executado. Assim, as campanhas abertas continuam adequadas para demonstracoes realizadas nos dias seguintes.

Execute dentro do container da aplicacao:

```powershell
docker compose exec app php artisan db:seed --class=DemoSeeder
```

O seeder usa registros identificaveis e pode ser executado novamente sem duplicar os dados demonstrativos. Todos os doadores criados usam a senha `Doador@123`. Para acessar um deles:

```txt
E-mail: doador001@vitaflow.local
Senha: Doador@123
```
