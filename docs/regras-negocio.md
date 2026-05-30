# Regras de negocio

Este documento registra regras que afetam comportamento do sistema.

## Login

- O campo `email` e obrigatorio.
- O campo `email` deve ser um e-mail valido.
- O campo `password` e obrigatorio.
- Se as credenciais forem invalidas, o erro aparece no campo `email`.
- Se as credenciais forem validas, a sessao e regenerada.

## Cadastro

- O campo `name` e obrigatorio.
- O campo `name` deve ter no maximo 255 caracteres.
- O campo `email` e obrigatorio.
- O campo `email` deve ser um e-mail valido.
- O campo `email` deve ter no maximo 255 caracteres.
- O campo `email` deve ser unico na tabela `users`.
- O campo `password` e obrigatorio.
- O campo `password` deve ter no minimo 8 caracteres.
- O campo `password` deve ser confirmado por `password_confirmation`.
- Se o cadastro for valido, o usuario e criado.
- Apos cadastro valido, o sistema exibe uma mensagem de sucesso e redireciona para o login.

## Usuarios e permissoes

- Usuarios criados pelo cadastro comum entram como `doador`.
- O cadastro comum nao permite criar usuario com tipo `admin`.
- Apenas usuarios com tipo `admin` podem promover outro usuario para admin.
- A promocao de privilegio altera o tipo do usuario promovido para `admin`.

## Conta do usuario

- Apenas usuarios autenticados podem atualizar os dados da propria conta.
- O campo `name` e obrigatorio e deve ter no maximo 255 caracteres.
- O campo `email` e obrigatorio, deve ser valido, deve ter no maximo 255 caracteres e deve ser unico.
- Na atualizacao, o e-mail atual do proprio usuario nao conta como duplicado.
- A senha e opcional na atualizacao da conta.
- Se uma nova senha for informada, ela deve ter no minimo 8 caracteres e ser confirmada por `password_confirmation`.
- A atualizacao da conta nao altera o tipo do usuario.
- Apenas usuarios autenticados podem excluir a propria conta.
- Para excluir a conta, a senha atual deve ser confirmada.
- Ao excluir a conta, a sessao atual e encerrada.

## Carteirinha de doador

- Apenas usuarios autenticados com tipo `doador` podem emitir carteirinha.
- Cada usuario doador pode ter apenas uma carteirinha.
- O `cpf` e obrigatorio, deve ter 11 digitos e deve ser unico.
- O `cpf` e salvo apenas com digitos.
- O `telefone` e obrigatorio.
- A `data_nascimento` e obrigatoria e nao pode ser futura.
- O `tipo_sanguineo` e obrigatorio e deve estar na lista de tipos aceitos pelo sistema.
- O `peso` e obrigatorio.
- A `cidade` e obrigatoria.
- Carteirinhas emitidas pelo fluxo comum entram com status `ativa`.
- A data de emissao e preenchida automaticamente pelo sistema.
- O doador pode atualizar os dados da propria carteirinha.
- A atualizacao da carteirinha nao altera `status` nem `emitida_em`.
