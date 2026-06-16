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

- A tela permite retornar a pagina anterior ou acessar a home publica.
- O campo `email` e obrigatorio.
- O campo `email` deve ser um e-mail valido.
- O campo `password` e obrigatorio.
- Se as credenciais forem invalidas, o erro aparece no campo `email`.
- Se as credenciais forem validas, a sessao e regenerada.

## Cadastro

- A tela permite retornar a pagina anterior ou acessar a home publica.
- O campo `name` e obrigatorio.
- O campo `name` deve ter no maximo 255 caracteres.
- O campo `email` e obrigatorio.
- O campo `email` deve ser um e-mail valido.
- O campo `email` deve ter no maximo 255 caracteres.
- O campo `email` deve ser unico na tabela `users`.
- O e-mail e normalizado para letras minusculas antes de ser salvo.
- O campo `password` e obrigatorio.
- O campo `password` deve ter no minimo 8 caracteres.
- O campo `password` deve ser confirmado por `password_confirmation`.
- Se o cadastro for valido, o usuario e criado.
- Apos cadastro valido, o sistema exibe uma mensagem de sucesso e redireciona para o login.

## Recuperacao de senha

- A tela de solicitacao permite retornar a pagina anterior ou acessar a home publica.
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
- Os perfis `admin` e `doador` sao exclusivos.
- Um doador existente nao pode ser promovido para administrador.
- Apenas usuarios com tipo `admin` podem acessar a listagem administrativa de usuarios.
- A listagem administrativa de usuarios pode ser filtrada por nome ou e-mail sem recarregar a pagina.
- A listagem administrativa de usuarios pode ser filtrada por perfil.
- A listagem administrativa de usuarios e paginada pelo componente Livewire.
- Apenas usuarios com tipo `admin` podem enviar, reenviar ou cancelar convites administrativos.
- O e-mail convidado nao pode pertencer a um usuario existente.
- Convites administrativos expiram em 48 horas e podem ser aceitos apenas uma vez.
- Convites pendentes ou expirados podem ser reenviados.
- Links de convites aceitos ou cancelados nao podem ser utilizados.
- Um e-mail cujo convite foi cancelado pode receber um novo convite.
- O aceite do convite cria diretamente um usuario com tipo `admin` e marca seu e-mail como verificado.
- Administradores criados por convite nao recebem dados pessoais de doador nem carteirinha.

## Conta do usuario

- Apenas usuarios autenticados podem atualizar os dados da propria conta.
- O campo `name` e obrigatorio e deve ter no maximo 255 caracteres.
- O campo `email` e obrigatorio, deve ser valido, deve ter no maximo 255 caracteres e deve ser unico.
- O e-mail e normalizado para letras minusculas antes de ser salvo.
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
- Locais de coleta com campanhas ou bolsas de sangue vinculadas nao podem ser excluidos.

## Campanhas

- Apenas usuarios autenticados com tipo `admin` podem cadastrar, atualizar ou excluir campanhas.
- O campo `local_coleta_id` e obrigatorio e deve apontar para um local de coleta existente.
- O campo `titulo` e obrigatorio e deve ter no maximo 255 caracteres.
- O campo `descricao` e obrigatorio e deve ter no maximo 5000 caracteres.
- O campo `tipos_sanguineos_alvo` e opcional e deve ser uma lista de tipos sanguineos aceitos pelo sistema.
- Se nenhum tipo sanguineo alvo for informado, a campanha considera todos os tipos sanguineos como alvo.
- O campo `meta_bolsas` e obrigatorio, deve ser inteiro e deve ficar entre 1 e 100000.
- O campo `agendamentos_por_horario` e obrigatorio e deve ficar entre 1 e 100.
- `horario_inicio` e `horario_fim` sao obrigatorios.
- `horario_fim` deve ser posterior a `horario_inicio`.
- O campo `data_inicio` e obrigatorio.
- Ao cadastrar uma campanha, `data_inicio` nao pode ser anterior ao dia atual.
- O campo `data_fim` e obrigatorio e deve ser posterior ou igual a `data_inicio`.
- Campanhas novas entram com status `ativa`.
- Na atualizacao, o status deve ser `ativa`, `encerrada` ou `cancelada`.
- Campanhas com agendamentos vinculados nao podem ser excluidas.

## Carteirinha de doador

- Apenas usuarios autenticados com tipo `doador` podem emitir carteirinha.
- Cada usuario doador pode ter apenas uma carteirinha.
- `cpf`, `telefone`, `data_nascimento`, `sexo`, `tipo_sanguineo`, `peso` e `cidade` pertencem ao usuario.
- O `cpf` e obrigatorio, deve ter 11 digitos e deve ser unico.
- O `cpf` e salvo apenas com digitos.
- Na atualizacao, a validacao de CPF unico ignora o proprio usuario.
- O `telefone` e obrigatorio e deve ter no maximo 20 caracteres.
- A `data_nascimento` e obrigatoria e nao pode ser futura.
- O `sexo` e obrigatorio e deve ser `masculino` ou `feminino`.
- O `tipo_sanguineo` e obrigatorio e deve estar na lista de tipos aceitos pelo sistema.
- O `peso` e obrigatorio, deve ser numerico e deve ficar entre 0.01 e 999.99.
- A `cidade` e obrigatoria e deve ter no maximo 255 caracteres.
- Carteirinhas emitidas pelo fluxo comum entram com status `ativa`.
- A data de emissao e preenchida automaticamente pelo sistema.
- A tela da carteirinha permite atualizar os dados do usuario sem alterar `status` nem `emitida_em`.
- Apenas administradores podem alterar o status de uma carteirinha ja emitida entre `ativa` e `inativa`.
- Doadores sem carteirinha emitida e usuarios administradores nao possuem status de carteirinha para alterar.
- Um usuario somente pode realizar agendamentos quando for doador e possuir uma carteirinha ativa.
- O agendamento e vinculado ao usuario doador.
- O mesmo doador nao pode agendar a mesma campanha mais de uma vez.
- A campanha precisa estar ativa e dentro do periodo vigente para aceitar agendamento.
- A data e horario do agendamento nao podem estar no passado.
- A data e horario do agendamento devem estar dentro do periodo da campanha.
- O horario do agendamento deve ficar dentro da janela diaria de atendimento da campanha.
- Agendamentos so podem ser feitos em horarios de 30 em 30 minutos.
- Cada horario respeita o limite configurado em `agendamentos_por_horario` da campanha.
- O doador deve respeitar o intervalo minimo entre doacoes ou agendamentos ativos.
- Para `sexo` masculino, o intervalo minimo e de 60 dias.
- Para `sexo` feminino, o intervalo minimo e de 90 dias.
- Horarios dentro do intervalo minimo ficam bloqueados no calendario e tambem sao recusados pelo backend.
- O doador pode consultar apenas os proprios agendamentos.
- Apenas agendamentos com status `agendado` e data futura podem ser cancelados ou reagendados pelo doador.
- O cancelamento do doador altera o status do agendamento para `cancelado`.
- O reagendamento nao cria um novo registro; ele atualiza a data e horario do agendamento existente.
- O reagendamento aplica as mesmas regras de periodo, horario de atendimento, intervalo de 30 minutos, vagas por horario e intervalo minimo entre doacoes.
- No reagendamento, o proprio agendamento e ignorado no calculo de ocupacao e intervalo minimo.
- Apenas administradores podem acessar a visao administrativa de agendamentos.
- A visao administrativa de agendamentos pode ser filtrada por campanha, local de coleta, status e periodo.
- Os filtros da visao administrativa de agendamentos sao aplicados sem recarregar a pagina inteira.
- O acompanhamento administrativo exibe dados do doador, campanha, local, status do agendamento e doacao vinculada quando existir.

## Bolsas de sangue e estoque

- Uma doacao confirmada com quantidade coletada gera uma unica bolsa de sangue.
- Uma doacao recusada nao gera bolsa de sangue.
- A bolsa nasce no local da campanha vinculada ao agendamento.
- A validade da bolsa e definida como 42 dias apos a data da coleta.
- O vencimento e determinado automaticamente pela data de validade, sem atualizacao manual do status persistido.
- Bolsas disponiveis e transferidas podem ser utilizadas, descartadas ou transferidas.
- Bolsas utilizadas, descartadas ou vencidas nao podem ser movimentadas.
- A transferencia exige um local de destino diferente do local atual.
- Bolsas transferidas continuam disponiveis e passam a compor o estoque do destino.
- O estoque considera somente bolsas disponiveis ou transferidas e dentro da validade.
- `estoques_sangue` armazena apenas o estoque minimo configurado por local e tipo sanguineo.
- O administrador pode configurar o estoque minimo entre 0 e 1.000.000 ml.
- Apenas administradores podem consultar e movimentar bolsas de sangue.
- Movimentacoes bloqueiam a bolsa durante a operacao para impedir alteracoes simultaneas conflitantes.

## Validacao e feedback visual

- Formularios com `data-validate-form` fazem uma validacao inicial no navegador antes de enviar para o backend.
- Quando ha erro no formulario, o sistema exibe um alerta lateral discreto e rola ate o primeiro campo invalido.
- Erros retornados pelo backend sao apresentados com SweetAlert, incluindo formularios que usam error bags nomeadas.
- Mensagens de sucesso tambem usam alerta lateral.
- Acoes destrutivas solicitam confirmacao com SweetAlert antes do envio.
- Alertas laterais pausam o temporizador quando o usuario passa o mouse.
- A validacao no navegador nao substitui a validacao do backend.

## Tema visual

- O sistema oferece tema `Sistema`, `Claro` e `Escuro`.
- A opcao padrao e `Sistema`, seguindo `prefers-color-scheme` do navegador.
- A preferencia do usuario fica salva em `localStorage`.
