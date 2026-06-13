# Rotas web

Este documento registra as rotas web do sistema.

## Sumário

### Autenticação
- [Home pública](#home-pública)
- [Login](#login)
- [Autenticação](#autenticação)
- [Recuperação de senha](#solicitar-recuperação-de-senha)
- [Cadastro](#cadastro)

### Conta do usuário
- [Dados da conta](#tela-de-dados-da-conta)
- [Atualizar conta](#atualizar-dados-da-conta)
- [Excluir conta](#excluir-conta)
- [Logout](#logout)

### Doador
- [Dashboard do doador](#área-do-doador)
- [Carteirinha](#tela-da-carteirinha-de-doador)
- [Emitir carteirinha](#emitir-carteirinha-de-doador)
- [Atualizar carteirinha](#atualizar-carteirinha-de-doador)

### Admin
- [Dashboard admin](#painel-admin)
- [Locais de coleta](#tela-de-locais-de-coleta)
- [Campanhas](#tela-de-campanhas)
- [Usuários](#tela-de-usuarios)

### Sistema
- [Health check](#health-check)

---

## Home pública

`GET /`

Exibe a tela publica inicial com campanhas de doacao de sangue em destaque.

Consulta:

```text
App\Models\Campanha
```

Controller:

```text
App\Http\Controllers\HomeController
```

View:

```text
resources/views/home.blade.php
```

Comportamento atual:

- Visitantes podem acessar sem login.
- Exibe campanhas cadastradas com status `ativa`.
- Exibe apenas campanhas dentro do periodo vigente, com `data_inicio` menor ou igual ao dia atual e `data_fim` maior ou igual ao dia atual.
- Ordena as campanhas pela data final e depois pelo titulo.
- Exibe resumo publico com total de campanhas abertas, meta total de bolsas e locais participantes.
- Se nao houver campanha aberta, exibe uma mensagem informativa.
- A acao de participacao direciona visitantes para login e usuarios autenticados para o dashboard.

## Login

`GET /login`

Exibe a tela de login.

Controller:

```text
App\Http\Controllers\Auth\LoginController@create
```

View:

```text
resources/views/auth/login.blade.php
```

## Autenticação

`POST /login`

Valida as credenciais do usuario e inicia a sessao.

Campos:

- `email`: obrigatorio e deve ser um e-mail valido.
- `password`: obrigatorio.

Comportamento atual:

- Se as credenciais forem invalidas, retorna erro no campo `email`.
- Se o login for valido, regenera a sessao.
- Apos autenticar, redireciona para `/dashboard`.

## Solicitar recuperação de senha

`GET /esqueci-senha`

Exibe a tela para solicitar o link de redefinicao de senha.

Controller:

```text
App\Http\Controllers\Auth\PasswordResetLinkController@create
```

Middlewares:

- `guest`

View:

```text
resources/views/auth/forgot-password.blade.php
```

## Enviar link de recuperação de senha

`POST /esqueci-senha`

Valida o e-mail informado e solicita ao broker do Laravel o envio do link de redefinicao.

Controller:

```text
App\Http\Controllers\Auth\PasswordResetLinkController@store
```

Middlewares:

- `guest`

Campos:

- `email`: obrigatorio e deve ser um e-mail valido.

Comportamento atual:

- Usa o mailer configurado no Laravel para enviar o link.
- Se o envio for aceito pelo broker, retorna para a tela anterior com mensagem de sucesso.
- Se o e-mail nao existir ou estiver limitado por throttle, retorna erro de validacao.

## Tela de redefinição de senha

`GET /redefinir-senha/{token}`

Exibe a tela para criar uma nova senha a partir do token recebido por e-mail.

Controller:

```text
App\Http\Controllers\Auth\NewPasswordController@create
```

Middlewares:

- `guest`

View:

```text
resources/views/auth/reset-password.blade.php
```

## Redefinir senha

`POST /redefinir-senha`

Valida o token e atualiza a senha do usuario.

Controller:

```text
App\Http\Controllers\Auth\NewPasswordController@store
```

Middlewares:

- `guest`

Campos:

- `token`: obrigatorio.
- `email`: obrigatorio e deve ser um e-mail valido.
- `password`: obrigatorio, minimo de 8 caracteres e precisa ser confirmado.
- `password_confirmation`: obrigatorio para confirmar a senha.

Comportamento atual:

- Usa o broker de senhas configurado em `config/auth.php`.
- Bloqueia a redefinicao se a nova senha for igual a senha atual.
- Se o token for valido, atualiza a senha e renova o token de "lembrar-me".
- Apos redefinir, redireciona para `/login` com mensagem de sucesso.
- Se o token ou e-mail forem invalidos, retorna erro de validacao.

## Logout

`POST /logout`

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

## Tela de dados da conta

`GET /conta`

Exibe a tela para editar dados da conta e solicitar exclusao da propria conta.

Controller:

```text
App\Http\Controllers\ContaController@edit
```

Middlewares:

- `auth`

View:

```text
resources/views/conta/edit.blade.php
```

Comportamento atual:

- Exibe formulario para atualizar nome, e-mail e senha opcional.
- Exibe a exclusao da conta como uma zona de risco discreta.
- A exclusao da conta usa um fluxo central de confirmacao por alerta.
- A navegacao para essa tela fica no dropdown do usuario autenticado.

## Atualizar dados da conta

`PUT /conta`

Atualiza os dados da conta do usuario autenticado.

Controller:

```text
App\Http\Controllers\ContaController@update
```

Middlewares:

- `auth`

Campos:

- `name`: obrigatorio, texto e maximo de 255 caracteres.
- `email`: obrigatorio, e-mail valido, maximo de 255 caracteres e unico.
- `current_password`: obrigatorio quando `password` for informado e deve ser a senha atual.
- `password`: opcional, minimo de 8 caracteres e precisa ser confirmado quando informado.
- `password_confirmation`: obrigatorio quando `password` for informado.

Comportamento atual:

- Atualiza apenas a conta do usuario autenticado.
- A validacao de e-mail unico ignora o proprio usuario.
- Se a senha nao for informada, a senha atual e mantida.
- Se uma nova senha for informada, a senha atual precisa conferir.
- Se uma nova senha for informada, ela precisa ser diferente da senha atual.
- A atualizacao da conta nao permite alterar o tipo do usuario.
- Apos atualizar, retorna para a pagina anterior com mensagem de sucesso.

## Excluir conta

`DELETE /conta`

Exclui a conta do usuario autenticado.

Controller:

```text
App\Http\Controllers\ContaController@destroy
```

Middlewares:

- `auth`

Campos:

- `password`: obrigatorio e deve ser a senha atual do usuario.

Comportamento atual:

- A tela solicita confirmacao em alerta central antes de enviar a exclusao.
- A senha atual e solicitada dentro do fluxo de confirmacao.
- Exclui apenas a conta do usuario autenticado.
- Se a senha atual estiver incorreta, retorna erro de validacao.
- Apos excluir, encerra a autenticacao.
- Invalida a sessao atual e regenera o token CSRF.
- Redireciona para `/` com mensagem de sucesso.

## Dashboard

`GET /dashboard`

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

## Área do doador

`GET /usuario`

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
- O atalho de campanhas abertas leva para a home publica.
- O historico de doacoes aparece como card informativo, sem rota propria.

## Tela da carteirinha de doador

`GET /usuario/carteirinha`

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
- Se o doador ja tiver carteirinha, combina os dados do usuario com o status e a data de emissao da carteirinha.
- Se o doador ja tiver carteirinha, permite editar os dados do usuario na propria tela.

## Emitir carteirinha de doador

`POST /usuario/carteirinha`

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
- Se os dados forem validos, salva os dados pessoais e de doador no usuario.
- A carteira criada armazena somente o usuario, o status `ativa` e a data de emissao.
- A data de emissao e preenchida automaticamente.

## Atualizar carteirinha de doador

`PUT /usuario/carteirinha`

Atualiza os dados pessoais e de doador do usuario autenticado pela tela da carteirinha.

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
- A validacao de CPF unico ignora o proprio usuario.
- `status` e `emitida_em` nao sao alterados por esse fluxo.

## Painel admin

`GET /admin`

Exibe a tela inicial administrativa.

Middlewares:

- `auth`
- `admin`

View:

```text
resources/views/admin/dashboard.blade.php
```

Comportamento atual:

- Exibe atalhos para locais de coleta, campanhas de sangue, usuarios e home publica.
- Exibe card informativo de agendamentos.
- O card de agendamentos ainda nao possui rota propria de gestao.

## Tela de locais de coleta

`GET /admin/locais-coleta`

Exibe a tela administrativa de locais de coleta.

Controller:

```text
App\Http\Controllers\Admin\LocalColetaController@index
```

Middlewares:

- `auth`
- `admin`

View:

```text
resources/views/admin/locais-coleta/index.blade.php
```

Comportamento atual:

- Lista os locais de coleta cadastrados.
- Exibe o endereco formatado a partir de CEP, logradouro, numero, bairro, cidade e UF.
- Exibe formulario para cadastrar novo local.
- Permite abrir o formulario de edicao de cada local.
- Permite solicitar exclusao de locais sem campanhas ou estoque vinculado.

## Cadastrar local de coleta

`POST /admin/locais-coleta`

Cadastra um local de coleta.

Controller:

```text
App\Http\Controllers\Admin\LocalColetaController@store
```

Middlewares:

- `auth`
- `admin`

Campos:

- `nome`: obrigatorio, texto e maximo de 255 caracteres.
- `cep`: obrigatorio e deve seguir o formato `00000-000`.
- `logradouro`: obrigatorio, texto e maximo de 255 caracteres.
- `numero`: obrigatorio, texto e maximo de 30 caracteres.
- `bairro`: obrigatorio, texto e maximo de 255 caracteres.
- `cidade`: obrigatorio, texto e maximo de 255 caracteres.
- `uf`: obrigatorio, texto e tamanho de 2 caracteres.
- `complemento`: opcional, texto e maximo de 255 caracteres.
- `capacidade_diaria`: obrigatorio, inteiro, minimo de 1 e maximo de 10000.

Comportamento atual:

- Apenas usuarios com tipo `admin` podem cadastrar locais de coleta.
- O CEP pode preencher logradouro, bairro, cidade e UF no formulario.
- A consulta de CEP usa ViaCEP e fallback na BrasilAPI pelo JavaScript.
- O backend normaliza CEP e UF antes de validar.
- Se os dados forem validos, cria o local de coleta.
- Apos cadastrar, retorna para a pagina anterior com mensagem de sucesso.

## Atualizar local de coleta

`PUT /admin/locais-coleta/{localColeta}`

Atualiza os dados de um local de coleta.

Controller:

```text
App\Http\Controllers\Admin\LocalColetaController@update
```

Middlewares:

- `auth`
- `admin`

Campos:

- `nome`: obrigatorio, texto e maximo de 255 caracteres.
- `cep`: obrigatorio e deve seguir o formato `00000-000`.
- `logradouro`: obrigatorio, texto e maximo de 255 caracteres.
- `numero`: obrigatorio, texto e maximo de 30 caracteres.
- `bairro`: obrigatorio, texto e maximo de 255 caracteres.
- `cidade`: obrigatorio, texto e maximo de 255 caracteres.
- `uf`: obrigatorio, texto e tamanho de 2 caracteres.
- `complemento`: opcional, texto e maximo de 255 caracteres.
- `capacidade_diaria`: obrigatorio, inteiro, minimo de 1 e maximo de 10000.

Comportamento atual:

- Apenas usuarios com tipo `admin` podem atualizar locais de coleta.
- O CEP pode preencher logradouro, bairro, cidade e UF no formulario.
- A consulta de CEP usa ViaCEP e fallback na BrasilAPI pelo JavaScript.
- O backend normaliza CEP e UF antes de validar.
- Se o local existir e os dados forem validos, atualiza o registro.
- Apos atualizar, retorna para a pagina anterior com mensagem de sucesso.

## Excluir local de coleta

`DELETE /admin/locais-coleta/{localColeta}`

Exclui um local de coleta.

Controller:

```text
App\Http\Controllers\Admin\LocalColetaController@destroy
```

Middlewares:

- `auth`
- `admin`

Comportamento atual:

- Apenas usuarios com tipo `admin` podem excluir locais de coleta.
- Se o local tiver campanhas ou estoque de sangue vinculado, a exclusao e bloqueada.
- Se o local nao tiver vinculos, exclui o registro.
- Apos excluir, retorna para a pagina anterior com mensagem de sucesso.

## Tela de campanhas

`GET /admin/campanhas`

Exibe a tela administrativa de campanhas.

Controller:

```text
App\Http\Controllers\Admin\CampanhaController@index
```

Middlewares:

- `auth`
- `admin`

View:

```text
resources/views/admin/campanhas/index.blade.php
```

Comportamento atual:

- Lista as campanhas cadastradas.
- Exibe formulario para cadastrar nova campanha.
- Permite abrir o formulario de edicao de cada campanha.
- Permite solicitar exclusao de campanhas sem agendamentos vinculados.
- Exibe tipos sanguineos alvo como lista; ausencia de tipos indica todos.

## Cadastrar campanha

`POST /admin/campanhas`

Cadastra uma campanha de doacao de sangue.

Controller:

```text
App\Http\Controllers\Admin\CampanhaController@store
```

Middlewares:

- `auth`
- `admin`

Campos:

- `local_coleta_id`: obrigatorio, inteiro e deve existir em `locais_coleta`.
- `titulo`: obrigatorio, texto e maximo de 255 caracteres.
- `descricao`: obrigatorio, texto e maximo de 5000 caracteres.
- `tipos_sanguineos_alvo`: opcional e deve ser uma lista.
- `tipos_sanguineos_alvo.*`: deve ser um tipo sanguineo aceito e nao pode repetir.
- `meta_bolsas`: obrigatorio, inteiro, minimo de 1 e maximo de 100000.
- `data_inicio`: obrigatorio, data e posterior ou igual ao dia atual.
- `data_fim`: obrigatorio, data e posterior ou igual a `data_inicio`.

Comportamento atual:

- Apenas usuarios com tipo `admin` podem cadastrar campanhas.
- A campanha e criada pelo admin autenticado.
- Se nenhum tipo sanguineo alvo for marcado, a campanha considera todos os tipos.
- Campanhas novas entram com status `ativa`.
- Apos cadastrar, retorna para a pagina anterior com mensagem de sucesso.

## Atualizar campanha

`PUT /admin/campanhas/{campanha}`

Atualiza os dados de uma campanha.

Controller:

```text
App\Http\Controllers\Admin\CampanhaController@update
```

Middlewares:

- `auth`
- `admin`

Campos:

- `local_coleta_id`: obrigatorio, inteiro e deve existir em `locais_coleta`.
- `titulo`: obrigatorio, texto e maximo de 255 caracteres.
- `descricao`: obrigatorio, texto e maximo de 5000 caracteres.
- `tipos_sanguineos_alvo`: opcional e deve ser uma lista.
- `tipos_sanguineos_alvo.*`: deve ser um tipo sanguineo aceito e nao pode repetir.
- `meta_bolsas`: obrigatorio, inteiro, minimo de 1 e maximo de 100000.
- `data_inicio`: obrigatorio e deve ser uma data.
- `data_fim`: obrigatorio, data e posterior ou igual a `data_inicio`.
- `status`: obrigatorio e deve ser `ativa`, `encerrada` ou `cancelada`.

Comportamento atual:

- Apenas usuarios com tipo `admin` podem atualizar campanhas.
- Se nenhum tipo sanguineo alvo for marcado, a campanha considera todos os tipos.
- Se a campanha existir e os dados forem validos, atualiza o registro.
- Apos atualizar, retorna para a pagina anterior com mensagem de sucesso.

## Excluir campanha

`DELETE /admin/campanhas/{campanha}`

Exclui uma campanha.

Controller:

```text
App\Http\Controllers\Admin\CampanhaController@destroy
```

Middlewares:

- `auth`
- `admin`

Comportamento atual:

- Apenas usuarios com tipo `admin` podem excluir campanhas.
- Se a campanha tiver agendamentos vinculados, a exclusao e bloqueada.
- Se a campanha nao tiver vinculos, exclui o registro.
- Apos excluir, retorna para a pagina anterior com mensagem de sucesso.

## Cadastro

`GET /cadastro`

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

`POST /cadastro`

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

## Tela de usuarios

`GET /admin/usuarios`

Exibe a tela administrativa de usuarios.

Controller:

```text
App\Http\Controllers\Admin\UserController@index
```

Middlewares:

- `auth`
- `admin`

View:

```text
resources/views/admin/usuarios/index.blade.php
```

Comportamento atual:

- Lista usuarios cadastrados com nome, e-mail e tipo.
- Usa componente Livewire para buscar usuarios por nome ou e-mail sem recarregar a pagina.
- Mantem a busca no parametro `busca` da URL.
- Pagina a listagem sem recarregar a pagina.
- Exibe resumo com total de usuarios, administradores e doadores.
- Quando ha busca, exibe tambem o total de resultados encontrados.
- Usuarios doadores podem ser promovidos para administradores.
- Usuarios administradores aparecem com acao desabilitada.

## Promover usuario para admin

`POST /admin/usuarios/{user}/promover-admin`

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

`GET /health`

Retorna um JSON simples indicando que a aplicacao esta respondendo.

Resposta atual:

```json
{
  "status": "ok"
}
```
