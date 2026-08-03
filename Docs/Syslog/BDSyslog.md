# Sys Logs — Auditoria e Log de Sistema (STI)

## Contexto

Sistema de auditoria multi-tenant do RHDPeople, baseado em Single Table Inheritance (STI) via `tightenco/parental`. Registra ações de autenticação, contratos, licenças, administração de tenant e webhooks de gateway de pagamento, com garantia de imutabilidade (append-only).

**Conexão:** `conn_rhdpeople_geral`

---

## Schema

### `sys_logs`

| Coluna | Tipo | Nullable | Descrição |
|---|---|---|---|
| `id` | ulid PK | não | Ordenação temporal implícita, boa localidade de índice |
| `tenant_id` | uuid | sim | FK `companies.id`, null = ação global de `super_admin` |
| `user_id` | uuid | sim | FK `users.id`, null = ação de sistema/cron/webhook |
| `correlation_id` | uuid | sim | Agrupa múltiplos logs de um mesmo request/job/fluxo |
| `type` | string | não | Discriminador STI curto (não FQCN) |
| `action` | string | não | Ex: `login`, `failed_attempt`, `upgraded`, `revoked` |
| `severity` | enum | não | `debug` \| `info` \| `warning` \| `error` \| `critical` (default `info`) |
| `auditable_type` | string | sim | Polimorfismo — classe do model afetado |
| `auditable_id` | uuid | sim | Polimorfismo — id do model afetado |
| `payload` | json | sim | Old/new values ou contexto do erro |
| `ip_address` | varchar(45) | sim | Suporta IPv6 |
| `user_agent` | text | sim | |
| `created_at` | timestamp | não | `useCurrent()` |

**Índices:** `(tenant_id, type, created_at)`, `(auditable_type, auditable_id)`, `correlation_id`, `action`, `user_id`, `created_at`

**Imutabilidade:** triggers `BEFORE UPDATE`/`BEFORE DELETE` (SQLSTATE 45000) versionados na migration via `DB::unprepared`. Sem exceção — nenhum child tem permissão de update, incluindo `WebhookLog` (eventos de reprocessamento geram novo registro vinculado por `correlation_id`, em vez de atualizar o original).

### `sys_logs_archive`

Espelho de `sys_logs` + `archived_at` (`useCurrent()`). Sem FKs (histórico não pode quebrar se tenant/user for removido), sem triggers de imutabilidade.

---

## Catálogo de child types (STI)

| `type` | Model | `action` (exemplos) | `auditable_type` típico |
|---|---|---|---|
| `authentication` | `AuthenticationLog` | `login`, `logout`, `failed_attempt`, `password_reset` | `User` (opcional) |
| `contract` | `ContractLog` | `created`, `upgraded`, `downgraded`, `canceled`, `gateway_sync_failed` | `Contract` |
| `license` | `LicenseLog` | `issued`, `revoked`, `type_changed` | `License` |
| `tenant_admin` | `TenantAdminLog` | `blocked`, `unblocked`, `settings_updated` | `Company` |
| `webhook` | `WebhookLog` | `received`, `processed`, `failed`, `retried` | `Contract` |

---

## Camadas de defesa (imutabilidade — defense in depth)

1. **Model (`SysLog::boot()`)** — `static::updating()`/`static::deleting()` lançam `LogicException`. Falha rápido em dev/test com mensagem legível.
2. **Banco (triggers)** — `SIGNAL SQLSTATE '45000'` em `BEFORE UPDATE`/`BEFORE DELETE`. Cobre qualquer acesso que bypasse o Eloquent (raw query, outro serviço, etc).
3. **Contract (assinatura de interface)** — nenhum repository de `sys_logs` expõe `update`/`delete`. Quem programa contra a interface fisicamente não tem o método disponível.

## Escopo de tenant

`SysLog` usa `BelongsToTenant` — leitura restrita ao tenant do usuário autenticado (exceto `super_admin`, que vê tudo). Escrita permanece explícita via `Model::create()` nos Services (fase de aplicação), não é automática pelo trait.

---

## Camada de Repository

Um par Contract/Eloquent por child, **sem duplicar CRUD genérico** — cada um expõe apenas os métodos de domínio específicos daquele tipo. Nenhum método `update`/`delete` em nenhuma interface.

| Interface | Métodos específicos |
|---|---|
| `SysLogRepositoryInterface` | `paginateByTenant`, `getByType`, `getByCorrelationId`, `getByAuditable`, `getBySeverity`, `getByUser` |
| `AuthenticationLogRepositoryInterface` | `countFailedAttemptsSince`, `lastSuccessfulLogin` |
| `ContractLogRepositoryInterface` | `getByContract`, `lastEventForContract` |
| `LicenseLogRepositoryInterface` | `getByLicense` |
| `TenantAdminLogRepositoryInterface` | `getByTenant` |
| `WebhookLogRepositoryInterface` | `findByGatewayEventId` (idempotência), `getByCorrelationId` |

Todas com `create`/`findById` delegando para a Eloquent Model do próprio child — o roteamento por `type` é automático via `HasChildren`/`HasParent` do `tightenco/parental`.

---

## Fora de escopo (fase de aplicação — próxima sessão)

- **`$casts` tipados de `payload` por child** — não definido nesta sessão porque o shape do payload depende da `action` disparada pelo Service (ex: `ContractLog.upgraded` loga campos diferentes de `ContractLog.canceled`), não apenas do child/tipo. Além disso, definir o cast agora seria fixar um contrato de Service que ainda não existe. 
- Masking de PII no payload antes da escrita (documento, email)
- Job de archive (`sys_logs` → `sys_logs_archive`), incluindo estratégia de lote e frequência
- Form Requests / Controllers / Routes de consulta de auditoria

---

## Decisões arquiteturais registradas

- **ULID em vez de UUID v4** — `sys_logs.id` não é referenciado como FK em lugar nenhum; ULID dá ordenação temporal implícita e melhor localidade de índice em tabela de alto volume de insert.
- **Discriminador STI curto** (`authentication`, não `App\Models\AuthenticationLog`) — desacopla schema de namespace PHP; refactor de pastas não quebra dados históricos.
- **`WebhookLog` sem coluna `processed_at`** — mantém tabela 100% append-only sem exceção; estado de processamento é modelado como novo registro (`action: processed`) vinculado via `correlation_id`.
- **Sem `$correlation_id` como índice composto** — usado isoladamente ("todos os logs desta request"), sem cruzar com tenant/type.


## Casos de uso — exemplos de consumo (ilustrativo)

> Os exemplos abaixo mostram como o repository/model de `sys_logs` deve ser consumido a partir de um Service. Alguns partem de código real do projeto (marcado como tal); os demais são fictícios, criados apenas para fixar o padrão correto de uso da camada de banco fechada nesta sessão.

### 1. Registrar tentativa de login falha (`AuthenticationService` — código real)

Trecho atual, sem auditoria:
```php
public function authenticate(array $credentials): void
{
    if (! Auth::attempt($credentials)) {
        throw ValidationException::withMessages([
            'email' => ['As credenciais estão incorretas.'],
        ]);
    }
}
```

Com `AuthenticationLog` — o log entra **antes** do `throw`, senão o fluxo nunca chega lá:
```php
public function __construct(
    private AuthenticationLogRepositoryInterface $authLogRepository,
) {}

public function authenticate(array $credentials): void
{
    if (! Auth::attempt($credentials)) {
        $user = User::where('email', $credentials['email'])->first();

        $this->authLogRepository->create([
            'user_id'    => $user?->id, // pode ser null se o e-mail nem existir
            'action'     => 'failed_attempt',
            'severity'   => 'warning',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        throw ValidationException::withMessages([
            'email' => ['As credenciais estão incorretas.'],
        ]);
    }
}
```

### 2. Auditoria dentro de transação existente (`RegisterAndContractService` — código real)

O método já usa `DB::transaction()`. O log **precisa entrar dentro da mesma closure**, antes do
`return` — assim, se um passo posterior falhar e a transação for revertida, o log é revertido junto
(evita `ContractLog` órfão referenciando um contrato que nunca existiu de fato):

```php
$contract = $this->contractRepo->create([
    'tenant_id'       => $company->id,
    'start_date'      => Carbon::today(),
    'expiration_date' => Carbon::today()->addMonth(),
    'billing_cycle'   => 'monthly',
    'auto_renew'      => true,
]);

$this->contractLogRepository->create([
    'tenant_id'      => $company->id,
    'user_id'        => $user->id,
    'action'         => 'created',
    'severity'       => 'info',
    'auditable_type' => Contract::class,
    'auditable_id'   => $contract->id,
    'payload'        => [
        'new' => [
            'billing_cycle' => $contract->billing_cycle,
            'auto_renew'    => $contract->auto_renew,
        ],
    ],
]);

// ... resto do método (criação de plan/license) continua igual ...
```

**Por que isso importa:** auditoria fora da transação cria a possibilidade de log "fantasma" — evento
registrado, mas a operação que ele descreve nunca foi persistida.

### 3. Checar regra de negócio via repository (fictício — lockout de conta)

```php
public function attemptLogin(string $userId, string $password): bool
{
    $failedCount = $this->authLogRepository->countFailedAttemptsSince(
        $userId,
        now()->subMinutes(15),
    );

    if ($failedCount >= 5) {
        throw new AccountLockedException();
    }

    // ... valida senha ...
}
```
Nunca fazer `AuthenticationLog::where(...)->count()` direto no Service — sempre via repository,
mesmo em leitura simples. Mantém o Service testável (mock da interface) e a query centralizada.

### 4. Correlacionar múltiplos logs do mesmo fluxo (fictício — webhook de gateway)

```php
public function handleGatewayWebhook(array $webhookPayload): void
{
    $correlationId = (string) Str::uuid();

    $existing = $this->webhookLogRepository->findByGatewayEventId($webhookPayload['event_id']);
    if ($existing) {
        return; // idempotência — evento já processado
    }

    $this->webhookLogRepository->create([
        'correlation_id' => $correlationId,
        'action'         => 'received',
        'severity'       => 'info',
        'auditable_type' => Contract::class,
        'auditable_id'   => $webhookPayload['contract_id'],
        'payload'        => ['gateway_event_id' => $webhookPayload['event_id']],
    ]);

    // ... processa o webhook ...

    $this->webhookLogRepository->create([
        'correlation_id' => $correlationId, // mesmo correlation_id do evento 'received'
        'action'         => 'processed',
        'severity'       => 'info',
        'auditable_type' => Contract::class,
        'auditable_id'   => $webhookPayload['contract_id'],
    ]);
}
```
Histórico completo do fluxo reconstruído com `getByCorrelationId()`.

### 5. O que NÃO fazer

```php
// ERRADO — bypassa repository, acopla Service ao Eloquent diretamente
SysLog::create([...]);

// ERRADO — tentativa de update; estoura exception no model + trigger no banco
$log = SysLog::find($id);
$log->update(['severity' => 'critical']);

// ERRADO — usar repository genérico quando existe repository de child com método específico
$this->sysLogRepository->getByType('authentication', $tenantId); // funciona, mas...
$this->authenticationLogRepository->countFailedAttemptsSince($userId, $since); // ...isso é o certo
```

### Nota — padrão de correlação já em uso informal no código

`IngestBatchService::ingest` (módulo Analytics/Tracking) já usa o mesmo padrão que motivou
`correlation_id` em `sys_logs`: um `$traceId = Str::uuid()` único por batch, propagado em dois
`Log::info` (`tracking.batch_received` / `tracking.batch_persisted`). A diferença é que ali o log vai para arquivo (não estruturado, não consultável via SQL), enquanto em `sys_logs` o mesmo padrão fica em tabela relacional, correlacionável via `getByCorrelationId()`.

Esse módulo **não é candidato a migrar para `sys_logs`**: `tracking_events` vive em
`conn_rhdpeople_landingpage` (conexão separada), o `tenant_id` ali é um `client_id` de sessão
pública sem FK real para `companies`, e o volume de telemetria de LP não se encaixa no propósito
de auditoria de negócio da tabela.
