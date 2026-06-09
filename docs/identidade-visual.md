# Identidade visual

Usa Bootstrap com CSS customizadas. Paleta vermelha (#C62828) como principal, azul (#1565C0) secundário, verde (#2E7D32) para sucesso e amarelo (#F9A825) para alertas.

## Temas

- Sistema: segue `prefers-color-scheme` do navegador
- Claro / Escuro: opções forçadas
- Preferência salva em `localStorage` com a chave `vitaflow-theme`

## Cores por uso

| Status | Cor |
|--------|-----|
| Ação principal | Vermelho |
| Ação secundária | Azul |
| Confirmado | Verde |
| Pendente | Amarelo |
| Cancelado/Erro | Vermelho escuro |

## Componentes

- **Logo**: `src/public/assets/images/logo-vitaflow-drop.png` (42px de altura)
- **Favicon**: `src/public/favicon.ico`
- **Ícones**: Bootstrap Icons — usar em botões e dashboards quando apropriado
- **Alertas**: toasts laterais para sucesso e validação; alerta central para ações destrutivas

## Estilos globais

Variáveis e customizações em `src/resources/css/app.css`
