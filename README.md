# VitaFlow

Ambiente Docker para desenvolvimento de um monolito Laravel com PostgreSQL, Livewire e assets via Node 22/Vite.

## Estrutura

```text
docker/              # Imagem local da aplicacao
docs/                # Documentacao funcional e tecnica do sistema
docker-compose.yml   # Servicos app + db
src/                 # Aplicacao Laravel
```

## Documentacao

A documentacao do sistema fica em [`docs/sistema.md`](docs/sistema.md).

Ela registra telas, rotas web, fluxos, regras de negocio e decisoes visuais do monolito.

## Servicos

- Aplicacao Laravel: http://localhost:8080
- Vite dev server: http://localhost:5173
- PostgreSQL: `localhost:5432`
- Banco: `vitaflow`
- Usuario: `vitaflow`
- Senha: `vitaflow`

## Subir o ambiente

As variaveis usadas no projeto ficam no `.env` da raiz, separadas por comentarios entre Docker e Laravel. Use o `.env.example` como referencia.

```powershell
docker compose up -d --build
```

O container da aplicacao ja inicia o Apache automaticamente e ajusta as permissoes de escrita do Laravel em `storage/` e `bootstrap/cache/`. Isso evita erro de permissao ao usar o projeto em outra maquina.

Para subir o ambiente com o Vite dev server junto, altere no `.env`:

```env
RUN_VITE_DEV=true
```

Com `RUN_VITE_DEV=false`, o Vite nao inicia automaticamente. Nesse caso, gere os assets com `docker compose exec app npm run build` ou rode o dev server manualmente com `docker compose exec app npm run dev -- --host 0.0.0.0`.

## Recriar a base Laravel do zero

Esta etapa so e necessaria se a pasta `src/` estiver vazia e o projeto precisar ser recriado do zero:

```powershell
docker compose run --rm app composer create-project laravel/laravel .
docker compose run --rm app composer require livewire/livewire
```

Depois de criar a base, confira no `.env` da raiz se o Laravel esta usando PostgreSQL:

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
