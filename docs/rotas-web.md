# Rotas web

Este documento registra as rotas web do sistema.

## Home publica

```text
GET /
```

Exibe a tela publica inicial com campanhas de doacao de sangue em destaque.

View:

```text
resources/views/home.blade.php
```

Comportamento atual:

- Visitantes podem acessar sem login.
- Exibe informacoes publicas de campanhas de sangue.
- Acoes de agendamento direcionam para login ou cadastro.

## Login

```text
GET /login
```

Exibe a tela de login.

Controller:

```text
App\Http\Controllers\Auth\LoginController@create
```

View:

```text
resources/views/auth/login.blade.php
```

## Autenticacao

```text
POST /login
```

Valida as credenciais do usuario e inicia a sessao.

Campos:

- `email`: obrigatorio e deve ser um e-mail valido.
- `password`: obrigatorio.

Comportamento atual:

- Se as credenciais forem invalidas, retorna erro no campo `email`.
- Se o login for valido, regenera a sessao.
- Apos autenticar, redireciona para `/dashboard`.

## Logout

```text
POST /logout
```

Encerra a sessao do usuario autenticado.

Controller:

```text
App\Http\Controllers\Auth\LoginController@destroy
```

Middlewares:

- `auth`

Comportamento atual:

- Remove o usuario autenticado da sessao.
- Invalida a sessao atual.
- Regenera o token CSRF.
- Redireciona para `/`.

## Dashboard

```text
GET /dashboard
```

Redireciona o usuario autenticado para a tela inicial correta conforme o tipo.

Controller:

```text
App\Http\Controllers\DashboardController
```

Middlewares:

- `auth`

Comportamento atual:

- Usuarios com tipo `admin` sao redirecionados para `/admin`.
- Usuarios com tipo `doador` sao redirecionados para `/usuario`.

## Area do doador

```text
GET /usuario
```

Exibe a tela inicial do usuario doador.

Middlewares:

- `auth`

View:

```text
resources/views/usuario/dashboard.blade.php
```

Comportamento atual:

- Exibe atalhos para campanhas abertas, historico de doacoes e carteirinha.
- A acao da carteirinha leva para a tela propria de emissao ou visualizacao.

## Tela da carteirinha de doador

```text
GET /usuario/carteirinha
```

Exibe a tela de carteirinha do doador autenticado.

Controller:

```text
App\Http\Controllers\Doador\CarteiraDoacaoController@create
```

Middlewares:

- `auth`

View:

```text
resources/views/usuario/carteirinha.blade.php
```

Comportamento atual:

- Apenas usuarios com tipo `doador` podem acessar.
- Se o doador ainda nao tiver carteirinha, exibe o formulario de emissao.
- Se o doador ja tiver carteirinha, exibe o resumo dos dados cadastrados.
- Se o doador ja tiver carteirinha, permite editar os dados na propria tela.

## Emitir carteirinha de doador

```text
POST /usuario/carteirinha
```

Cria a carteirinha de doador para o usuario autenticado.

Controller:

```text
App\Http\Controllers\Doador\CarteiraDoacaoController@store
```

Middlewares:

- `auth`

Campos:

- `cpf`: obrigatorio, deve ter 11 digitos e ser unico.
- `telefone`: obrigatorio, texto e maximo de 20 caracteres.
- `data_nascimento`: obrigatorio, data e nao pode ser futura.
- `tipo_sanguineo`: obrigatorio e deve ser um tipo sanguineo aceito pelo sistema.
- `peso`: obrigatorio, numerico e deve caber no formato do banco.
- `cidade`: obrigatorio, texto e maximo de 255 caracteres.

Comportamento atual:

- Apenas usuarios com tipo `doador` podem emitir carteirinha.
- Se o usuario autenticado for `admin`, retorna erro `403`.
- Se o usuario ja tiver carteirinha, retorna erro de validacao.
- O CPF e salvo apenas com digitos.
- Se os dados forem validos, cria uma carteira com status `ativa`.
- A data de emissao e preenchida automaticamente.

## Atualizar carteirinha de doador

```text
PUT /usuario/carteirinha
```

Atualiza os dados da carteirinha do doador autenticado.

Controller:

```text
App\Http\Controllers\Doador\CarteiraDoacaoController@update
```

Middlewares:

- `auth`

Campos:

- `cpf`: obrigatorio, deve ter 11 digitos e ser unico.
- `telefone`: obrigatorio, texto e maximo de 20 caracteres.
- `data_nascimento`: obrigatorio, data e nao pode ser futura.
- `tipo_sanguineo`: obrigatorio e deve ser um tipo sanguineo aceito pelo sistema.
- `peso`: obrigatorio, numerico e deve caber no formato do banco.
- `cidade`: obrigatorio, texto e maximo de 255 caracteres.

Comportamento atual:

- Apenas usuarios com tipo `doador` podem atualizar a propria carteirinha.
- Se o usuario autenticado for `admin`, retorna erro `403`.
- Se o doador ainda nao tiver carteirinha, retorna erro de validacao.
- O CPF e salvo apenas com digitos.
- A validacao de CPF unico ignora a propria carteirinha do doador.
- `status` e `emitida_em` nao sao alterados por esse fluxo.

## Painel admin

```text
GET /admin
```

Exibe a tela inicial administrativa.

Middlewares:

- `auth`
- `admin`

View:

```text
resources/views/admin/dashboard.blade.php
```

## Cadastro

```text
GET /cadastro
```

Exibe a tela de cadastro.

Controller:

```text
App\Http\Controllers\Auth\RegisterController@create
```

View:

```text
resources/views/auth/register.blade.php
```

## Criar usuario

```text
POST /cadastro
```

Valida os dados do usuario e cria a conta.

Campos:

- `name`: obrigatorio e deve ter no maximo 255 caracteres.
- `email`: obrigatorio, deve ser um e-mail valido, ter no maximo 255 caracteres e ser unico.
- `password`: obrigatorio, deve ter no minimo 8 caracteres e precisa ser confirmado.
- `password_confirmation`: obrigatorio para confirmar a senha.

Comportamento atual:

- Se os dados forem invalidos, retorna os erros nos campos do formulario.
- Se o cadastro for valido, cria o usuario.
- Apos criar o usuario, exibe mensagem de sucesso.
- Apos alguns segundos, redireciona para `/login`.

## Promover usuario para admin

```text
POST /usuarios/{user}/promover-admin
```

Promove um usuario existente para administrador.

Controller:

```text
App\Http\Controllers\Admin\UserPromotionController
```

Middlewares:

- `auth`
- `admin`

Comportamento atual:

- Apenas usuarios autenticados com tipo `admin` podem acessar.
- Se o usuario autenticado nao for admin, retorna erro `403`.
- Se o usuario informado existir, altera seu tipo para `admin`.
- Apos promover, retorna para a pagina anterior com mensagem de sucesso.

## Health check

```text
GET /health
```

Retorna um JSON simples indicando que a aplicacao esta respondendo.

Resposta atual:

```json
{
  "status": "ok"
}
```
