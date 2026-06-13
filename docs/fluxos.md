# Fluxos

Este documento registra os fluxos existentes no sistema.

## Home publica

1. Visitante ou usuario autenticado acessa `GET /`.
2. Sistema consulta campanhas com status `ativa` dentro do periodo vigente.
3. Sistema exibe o resumo publico com total de campanhas abertas, meta de bolsas e locais participantes.
4. Sistema lista as campanhas encontradas com local de coleta, tipos sanguineos alvo, meta e data final.
5. Se nao houver campanha aberta, sistema exibe uma mensagem informativa.
6. Visitante pode acessar o login pela acao de participacao.
7. Usuario autenticado pode acessar o dashboard pela acao de participacao.

## Login

1. Usuario acessa `GET /login`.
2. Sistema exibe o formulario de login.
3. Usuario pode retornar a pagina anterior ou acessar a home publica.
4. Usuario informa e-mail e senha.
5. Sistema valida as credenciais em `POST /login`.
6. Se as credenciais forem validas, a sessao e regenerada.
7. Usuario e redirecionado para `/dashboard`.

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
3. Usuario pode retornar a pagina anterior ou acessar a home publica.
4. Usuario informa nome, e-mail, senha e confirmacao da senha.
5. Sistema valida os dados em `POST /cadastro`.
6. Se os dados forem validos, o usuario e criado.
7. Sistema exibe mensagem de sucesso.
8. Usuario e redirecionado para `/login` apos alguns segundos.

## Recuperacao de senha

1. Visitante acessa `GET /esqueci-senha`.
2. Sistema exibe o formulario de solicitacao do link.
3. Visitante pode retornar a pagina anterior ou acessar a home publica.
4. Visitante informa o e-mail cadastrado.
5. Sistema valida o e-mail em `POST /esqueci-senha`.
6. Se o e-mail existir e nao estiver limitado por throttle, sistema envia o link pelo mailer configurado.
7. Visitante abre o link recebido por e-mail.
8. Sistema exibe `GET /redefinir-senha/{token}` com token e e-mail preenchidos.
9. Visitante informa e confirma a nova senha.
10. Sistema valida os dados em `POST /redefinir-senha`.
11. Sistema bloqueia a redefinicao se a nova senha for igual a senha atual.
12. Se o token for valido, sistema atualiza a senha e renova o token de "lembrar-me".
13. Sistema redireciona para `/login` com mensagem de sucesso.

## Atualizacao dos dados da conta

1. Usuario autenticado acessa `GET /conta`.
2. Sistema exibe a tela de dados da conta.
3. Usuario envia `PUT /conta`.
4. Sistema valida nome, e-mail e senha opcional.
5. Sistema garante que o e-mail informado nao pertence a outro usuario.
6. Se uma nova senha for enviada, sistema valida a senha atual.
7. Se uma nova senha for enviada, sistema valida a confirmacao da senha.
8. Se uma nova senha for enviada, sistema bloqueia o uso da mesma senha atual.
9. Sistema atualiza apenas os dados da propria conta.
10. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Exclusao da conta

1. Usuario autenticado acessa `GET /conta`.
2. Sistema exibe a acao de exclusao como uma zona de risco discreta.
3. Usuario aciona a exclusao.
4. Sistema abre um alerta central de confirmacao e bloqueia a continuidade por alguns segundos.
5. Usuario confirma que deseja continuar.
6. Sistema solicita a senha atual dentro do alerta.
7. Usuario informa a senha e o formulario oculto envia `DELETE /conta`.
8. Sistema valida a senha atual do usuario.
9. Se a senha for valida, sistema encerra a autenticacao.
10. Sistema exclui a conta do usuario.
11. Sessao atual e invalidada.
12. Token CSRF e regenerado.
13. Usuario e redirecionado para `/` com mensagem de sucesso.

## Emissao da carteirinha de doador

1. Doador autenticado acessa `GET /usuario`.
2. Sistema exibe o atalho da carteirinha no dashboard do doador.
3. Doador acessa `GET /usuario/carteirinha`.
4. Se ainda nao tiver carteirinha, sistema exibe o formulario de emissao.
5. Doador envia `POST /usuario/carteirinha`.
6. Sistema valida se o usuario logado tem tipo `doador`.
7. Sistema verifica se o doador ainda nao possui carteirinha.
8. Sistema valida os dados informados.
9. Se os dados forem validos, atualiza os dados pessoais e de doador no usuario.
10. Sistema cria a carteirinha com status `ativa`.
11. Sistema registra a data de emissao automaticamente.
12. Sistema retorna para a tela da carteirinha com mensagem de sucesso.

## Atualizacao da carteirinha de doador

1. Doador autenticado acessa `GET /usuario/carteirinha`.
2. Sistema exibe a carteirinha ja emitida.
3. Doador aciona a opcao de editar dados na propria tela.
4. Sistema libera os campos da propria carteirinha para edicao.
5. Doador envia `PUT /usuario/carteirinha`.
6. Sistema valida se o usuario logado tem tipo `doador`.
7. Sistema valida os dados informados.
8. Se os dados forem validos, atualiza os dados do usuario.
9. Sistema mantem o status e a data de emissao originais.
10. Sistema retorna para a tela da carteirinha com mensagem de sucesso.

## Cadastro de local de coleta

1. Admin autenticado acessa `GET /admin/locais-coleta`.
2. Sistema exibe a tela de locais de coleta.
3. Admin informa o CEP.
4. Sistema tenta buscar o endereco pelo CEP e preencher logradouro, bairro, cidade e UF.
5. Admin completa numero, complemento quando houver, nome e capacidade diaria.
6. Admin envia `POST /admin/locais-coleta`.
7. Sistema valida nome, CEP, logradouro, numero, bairro, cidade, UF, complemento e capacidade diaria.
8. Se os dados forem validos, sistema cria o local de coleta.
9. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Atualizacao de local de coleta

1. Admin autenticado acessa `GET /admin/locais-coleta`.
2. Sistema exibe a lista de locais cadastrados.
3. Admin aciona a opcao de editar um local.
4. Admin pode alterar o CEP para preencher novamente parte do endereco.
5. Admin envia `PUT /admin/locais-coleta/{localColeta}`.
6. Sistema valida nome, CEP, logradouro, numero, bairro, cidade, UF, complemento e capacidade diaria.
7. Se os dados forem validos, sistema atualiza o local de coleta informado.
8. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Exclusao de local de coleta

1. Admin autenticado acessa `GET /admin/locais-coleta`.
2. Sistema exibe a lista de locais cadastrados.
3. Admin envia `DELETE /admin/locais-coleta/{localColeta}`.
4. Sistema verifica se o local possui campanhas ou estoque vinculado.
5. Se houver vinculo, sistema bloqueia a exclusao e retorna erro de validacao.
6. Se nao houver vinculo, sistema exclui o local de coleta.
7. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Cadastro de campanha

1. Admin autenticado acessa `GET /admin/campanhas`.
2. Sistema exibe a tela de campanhas.
3. Admin abre o formulario de nova campanha.
4. Admin informa local de coleta, titulo, descricao, tipos sanguineos alvo, meta e datas.
5. Admin envia `POST /admin/campanhas`.
6. Sistema valida os dados informados.
7. Se nenhum tipo sanguineo alvo for marcado, sistema considera a campanha para todos os tipos.
8. Sistema cria a campanha com status `ativa` e registra o admin como criador.
9. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Atualizacao de campanha

1. Admin autenticado acessa `GET /admin/campanhas`.
2. Sistema exibe a lista de campanhas cadastradas.
3. Admin aciona a opcao de editar uma campanha.
4. Admin altera dados, tipos sanguineos alvo e status.
5. Admin envia `PUT /admin/campanhas/{campanha}`.
6. Sistema valida os dados informados.
7. Se os dados forem validos, sistema atualiza a campanha.
8. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Exclusao de campanha

1. Admin autenticado acessa `GET /admin/campanhas`.
2. Sistema exibe a lista de campanhas cadastradas.
3. Admin envia `DELETE /admin/campanhas/{campanha}`.
4. Sistema verifica se a campanha possui agendamentos vinculados.
5. Se houver vinculo, sistema bloqueia a exclusao e retorna erro de validacao.
6. Se nao houver vinculo, sistema exclui a campanha.
7. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Alteracao de tema

1. Usuario acessa qualquer tela com o layout principal.
2. Sistema aplica a preferencia salva em `localStorage`.
3. Se nao houver preferencia salva, sistema usa a opcao `Sistema`.
4. Na opcao `Sistema`, o tema segue a configuracao do navegador ou sistema operacional.
5. Usuario pode abrir o seletor discreto na navbar e escolher `Sistema`, `Claro` ou `Escuro`.
6. Sistema salva a preferencia e atualiza o tema da pagina.

## Promocao de usuario para admin

1. Admin autenticado acessa `GET /admin/usuarios`.
2. Sistema lista usuarios cadastrados com nome, e-mail e tipo atual.
3. Admin pode buscar usuarios por nome ou e-mail sem recarregar a pagina.
4. Sistema sincroniza a busca com o parametro `busca` da URL.
5. Sistema mantem a busca aplicada ao navegar entre paginas da listagem.
6. Admin aciona a promocao de um usuario doador.
7. Sistema envia `POST /admin/usuarios/{user}/promover-admin`.
8. Sistema valida o acesso pelo middleware `admin`.
9. Usuario informado tem seu tipo alterado para `admin`.
10. Sistema retorna para a pagina anterior com mensagem de sucesso.

## Health check

1. Usuario ou servico acessa `GET /health`.
2. Sistema retorna um JSON com `status` igual a `ok`.
