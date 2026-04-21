RHdPeople — API Base


Projeto backend em Laravel 11 para suporte ao produto RHdPeople.

📦 Módulos Principais
Security (v1): Geração e validação atômica de desafios visuais (Captcha)
Landing / Leads (v1): Captação segura de solicitações de demonstração B2B
Analytics / Tracking (v1): Ingestão em lote (batch) de eventos do frontend
Auth (v1): Gestão de identidade e sessões Stateful

⚠️ Observação de Arquitetura:
As rotas não ficam em routes/.
Elas seguem um padrão modular e são carregadas a partir de app/routes/* (ver bootstrap/app.php).

🛠 Requisitos
PHP ^8.2 (com extensão GD habilitada)
Composer
Node.js + npm (Vite / Tailwind)
Redis (recomendado para cache, filas e rate limiting)
MySQL (opcional)
🚀 Como executar (dev)
1) Instalação
composer install
cp .env.develop.example .env
php artisan key:generate
2) Banco de Dados e Cache
php artisan cache:table
php artisan queue:table
php artisan queue:failed-table
php artisan queue:batches-table
php artisan migrate
3) Subir backend
php artisan serve
🛡 Arquitetura de Segurança (V1)

O RHdPeople adota uma postura rigorosa contra bots/spam.

🔐 CORS e Sanctum (Stateful)
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000,https://rhdpeople.com.br
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000,rhdpeople.com.br
📄 Formulários Públicos
Honeypot (website): Campo invisível para capturar bots
Time-trap (form_started_at): Bloqueia envio rápido demais
Captcha Atômico:
Gerado via GD em memória
Sem uso de disco
Validado com Cache::pull (uso único)
Token de Contexto:
Vinculado à sessão
Evita reutilização em outros endpoints
📡 Endpoints (v1)
Base URL: {{base_url}}/api/v1
📦 Padrão de Resposta (JSON)
{
  "success": true,
  "message": "OK",
  "data": {},
  "meta": {
    "timestamp": "2026-04-20T00:00:00+00:00"
  }
}
🔐 1. Security — Captcha
Gerar captcha
GET /security/captcha/generate
Retorna imagem + chave do desafio
Rate limit: throttle:captcha (10/min)
Validar captcha
POST /security/captcha/verify
Payload
{
  "captcha_key": "uuid-v7-gerado-no-get",
  "captcha_code": "k9Z2A",
  "context": "leads_landing"
}
Sucesso
{
  "validation_token": "token_alfanumerico_de_64_chars"
}
🧲 2. Landing — Leads
POST /landing/leads
Headers
CSRF Token (se Stateful)
Middleware
captcha.check:leads_landing
Payload
{
  "name": "João Silva",
  "company_name": "Tech Solutions",
  "corporate_email": "joao@tech.com",
  "phone": "11999999999",
  "number_employees": 50,
  "objective": {
    "time_tracking": true,
    "payroll": true
  },
  "form_started_at": 1710280000,
  "website": "",
  "validation_token": "token_obtido_na_verificacao_do_captcha"
}
📌 Regras principais
objective: Apenas módulos válidos
validation_token: Uso único (Redis) → reutilização = 403
Deduplicação por e-mail (TTL ~1h)
📊 3. Analytics — Tracking (Batch)
POST /analytics/track
Ingestão com idempotência (descarta duplicados)
Rate limit: throttle:tracking (60/min)
Payload
{
  "client_id": "b5f1a6de-7b66-4c1a-8c4f-2b1e0d9b7c21",
  "session_id": "2e739f0d-9a7a-4b4e-b5f6-0a1b2c3d4e5f",
  "events": [
    {
      "event_id": "33333333-3333-3333-3333-000000000003",
      "type": "page_view",
      "occurred_at": "2026-04-20T10:00:45+00:00",
      "page": "/landing",
      "props": {
        "section": "features"
      }
    }
  ]
}
🔑 4. Auth (Sanctum Stateful)

⚠️ Em ideação (fluxo conceitual)

POST /auth/login → Gera nova sessão (anti session fixation)
GET /auth/me → Dados do usuário
POST /auth/logout → Revoga sessão
📜 Logs e LGPD (Dev)
Arquivo: storage/logs/laravel.log
Monitoramento
leads.demo_requested
tracking.batch_received
tracking.batch_persisted
🔒 LGPD
Data Masking / Hashing para PII nos logs
Banco de dados = única fonte persistente
🛠 Troubleshooting
1. Erro 500 em todas as rotas

Se usar:

CACHE_STORE=database

E não tiver tabela → middleware throttle quebra.

Solução:

php artisan cache:table
php artisan migrate

ou use:

CACHE_STORE=file
2. Vite Manifest Not Found
npm run dev

ou

npm run build
3. GD Library Missing

Captcha depende da extensão GD.

Solução:
Ativar no php.ini

🚧 Projetado e Desenvolvido para RHdPeople
