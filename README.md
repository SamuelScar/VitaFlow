# VitaFlow

Ambiente Docker para desenvolvimento de um monolito Laravel com PostgreSQL, Livewire e assets via Node 18/Vite.

## Estrutura

```text
docker/              # Imagem local da aplicacao
docker-compose.yml   # Servicos app + db
src/                 # Aplicacao Laravel, criada depois
```

## Servicos

- Aplicacao Laravel: http://localhost:8080
- Vite dev server: http://localhost:5173
- PostgreSQL: `localhost:5432`
- Banco: `vitaflow`
- Usuario: `vitaflow`
- Senha: `vitaflow`

## Subir o ambiente

```powershell
docker compose up -d --build
```

O container da aplicacao ja inicia o Apache automaticamente e ajusta as permissoes de escrita do Laravel em `storage/` e `bootstrap/cache/`. Isso evita erro de permissao ao usar o projeto em outra maquina.

## Criar o Laravel depois

Com `src/` vazia, rode na raiz do projeto:

```powershell
docker compose run --rm app composer create-project laravel/laravel .
docker compose run --rm app composer require livewire/livewire
```

Configure o `src/.env` do Laravel:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=vitaflow
DB_USERNAME=vitaflow
DB_PASSWORD=vitaflow
```

Depois:

```powershell
docker compose exec app php artisan migrate
```

## Rodar em outra maquina

Com o Laravel ja criado em `src/`, rode:

```powershell
docker compose up -d --build
docker compose exec app php artisan migrate
```

Use comandos do Laravel e Composer dentro do container:

```powershell
docker compose exec app php artisan ...
docker compose exec app composer ...
```

Para parar:

```powershell
docker compose down
```
