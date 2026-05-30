# Identidade visual

A identidade usa Bootstrap como base, com variaveis CSS proprias para manter consistencia visual.

## Paleta clara

- Principal: `#C62828`
- Principal escura: `#B71C1C`
- Secundaria: `#1565C0`
- Sucesso: `#2E7D32`
- Alerta/Pendente: `#F9A825`
- Fundo: `#F8FAFC`
- Superficie/cards: `#FFFFFF`
- Bordas: `#E5E7EB`
- Texto principal: `#1F2937`
- Texto secundario: `#6B7280`

## Paleta escura

- Principal: `#EF5350`
- Principal escura: `#D32F2F`
- Secundaria: `#64B5F6`
- Sucesso: `#66BB6A`
- Alerta/Pendente: `#FDD663`
- Fundo: `#0F172A`
- Superficie/cards: `#111827`
- Superficie secundaria: `#1F2937`
- Bordas: `#334155`
- Texto principal: `#F8FAFC`
- Texto secundario: `#CBD5E1`

## Uso

- Acoes principais: vermelho principal.
- Acoes secundarias: azul.
- Cards: fundo branco, borda cinza clara e texto grafite.
- Status confirmado/compareceu: verde.
- Status pendente: ambar com texto escuro.
- Status cancelado/erro: vermelho escuro.
- Status nao compareceu: cinza.

## Tema

O sistema usa o suporte nativo do Bootstrap por `data-bs-theme`.

Opcoes disponiveis:

- `Sistema`: padrao, segue `prefers-color-scheme` do navegador.
- `Claro`: forca o tema claro.
- `Escuro`: forca o tema escuro.

Comportamento:

- A preferencia fica salva em `localStorage` com a chave `vitaflow-theme`.
- Um script inline no layout aplica o tema antes do carregamento dos assets para evitar troca visual tardia.
- O seletor fica na navbar como um icone discreto ao lado das acoes do usuario.

## Icones

O projeto usa Bootstrap Icons.

Uso esperado:

- botoes de acao devem usar icone quando houver simbolo claro;
- dashboards e cards usam icones para facilitar leitura rapida;
- badges podem usar icones pequenos para indicar contexto;
- icones devem ser decorativos com `aria-hidden="true"` quando o texto ja explica a acao.

## Alertas

- Mensagens de sucesso usam toast lateral.
- Erros de validacao client-side usam toast lateral de aviso.
- Toasts pausam o temporizador no hover.
- Confirmacoes destrutivas podem usar alerta central.
- Exclusao de conta usa alerta central com espera obrigatoria, senha e estado de carregamento.

## Arquivo principal

As variaveis e ajustes globais ficam em:

```text
src/resources/css/app.css
```
