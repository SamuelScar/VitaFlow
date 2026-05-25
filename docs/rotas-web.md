# Rotas web

Este documento registra as rotas web do sistema.

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
- Apos autenticar, redireciona para `/health`.

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
