# Regras de negocio

Este documento registra regras que afetam comportamento do sistema.

## Home publica

- Visitantes e usuarios autenticados podem acessar a home publica.
- A home publica exibe apenas campanhas com status `ativa`.
- A home publica exibe apenas campanhas dentro do periodo vigente.
- Campanhas com `data_inicio` futura nao aparecem como abertas.
- Campanhas com `data_fim` anterior ao dia atual nao aparecem como abertas.
- A ausencia de tipos sanguineos alvo indica que a campanha aceita todos os tipos.
- A listagem de campanhas abertas e paginada.
- O visitante pode escolher exibir 6, 12, 24 ou 48 campanhas por pagina.
- O resumo publico considera todas as campanhas abertas, independentemente da pagina atual.

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

## Recuperacao de senha

- Visitantes podem solicitar um link de redefinicao de senha pelo e-mail cadastrado.
- O campo `email` e obrigatorio e deve ser um e-mail valido.
- Se o e-mail existir, o sistema envia o link usando o mailer configurado no Laravel.
- O link de redefinicao usa token armazenado em `password_reset_tokens`.
- O token expira em 60 minutos.
- O sistema limita novas solicitacoes para o mesmo e-mail por 60 segundos.
- Para redefinir a senha, o formulario exige token, e-mail, senha e confirmacao.
- A nova senha deve ter no minimo 8 caracteres e ser confirmada por `password_confirmation`.
- A nova senha nao pode ser igual a senha atual do usuario.
- Se o token for valido, a senha do usuario e atualizada e o token de "lembrar-me" e renovado.

## Usuarios e permissoes

- Usuarios criados pelo cadastro comum entram como `doador`.
- O cadastro comum nao permite criar usuario com tipo `admin`.
- Apenas usuarios com tipo `admin` podem acessar a listagem administrativa de usuarios.
- A listagem administrativa de usuarios pode ser filtrada por nome ou e-mail sem recarregar a pagina.
- A listagem administrativa de usuarios e paginada pelo componente Livewire.
- Apenas usuarios com tipo `admin` podem promover outro usuario para admin.
- A promocao de privilegio altera o tipo do usuario promovido para `admin`.
- A promocao nao altera nome, e-mail, senha ou dados de doador.

## Conta do usuario

- Apenas usuarios autenticados podem atualizar os dados da propria conta.
- O campo `name` e obrigatorio e deve ter no maximo 255 caracteres.
- O campo `email` e obrigatorio, deve ser valido, deve ter no maximo 255 caracteres e deve ser unico.
- Na atualizacao, o e-mail atual do proprio usuario nao conta como duplicado.
- A senha e opcional na atualizacao da conta.
- Se uma nova senha for informada, a senha atual deve ser confirmada por `current_password`.
- Se uma nova senha for informada, ela deve ter no minimo 8 caracteres e ser confirmada por `password_confirmation`.
- Se uma nova senha for informada, ela nao pode ser igual a senha atual.
- A atualizacao da conta nao altera o tipo do usuario.
- Apenas usuarios autenticados podem excluir a propria conta.
- Para excluir a conta, a senha atual deve ser confirmada.
- Ao excluir a conta, a sessao atual e encerrada.

## Locais de coleta

- Apenas usuarios autenticados com tipo `admin` podem cadastrar, atualizar ou excluir locais de coleta.
- O campo `nome` e obrigatorio e deve ter no maximo 255 caracteres.
- O campo `cep` e obrigatorio e deve seguir o formato `00000-000`.
- O campo `logradouro` e obrigatorio e deve ter no maximo 255 caracteres.
- O campo `numero` e obrigatorio e deve ter no maximo 30 caracteres.
- O campo `bairro` e obrigatorio e deve ter no maximo 255 caracteres.
- O campo `cidade` e obrigatorio e deve ter no maximo 255 caracteres.
- O campo `uf` e obrigatorio e deve ter 2 caracteres.
- O campo `complemento` e opcional e deve ter no maximo 255 caracteres.
- O campo `capacidade_diaria` e obrigatorio, deve ser inteiro e deve ficar entre 1 e 10000.
- O CEP informado pode preencher automaticamente logradouro, bairro, cidade e UF no formulario.
- A consulta de CEP e uma ajuda de preenchimento; o backend continua validando os campos enviados.
- Locais de coleta com campanhas vinculadas nao podem ser excluidos.
- Locais de coleta com estoque de sangue vinculado nao podem ser excluidos.

## Campanhas

- Apenas usuarios autenticados com tipo `admin` podem cadastrar, atualizar ou excluir campanhas.
- O campo `local_coleta_id` e obrigatorio e deve apontar para um local de coleta existente.
- O campo `titulo` e obrigatorio e deve ter no maximo 255 caracteres.
- O campo `descricao` e obrigatorio e deve ter no maximo 5000 caracteres.
- O campo `tipos_sanguineos_alvo` e opcional e deve ser uma lista de tipos sanguineos aceitos pelo sistema.
- Se nenhum tipo sanguineo alvo for informado, a campanha considera todos os tipos sanguineos como alvo.
- O campo `meta_bolsas` e obrigatorio, deve ser inteiro e deve ficar entre 1 e 100000.
- O campo `data_inicio` e obrigatorio.
- Ao cadastrar uma campanha, `data_inicio` nao pode ser anterior ao dia atual.
- O campo `data_fim` e obrigatorio e deve ser posterior ou igual a `data_inicio`.
- Campanhas novas entram com status `ativa`.
- Na atualizacao, o status deve ser `ativa`, `encerrada` ou `cancelada`.
- Campanhas com agendamentos vinculados nao podem ser excluidas.

## Carteirinha de doador

- Apenas usuarios autenticados com tipo `doador` podem emitir carteirinha.
- Cada usuario doador pode ter apenas uma carteirinha.
- `cpf`, `telefone`, `data_nascimento`, `tipo_sanguineo`, `peso` e `cidade` pertencem ao usuario.
- O `cpf` e obrigatorio, deve ter 11 digitos e deve ser unico.
- O `cpf` e salvo apenas com digitos.
- Na atualizacao, a validacao de CPF unico ignora o proprio usuario.
- O `telefone` e obrigatorio e deve ter no maximo 20 caracteres.
- A `data_nascimento` e obrigatoria e nao pode ser futura.
- O `tipo_sanguineo` e obrigatorio e deve estar na lista de tipos aceitos pelo sistema.
- O `peso` e obrigatorio, deve ser numerico e deve ficar entre 0.01 e 999.99.
- A `cidade` e obrigatoria e deve ter no maximo 255 caracteres.
- Carteirinhas emitidas pelo fluxo comum entram com status `ativa`.
- A data de emissao e preenchida automaticamente pelo sistema.
- A tela da carteirinha permite atualizar os dados do usuario sem alterar `status` nem `emitida_em`.
- Um usuario somente pode realizar agendamentos quando for doador e possuir uma carteirinha ativa.

## Validacao e feedback visual

- Formularios com `data-validate-form` fazem uma validacao inicial no navegador antes de enviar para o backend.
- Quando ha erro no formulario, o sistema exibe um alerta lateral discreto e rola ate o primeiro campo invalido.
- Mensagens de sucesso tambem usam alerta lateral.
- Alertas laterais pausam o temporizador quando o usuario passa o mouse.
- A validacao no navegador nao substitui a validacao do backend.

## Tema visual

- O sistema oferece tema `Sistema`, `Claro` e `Escuro`.
- A opcao padrao e `Sistema`, seguindo `prefers-color-scheme` do navegador.
- A preferencia do usuario fica salva em `localStorage`.
