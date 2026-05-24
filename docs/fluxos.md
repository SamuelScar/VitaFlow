# Fluxos

Este documento registra os fluxos existentes no sistema.

## Login

1. Usuario acessa `GET /login`.
2. Sistema exibe o formulario de login.
3. Usuario informa e-mail e senha.
4. Sistema valida as credenciais em `POST /login`.
5. Se as credenciais forem validas, a sessao e regenerada.
6. Usuario e redirecionado para `/health`.

## Cadastro

1. Usuario acessa `GET /cadastro`.
2. Sistema exibe o formulario de cadastro.
3. Usuario informa nome, e-mail, senha e confirmacao da senha.
4. Sistema valida os dados em `POST /cadastro`.
5. Se os dados forem validos, o usuario e criado.
6. Sistema exibe mensagem de sucesso.
7. Usuario e redirecionado para `/login` apos alguns segundos.

## Health check

1. Usuario ou servico acessa `GET /health`.
2. Sistema retorna um JSON com `status` igual a `ok`.
