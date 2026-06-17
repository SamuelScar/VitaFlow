# Pendências de implementação

Este documento resume o status dos casos de uso do VitaFlow a partir da tabela de entregas.

<table>
    <thead>
        <tr>
            <th>UC</th>
            <th>Caso de uso</th>
            <th>Entrega</th>
            <th>Status</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>UC1</td>
            <td>Visualizar campanhas</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Home pública lista campanhas ativas.</td>
        </tr>
        <tr>
            <td>UC2</td>
            <td>Cadastrar Conta</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Cadastro de doador implementado.</td>
        </tr>
        <tr>
            <td>UC3</td>
            <td>Fazer login</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Login por e-mail e senha implementado.</td>
        </tr>
        <tr>
            <td>UC4</td>
            <td>Gerenciar Conta</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Usuário pode editar e excluir a própria conta.</td>
        </tr>
        <tr>
            <td>UC5</td>
            <td>Gerenciar Carteirinha de Doador</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Doador pode criar, visualizar e atualizar a carteirinha.</td>
        </tr>
        <tr>
            <td>UC6</td>
            <td>Realizar agendamento</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Doador com carteirinha ativa pode agendar data e horario em campanha disponivel.</td>
        </tr>
        <tr>
            <td>UC7</td>
            <td>Gerenciar agendamento</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Doador pode consultar, cancelar e reagendar agendamentos ativos.</td>
        </tr>
        <tr>
            <td>UC8</td>
            <td>Visualizar dados de suas doações</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #f8d7da;"><strong>Pendente</strong></td>
            <td>Exibir histórico de doações realizadas e separar de agendamentos pendentes.</td>
        </tr>
        <tr>
            <td>UC9</td>
            <td>Cadastrar local de coleta</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Cadastro administrativo de locais implementado.</td>
        </tr>
        <tr>
            <td>UC10</td>
            <td>Gerenciar locais de coleta</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Listagem, edição e remoção de locais implementadas.</td>
        </tr>
        <tr>
            <td>UC11</td>
            <td>Cadastrar campanha</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Cadastro administrativo de campanhas implementado.</td>
        </tr>
        <tr>
            <td>UC12</td>
            <td>Gerenciar campanha</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Listagem, edição e remoção de campanhas implementadas.</td>
        </tr>
        <tr>
            <td>UC13</td>
            <td>Gerenciar horários e vagas</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #fff3cd;"><strong>Parcial</strong></td>
            <td>Limite por horario configurado na campanha; ainda falta gestao completa de janelas.</td>
        </tr>
        <tr>
            <td>UC14</td>
            <td>Acompanhar agendamentos</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Visao administrativa com filtros por campanha, local, data e status implementada.</td>
        </tr>
        <tr>
            <td>UC15</td>
            <td>Registrar comparecimento</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Admin pode marcar e corrigir comparecimento, falta ou cancelamento operacional dentro da janela de 24h apos o horario.</td>
        </tr>
        <tr>
            <td>UC16</td>
            <td>Registrar doação</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Admin registra doacao confirmada ou recusada a partir de agendamento realizado e com horario ja iniciado; doacao confirmada gera bolsa.</td>
        </tr>
        <tr>
            <td>UC17</td>
            <td>Gerar relatórios</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #f8d7da;"><strong>Pendente</strong></td>
            <td>Criar relatórios de campanhas, agendamentos, comparecimentos, doações e estoque.</td>
        </tr>
        <tr>
            <td>UC18</td>
            <td>Visualizar agendamento</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #fff3cd;"><strong>Parcial</strong></td>
            <td>Tela de detalhe do doador implementada; ainda falta detalhe administrativo.</td>
        </tr>
        <tr>
            <td>UC19</td>
            <td>Participar de campanha</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>A acao da campanha direciona o doador para o fluxo de agendamento.</td>
        </tr>
        <tr>
            <td>UC20</td>
            <td>Fazer logout</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Logout autenticado implementado.</td>
        </tr>
        <tr>
            <td>UC21</td>
            <td>Recuperar senha</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Solicitação e redefinição de senha implementadas.</td>
        </tr>
        <tr>
            <td>UC22</td>
            <td>Gerenciar estoque de sangue</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Gestão de bolsas, estoque calculado, filtros, transferência, descarte, utilização e estoque mínimo implementados.</td>
        </tr>
        <tr>
            <td>UC23</td>
            <td>Gerenciar usuários</td>
            <td style="background-color: #d1e7dd;">09/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Listagem de usuários e convites administrativos implementados.</td>
        </tr>
    </tbody>
</table>

## Resumo

- **Feitos**: UC1, UC2, UC3, UC4, UC5, UC6, UC7, UC9, UC10, UC11, UC12, UC14, UC15, UC16, UC19, UC20, UC21, UC22 e UC23.
- **Parcial**: UC13 e UC18.
- **Pendentes**: UC8 e UC17.

## Pendências avulsas

- Exibir aviso para o usuário quando o e-mail ainda não estiver verificado.
- Mostrar o status de verificação de e-mail nos dados da conta do usuário.
- Bloquear a criação da carteirinha de doador enquanto o e-mail do usuário não estiver verificado.
- Orientar o usuário, no bloqueio da carteirinha, a verificar o e-mail antes de continuar.

## Observações

- Mudanças concluídas devem ser registradas em `docs/alteracoes.md`.
- Ajustes que alterem fluxo, entidades ou regras devem refletir também em `docs/fluxos.md`, `docs/regras-negocio.md` e `docs/modelo-relacional.dbml`, quando aplicável.
- A tela de detalhe administrativa de agendamento ainda precisa ser criada para concluir UC18.
