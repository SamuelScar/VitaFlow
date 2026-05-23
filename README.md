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

Para parar:

```powershell
docker compose down
```
