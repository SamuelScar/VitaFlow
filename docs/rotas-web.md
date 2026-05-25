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
