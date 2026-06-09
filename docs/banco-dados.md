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

### `password_reset_tokens`

Representa tokens temporarios usados na recuperacao de senha.

Campos principais:

- `email`
- `token`
- `created_at`

Observacoes:

- O token e gerenciado pelo broker de senhas do Laravel.
- A expiracao atual e de 60 minutos.

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

Status atuais:

- `ativa`
- `bloqueada`
- `inativa`

Relacionamentos:

- Pertence a um usuario.
- Pode ter muitos agendamentos.
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
- Pode ter muitos registros de estoque de sangue.

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

Observacoes:

- `tipos_sanguineos_alvo` e um campo JSON.
- Valor `null` em `tipos_sanguineos_alvo` significa que a campanha aceita todos os tipos sanguineos.

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

### Dados demonstrativos

O `DemoSeeder` cria um conjunto grande e previsivel de dados para apresentacoes do sistema:

- 150 usuarios doadores com carteirinhas;
- 30 locais de coleta;
- 240 registros de estoque;
- 60 campanhas ativas, futuras, encerradas e canceladas;
- mais de 1.600 agendamentos e cerca de 200 doacoes em diferentes status.

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
