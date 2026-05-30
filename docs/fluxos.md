# Fluxos

Este documento registra os fluxos existentes no sistema.

## Login

1. Usuario acessa `GET /login`.
2. Sistema exibe o formulario de login.
3. Usuario informa e-mail e senha.
4. Sistema valida as credenciais em `POST /login`.
5. Se as credenciais forem validas, a sessao e regenerada.
6. Usuario e redirecionado para `/dashboard`.

## Dashboard

1. Usuario autenticado acessa `GET /dashboard`.
2. Sistema verifica o tipo do usuario logado.
3. Se o usuario for `admin`, redireciona para `/admin`.
4. Se o usuario for `doador`, redireciona para `/usuario`.

## Logout

1. Usuario autenticado envia `POST /logout`.
2. Sistema encerra a autenticacao.
3. Sessao atual e invalidada.
4. Token CSRF e regenerado.
5. Usuario e redirecionado para `/`.

## Cadastro

1. Usuario acessa `GET /cadastro`.
2. Sistema exibe o formulario de cadastro.
3. Usuario informa nome, e-mail, senha e confirmacao da senha.
4. Sistema valida os dados em `POST /cadastro`.
5. Se os dados forem validos, o usuario e criado.
6. Sistema exibe mensagem de sucesso.
7. Usuario e redirecionado para `/login` apos alguns segundos.

## Atualizacao dos dados da conta

1. Usuario autenticado acessa `GET /conta`.
2. Sistema exibe a tela de dados da conta.
3. Usuario envia `PUT /conta`.
4. Sistema valida nome, e-mail e senha opcional.
5. Sistema garante que o e-mail informado nao pertence a outro usuario.
6. Se uma nova senha for enviada, sistema valida a confirmacao da senha.
7. Sistema atualiza apenas os dados da propria conta.
8. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Exclusao da conta

1. Usuario autenticado acessa `GET /conta`.
2. Sistema exibe a area de exclusao da conta.
3. Usuario informa a senha atual e envia `DELETE /conta`.
4. Sistema valida a senha atual do usuario.
5. Se a senha for valida, sistema encerra a autenticacao.
6. Sistema exclui a conta do usuario.
7. Sessao atual e invalidada.
8. Token CSRF e regenerado.
9. Usuario e redirecionado para `/` com mensagem de sucesso.

## Emissao da carteirinha de doador

1. Doador autenticado acessa `GET /usuario`.
2. Sistema exibe o atalho da carteirinha no dashboard do doador.
3. Doador acessa `GET /usuario/carteirinha`.
4. Se ainda nao tiver carteirinha, sistema exibe o formulario de emissao.
5. Doador envia `POST /usuario/carteirinha`.
6. Sistema valida se o usuario logado tem tipo `doador`.
7. Sistema verifica se o doador ainda nao possui carteirinha.
8. Sistema valida os dados informados.
9. Se os dados forem validos, cria a carteirinha com status `ativa`.
10. Sistema registra a data de emissao automaticamente.
11. Sistema retorna para a tela da carteirinha com mensagem de sucesso.

## Atualizacao da carteirinha de doador

1. Doador autenticado acessa `GET /usuario/carteirinha`.
2. Sistema exibe a carteirinha ja emitida.
3. Doador aciona a opcao de editar dados na propria tela.
4. Sistema libera os campos da propria carteirinha para edicao.
5. Doador envia `PUT /usuario/carteirinha`.
6. Sistema valida se o usuario logado tem tipo `doador`.
7. Sistema valida os dados informados.
8. Se os dados forem validos, atualiza a carteirinha.
9. Sistema mantem o status e a data de emissao originais.
10. Sistema retorna para a tela da carteirinha com mensagem de sucesso.

## Promocao de usuario para admin

1. Admin autenticado envia `POST /usuarios/{user}/promover-admin`.
2. Sistema valida o acesso pelo middleware `admin`.
3. Usuario informado tem seu tipo alterado para `admin`.
4. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Health check

1. Usuario ou servico acessa `GET /health`.
2. Sistema retorna um JSON com `status` igual a `ok`.
