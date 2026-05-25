# Banco de dados

Este documento registra as entidades principais, relacionamentos e decisoes de modelagem do banco.

## Estado atual

O projeto usa PostgreSQL.

Configuracao do ambiente local:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=vitaflow
DB_USERNAME=vitaflow
DB_PASSWORD=vitaflow
```

## Seeds

O sistema cria um usuario administrador padrao para permitir o primeiro acesso administrativo.

Credenciais do administrador padrao:

```txt
E-mail: admin@vitaflow.local
Senha: Admin@123
```

Usuarios criados pelo cadastro comum entram como `doador`. Para se tornar `admin`, o usuario deve ser promovido posteriormente por outro administrador.
