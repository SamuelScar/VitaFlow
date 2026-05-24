# Regras de negocio

Este documento registra regras que afetam comportamento do sistema.

## Login

- O campo `email` e obrigatorio.
- O campo `email` deve ser um e-mail valido.
- O campo `password` e obrigatorio.
- Se as credenciais forem invalidas, o erro aparece no campo `email`.
- Se as credenciais forem validas, a sessao e regenerada.
- O campo `remember` e opcional e mantem o usuario conectado quando marcado.
