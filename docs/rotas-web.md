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
- `remember`: opcional, mantem o usuario conectado.

Comportamento atual:

- Se as credenciais forem invalidas, retorna erro no campo `email`.
- Se o login for valido, regenera a sessao.
- Apos autenticar, redireciona para `/health`.

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
