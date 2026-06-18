# Pendências de implementação

Este documento resume o status dos casos de uso do VitaFlow a partir da tabela de entregas.

Última verificação por inspeção do código: 17/06/2026.

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
            <td>Doador com carteirinha ativa pode agendar data e horário em campanha disponível.</td>
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
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Doador visualiza histórico de doações realizadas e impacto pessoal.</td>
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
            <td>Listagem, edição, remoção e detalhe de campanhas implementados.</td>
        </tr>
        <tr>
            <td>UC13</td>
            <td>Gerenciar horários e vagas</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Ocupação de horários com FullCalendar no detalhe da campanha implementada.</td>
        </tr>
        <tr>
            <td>UC14</td>
            <td>Acompanhar agendamentos</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Visão administrativa implementada com filtros e resumo por status.</td>
        </tr>
        <tr>
            <td>UC15</td>
            <td>Registrar comparecimento</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Admin pode marcar comparecimento, falta ou cancelamento operacional.</td>
        </tr>
        <tr>
            <td>UC16</td>
            <td>Registrar doação</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Admin registra doação confirmada ou recusada a partir do agendamento.</td>
        </tr>
        <tr>
            <td>UC17</td>
            <td>Gerar relatórios</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Tela de relatórios dinâmicos e exportação de PDFs implementada. Funcionalidades avançadas adicionadas: processamento de arquivo com envio de jobs para compactação ZIP assíncrona (Arquivamento), e histórico completo com Soft Deletes e ações em lote (Meus Relatórios).</td>
        </tr>
        <tr>
            <td>UC18</td>
            <td>Visualizar agendamento</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Telas de detalhe para o doador e para o admin implementadas.</td>
        </tr>
        <tr>
            <td>UC19</td>
            <td>Participar de campanha</td>
            <td style="background-color: #fff3cd;">30/06</td>
            <td style="background-color: #d1e7dd;"><strong>Feito</strong></td>
            <td>Fluxo de agendamento do doador finalizado.</td>
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
            <td>Gestão de bolsas e estoque calculado implementada.</td>
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

- **Casos de uso feitos**: UC1 a UC23.
- **Casos de uso pendentes**: nenhum identificado por inspeção do código atual.
- **Pendências avulsas abertas**: nenhuma.

## Pendências avulsas

- [x] Exibir aviso para o usuário quando o e-mail ainda não estiver verificado.
- [x] Mostrar o status de verificação de e-mail nos dados da conta do usuário.
- [x] Bloquear a criação da carteirinha de doador enquanto o e-mail do usuário não estiver verificado.
- [x] Orientar o usuário, no bloqueio da carteirinha, a verificar o e-mail antes de continuar.
- [x] Revisar filtros do Livewire por status nas telas administrativas. Por inspeção, os filtros atuais validam valores aceitos e aplicam as consultas sem o erro global descrito anteriormente.
- [x] Otimização da geração de PDF de relatórios: geração movida para fila assíncrona com registro de status e download posterior; mantido DomPDF sem Chrome Headless e sem limite fixo de registros neste momento.
- [x] Painel de gráficos dos relatórios: implementada visualização com indicadores, gráfico principal em Chart.js, seleção de múltiplos gráficos para PDF e gráficos estáticos no relatório exportado.

## Observações

- Mudanças concluídas devem ser registradas em `docs/alteracoes.md`.
- Ajustes que alterem fluxo, entidades ou regras devem refletir também em `docs/fluxos.md`, `docs/regras-negocio.md` e `docs/modelo-relacional.dbml`, quando aplicável.
- Esta verificação foi feita por leitura dos arquivos do projeto; nenhum teste, build ou lint foi executado.
