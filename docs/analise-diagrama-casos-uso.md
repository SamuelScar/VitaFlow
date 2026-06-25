# Revisao do diagrama de casos de uso

## Veredito

O diagrama esta adequado para representar o VitaFlow.

Considerando que algumas acoes ficam abstraidas dentro de casos maiores, os principais casos de uso do sistema estao cobertos.

## Cobertura por ator

### Visitante

Coberto pelo diagrama:

- visualizar campanhas;
- cadastrar conta;
- recuperar senha;
- aceitar convite admin;
- verificar e-mail;
- fazer login.

### Doador

Coberto pelo diagrama:

- gerenciar conta;
- excluir conta;
- gerenciar carteirinha de doador;
- agendar doacao em campanha;
- gerenciar agendamento;
- visualizar dados de suas doacoes;
- fazer login;
- fazer logout.

Observacao: visualizar, cancelar e reagendar agendamento podem ficar abstraidos dentro de `Gerenciar Agendamento`.

### Administrador

Coberto pelo diagrama:

- cadastrar local de coleta;
- gerenciar locais de coleta;
- cadastrar campanha;
- gerenciar campanha;
- gerenciar horarios e vagas;
- acompanhar agendamentos;
- registrar comparecimento;
- registrar doacao;
- gerenciar estoque de sangue;
- gerar relatorios;
- baixar relatorio;
- gerenciar relatorio;
- gerenciar usuarios;
- enviar/gerenciar convite para admin;
- fazer login;
- fazer logout.

## Abstracoes aceitas

- `Gerenciar estoque de sangue` cobre consultar bolsas, calcular estoque, atualizar estoque minimo, utilizar, descartar e transferir bolsas.
- `Gerenciar Agendamento` cobre listar, visualizar, cancelar e reagendar agendamentos do doador.
- `Enviar/Gerenciar convite para admin` cobre envio, reenvio, cancelamento e aceite do convite.
- `Gerenciar Usuarios` cobre listagem, filtros e alteracao administrativa do status da carteirinha.
- `Gerenciar relatorio` cobre historico, arquivamento, desarquivamento e exclusao.
- `Gerenciar campanha` cobre visualizacao, edicao, exclusao e acompanhamento operacional da campanha.
- `Gerenciar locais de coleta` cobre edicao e exclusao de locais cadastrados.

## Ajuste opcional

O unico nome que pode ser refinado e:

- `Gerenciar horarios e vagas`

Nome mais preciso:

- `Configurar horarios e vagas da campanha`

Isso porque horarios e vagas sao configuracoes da campanha, nao um modulo separado.

## Conclusao

O diagrama cobre o sistema atual.

Nao ha caso de uso essencial faltando. As diferencas restantes sao apenas escolhas de abstracao e nomenclatura.
