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

Tipos atuais:

- `admin`
- `doador`

Relacionamentos:

- Um usuario pode ter uma carteira de doacao.
- Um usuario admin pode criar muitas campanhas.

### `carteiras_doacao`

Representa os dados complementares do doador.

Campos principais:

- `user_id`
- `cpf`
- `telefone`
- `data_nascimento`
- `tipo_sanguineo`
- `peso`
- `cidade`
- `status`
- `emitida_em`

Relacionamentos:

- Pertence a um usuario.
- Pode ter muitos agendamentos.
- Cada usuario pode ter apenas uma carteira.

### `locais_coleta`

Representa os locais onde campanhas e coletas acontecem.

Campos principais:

- `nome`
- `endereco`
- `cidade`
- `capacidade_diaria`

Relacionamentos:

- Pode ter muitas campanhas.
- Pode ter muitos registros de estoque de sangue.

### `campanhas`

Representa campanhas de doacao de sangue.

Campos principais:

- `criada_por_id`
- `local_coleta_id`
- `titulo`
- `descricao`
- `tipo_sanguineo_alvo`
- `meta_bolsas`
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

### `estoques_sangue`

Representa o estoque de sangue por local de coleta e tipo sanguineo.

Campos principais:

- `local_coleta_id`
- `tipo_sanguineo`
- `quantidade_ml`
- `bolsas_disponiveis`
- `estoque_minimo_ml`

Relacionamentos:

- Pertence a um local de coleta.
- Cada local possui apenas um registro por tipo sanguineo.

### `agendamentos`

Representa a participacao agendada de um doador em uma campanha.

Campos principais:

- `carteira_doacao_id`
- `campanha_id`
- `data_hora`
- `status`

Status atuais:

- `agendado`
- `cancelado`
- `realizado`
- `faltou`

Relacionamentos:

- Pertence a uma carteira de doacao.
- Pertence a uma campanha.
- Pode ter uma doacao registrada.
- A mesma carteira nao pode repetir agendamento na mesma campanha.

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

## Tipos sanguineos

Os tipos sanguineos aceitos ficam centralizados em `App\Support\TiposSanguineos`:

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

Usuarios criados pelo cadastro comum entram como `doador`. Para se tornar `admin`, o usuario deve ser promovido posteriormente por outro administrador.
