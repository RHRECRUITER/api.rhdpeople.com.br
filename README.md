# RHdPeople — API Base

Projeto backend em Laravel 11 para suporte ao produto RHdPeople, com endpoints de:

- **Landing / Leads (v1)**: recebimento de solicitações de demonstração
- **Analytics / Tracking (v1)**: ingestão em lote (batch) de eventos do frontend

> Observação: este repositório usa Laravel 11, mas as rotas **não** ficam em `routes/`.
> Elas são carregadas a partir de `app/routes/*` (ver `bootstrap/app.php`).

## Requisitos

- PHP `^8.2`
- Composer
- Node.js + npm (para Vite/Tailwind)
- MySQL (opcional, dependendo dos drivers em `.env`)

## Como executar (dev)

### 1) Instalação

```bash
composer install
cp .env.develop.example .env
php artisan key:generate
```

### 2) Banco de dados (se aplicável)

O projeto pode rodar sem depender do banco para alguns fluxos, mas **se** você estiver usando:

- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`

então você precisa das migrations/tabelas correspondentes.

Comandos úteis:

```bash
php artisan migrate
php artisan cache:table
php artisan queue:table
php artisan queue:failed-table
php artisan queue:batches-table
php artisan migrate
```

### 3) Subir backend

```bash
php artisan serve
```

## Padrão de resposta JSON (API)

As respostas “controladas” usam `Response::api(...)` (ver `app/Providers/AppServiceProvider.php`), com o formato:

```json
{
  "success": true,
  "message": "OK",
  "data": {},
  "meta": { "timestamp": "2026-03-17T00:00:00+00:00" }
}
```

Erros automáticos (422/404/500 etc.) também são padronizados em `bootstrap/app.php`.

## Endpoints (v1)

Base URL: `{{base_url}}/api/v1`

### Landing — Leads Demo

- **POST** `{{base_url}}/api/v1/landing/leads`

Payload (exemplo):

```json
{
  "name": "João Silva",
  "company_name": "Tech Solutions",
  "corporate_email": "joao@tech.com",
  "phone": "+55 (11) 99999-9999",
  "number_employees": 50,
  "objective": {
    "time_tracking": true,
    "vacation": false,
    "payroll": true,
    "documents": false,
    "reports": false
  },
  "form_started_at": 1710280000,
  "website": ""
}
```

Regras principais:

- `corporate_email`: `email:rfc,dns`
- `objective`: somente módulos permitidos; **pelo menos 1** precisa ser `true`
- Honeypot: `website` deve estar **presente** e **vazio**
- Time-trap: `form_started_at` (epoch em segundos) deve ser válido e:
  - rejeita se “rápido demais” (`< 2s`)
  - rejeita se “antigo demais” (`> 2h`)
- Deduplicação por e-mail: bloqueia envios recentes (TTL ~ 1h) via cache
- Rate limit: `throttle:leads` (10/min por IP)

Referências:

- Request: `app/Http/Requests/Landing/Leads/V1/LeadsRequest.php`
- Controller: `app/Http/Controllers/Api/Landing/Leads/V1/LeadsController.php`
- Rotas: `app/routes/api/v1.php`

### Analytics — Tracking (batch)

- **POST** `{{base_url}}/api/v1/analytics/track`
- Header opcional: `X-Tracking-Token` (depende de `TRACKING_REQUIRE_TOKEN`)

Payload (exemplo):

```json
{
  "client_id": "b5f1a6de-7b66-4c1a-8c4f-2b1e0d9b7c21",
  "session_id": "2e739f0d-9a7a-4b4e-b5f6-0a1b2c3d4e5f",
  "events": [
    {
      "event_id": "a3b2c1d0-1111-2222-3333-444455556666",
      "type": "page_view",
      "occurred_at": "2026-03-16T20:11:36+00:00",
      "page": "/landing",
      "props": { "section": "form" }
    }
  ]
}
```

Regras principais:

- `events`: array obrigatório (`min:1`, `max:10`)
- `events.*.type`: permitido por role (configurado em `config/analytics.php`)
- Bloqueio de campos extras em `events.*` e `events.*.props` via schema
- Rate limit: `throttle:tracking` (60/min por IP)

Referências:

- Middleware: `app/Http/Middleware/analytics/TrackingToken.php`
- Request: `app/Http/Requests/Tracking/TrackBatchRequest.php`
- Service: `app/Services/Analytics/V1/IngestBatchService.php`
- Config: `config/analytics.php`
- Rotas: `app/routes/api/v1.php`

### Outros

- **GET** `{{base_url}}/api/v1/landing/health` — healthcheck simples
- **GET** `{{base_url}}/api/v1/analytics/overview` — endpoint **mock** (ideação)

## Logs (dev)

- Arquivo: `storage/logs/laravel.log`
- Eventos úteis:
  - `leads.demo_requested`
  - `tracking.batch_received`
  - `tracking.batch_persisted`

LGPD:

- Em produção, evite logar PII (e-mail/telefone/IP) em texto claro. Prefira hash/masking.

## Troubleshooting

### 500 em todas rotas de API (cache database)

Se o `.env` tiver `CACHE_STORE=database` e não existir a tabela `cache`, o throttle vai quebrar e gerar 500.

Soluções:

- Criar a tabela: `php artisan cache:table && php artisan migrate`
- Ou trocar temporariamente para `CACHE_STORE=file` (dev)

### Vite manifest not found

Se aparecer `ViteManifestNotFoundException`, rode:

- `npm run dev` (gera `public/hot`) **ou**
- `npm run build` (gera `public/build/manifest.json`)

## Próximos passos (backlog)

- Persistência real do tracking
- Gestão de tokens (hash, rotação, revogação)
- Substituir `GET /analytics/overview` mock por implementação real

## Sobre o Laravel

Framework base: Laravel (https://laravel.com).
