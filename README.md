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

## Segurança e CORS

Para garantir que a API seja acessada apenas pelos frontends autorizados, o projeto possui restrições de **CORS** e configuração de estado do **Sanctum**.

No seu arquivo `.env`, configure as seguintes variáveis (separadas por vírgula):

```env
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000,https://rhdpeople.com.br
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000,rhdpeople.com.br
```
Isso garante que navegadores bloqueiem chamadas de sites de terceiros para as rotas públicas (CORS) e orienta o Sanctum a exigir os cookies de proteção CSRF corretamente no painel autenticado (Stateful).

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
- **Headers**: 
  - `Origin` ou `Referer`: Deve corresponder aos domínios permitidos nas configurações de CORS.
  - `X-XSRF-TOKEN`: Obrigatório **apenas** se a requisição vier de um domínio *Stateful* (SPA). O valor é o token fornecido via cookie (`XSRF-TOKEN`) e geralmente é injetado automaticamente pelo Axios/Fetch.
  - `Content-Type`: `application/json`

Payload (exemplo):

```json
{
  "client_id": "b5f1a6de-7b66-4c1a-8c4f-2b1e0d9b7c21",
  "session_id": "2e739f0d-9a7a-4b4e-b5f6-0a1b2c3d4e5f",
  "events": [
    {
      "event_id": "33333333-3333-3333-3333-000000000003",
      "type": "page_view",
      "occurred_at": "2026-03-17T10:00:45+00:00",
      "page": "/landing",
      "props": { "section": "features" }
    },
  ]
}
```

Regras principais:

- `events`: array obrigatório (`min:1`, `max:10`)
- `events.*.type`: permitido por role (configurado em `config/analytics.php`)
- Bloqueio de campos extras em `events.*` e `events.*.props` via schema
- Rate limit: `throttle:tracking` (60/min por IP)

> **Nota de Arquitetura:** A ingestão agora possui **persistência real no banco de dados com garantia de idempotência**. O `IngestBatchService` valida, sanitiza e descarta silenciosamente eventos duplicados dentro do mesmo lote ou que já existam no banco de dados (validação pelo `event_id`).

Referências:

- Middleware: `throttle:tracking` (Rate Limit) e `EnsureFrontendRequestsAreStateful` (Global da API)
- Controller: `app/Http/Controllers/Api/Analytics/V1/Tracking/TrackController.php`
- Request: `app/Http/Requests/Tracking/TrackBatchRequest.php`
- Service: `app/Services/Analytics/V1/IngestBatchService.php`
- Config: `config/analytics.php`
- Rotas: `app/routes/api/v1.php`

### Autenticação / Auth (Conceitual / Dev)

> **⚠️ IMPORTANTE:** A API de login atual é **meramente conceitual**. Ela foi criada temporariamente para destravar o desenvolvimento do frontend e permitir a criação e o teste de endpoints que exigem autenticação (`auth:sanctum`). O fluxo real de identidade será definido e refatorado futuramente.

A autenticação utiliza o **Laravel Sanctum no modo Stateful (SPA)**. Isso significa que a API não emite um "Bearer Token" em texto para o frontend, mas sim estabelece uma **Sessão baseada em Cookies** (protegida contra XSS e CSRF).

- **POST** `{{base_url}}/api/v1/auth/login`
  - **Payload:** `{ "email": "usuario@teste.com", "password": "password" }`
  - **Ação:** Valida credenciais, gera novo ID de sessão (blindagem contra *Session Fixation*) e retorna os cookies `laravel_session` e `XSRF-TOKEN` no header da resposta.
- **GET** `{{base_url}}/api/v1/auth/me`
  - **Ação:** Retorna os dados do usuário logado atual. (Exige o envio dos cookies gerados no login).
- **POST** `{{base_url}}/api/v1/auth/logout`
  - **Ação:** Invalida a sessão atual e revoga os cookies de autenticação do navegador.

*Dica para testes via Postman/Insomnia:* Como o fluxo é Stateful, certifique-se de que o seu cliente HTTP está configurado para **armazenar e enviar cookies** automaticamente após bater na rota de `/login`.


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

- Gestão de tokens (hash, rotação, revogação)
- Substituir `GET /analytics/overview` mock por implementação real

## Sobre o Laravel

Framework base: Laravel (https://laravel.com).
