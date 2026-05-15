# Documentação do Módulo de Tracking (Analytics V1)

Este documento detalha o módulo de Tracking e Analytics da API RHDPeople. A solução foi desenhada para realizar a ingestão de eventos comportamentais e de telemetria do frontend (cliques, visualizações de página, submissões de formulário) de forma escalável e segura.

---

## 1. Visão Geral e Decisões Arquiteturais

A arquitetura do Tracking foi baseada em processamento em lote (*batching*) e validação estrita baseada em esquemas (Schema-driven).

**Principais Decisões Arquiteturais:**

* **Processamento em Lote (Batching):** O endpoint `POST /api/v1/analytics/track` aceita múltiplos eventos por requisição (limitado a 10). Isso reduz o *overhead* de rede e conexões com o banco de dados.
* **Domain-Driven Schema:** Os tipos de eventos e suas propriedades permitidas/obrigatórias são centralizados no domínio da aplicação através das classes `EventType` e `EventSchema`.
* **Idempotência e Descarte de Duplicatas:** O `IngestBatchService` filtra eventos duplicados na mesma requisição usando a chave `event_id`. O repositório (`insertIgnoringDuplicates`) garante que duplicatas não cheguem ao banco.
* **Segurança e Prevenção contra Abuso:**
* **Throttling:** O limite de requisições está configurado para 60 por minuto por IP no `AppServiceProvider`.
* **Mismatch de Sessão:** O payload impede o envio de eventos "falsos" cruzando o `session_id` enviado com o ID de sessão real do Laravel, a menos que o modo de depuração (`app.debug`) esteja ativo.
* **Validação de Tokens:** O acesso é controlado pelo middleware `TrackingToken`, que verifica o cabeçalho `X-Tracking-Token` e assinala *roles* com permissões específicas.



---

## 2. Como Implementar no Frontend

O frontend deve agrupar eventos e enviá-los em lotes para otimizar o uso da rede. Abaixo está um exemplo de como construir o serviço de disparo em JavaScript (Axios ou Fetch API).

### Estrutura do Payload Esperada

O payload exige identificadores de sessão/cliente e um array com no máximo 10 eventos.

```javascript
// Exemplo de payload a ser enviado no body da requisição
const payload = {
  client_id: "123e4567-e89b-12d3-a456-426614174000", // UUID do Cliente/Tenant
  session_id: "sua_sessao_laravel_aqui",             // Deve bater com a sessão do backend
  events: [
    {
      event_id: crypto.randomUUID(),                 // UUID Único do Evento
      type: "page_view",                             // Presente em EventType (ex: page_view, form_start)
      occurred_at: new Date().toISOString(),         // Formato ISO 8601 (Y-m-d\TH:i:sP)
      page: window.location.pathname,
      props: {                                       // Opcional, dependendo do tipo do evento
        section: "pricing"
      }
    }
  ]
}

```

### Exemplo de Integração (Fetch API)

```javascript
async function sendTrackingBatch(events) {
  if (events.length === 0) return;

  try {
    const response = await fetch('/api/v1/analytics/track', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Tracking-Token': 'seu_token_publico_ou_privado' // Necessário pelo TrackingToken Middleware
      },
      body: JSON.stringify({
        client_id: getClientId(),
        session_id: getSessionId(),
        events: events
      })
    });

    const result = await response.json();
    console.log('Batch enviado:', result.data.trace_id, result.data.accepted);
  } catch (error) {
    console.error('Falha ao enviar tracking', error);
  }
}

```

*Observação: Lembre-se de implementar um mecanismo no front-end para não enviar mais de 10 eventos por requisição (limite do `TrackBatchRequest`).*

---

## 3. Como Realizar Testes

### Testes Automatizados (PHPUnit / Pest)

1. **Bypass do Session ID:** Ao testar o `TrackBatchRequest`, garanta que você iniciou uma sessão (`Session::start()`) e que o `session_id` no payload corresponde a `session()->getId()`, ou ative `config(['app.debug' => true])` no teste.
2. **Mock de Repositórios:** Utilize o bind das interfaces definidas no `RepositoryServiceProvider` (`TrackingEventsRepositoryInterface` e `SessionBlockRepositoryInterface`) para não sujar o banco de dados.
3. **Teste de Throttling:** Simule 61 requisições seguidas da mesma origin IP para garantir que a API retorne *429 Too Many Requests* no endpoint de tracking.

### Testes Manuais (Postman/Insomnia)

* **Headers necessários:** `Accept: application/json` e `X-Tracking-Token: <valor>`.
* Crie um UUID válido online e o formato ISO8601 exato para o campo `occurred_at`.
* Envie propriedades "não permitidas" de propósito para validar a rejeição na regra `EventSchema::allowedPropsForType`.

---

## 4. Guia de Manutenção

Para evoluir a solução e adicionar novas métricas no futuro, siga este roteiro:

1. **Adicionar um Novo Evento:**
* Abra o arquivo `EventType.php` e adicione o novo *case* (ex: `case VideoPlay = 'video_play';`).


2. **Definir Propriedades (Opcional):**
* Se o evento exigir propriedades extras (ex: `video_id`), registre no `EventSchema.php` adicionando ao método `allowedProps` e/ou `requiredProps`.


3. **Ajuste de Limites:**
* Se o tráfego aumentar, altere o valor `Limit::perMinute(60)` para a rota de `tracking` no `AppServiceProvider.php`.
* Para processar lotes maiores, ajuste as regras `max:10` em `TrackBatchRequest.php`.



---

## 5. Impactos da Solução

* **Impacto Positivo em Banco de Dados:** A estratégia de realizar o filtro de duplicatas em memória antes da inserção (`$seenInBatch`) e utilizar um método `insertIgnoringDuplicates` evita a sobrecarga de *locks* no banco de dados.
* **Impacto no Logging:** O volume de logs crescerá rapidamente. A aplicação registra `tracking.batch_received` e `tracking.batch_persisted` contendo as estatísticas de cada lote processado.
* **Padronização de Resposta:** Todas as saídas herdam o `Response::api()`, retornando o `trace_id` e a contagem do status do *batch* (aceitos, inseridos, inválidos), o que garante alta rastreabilidade no frontend.

---

## 6. Pontos de Melhoria Futuros (Roadmap)

Embora a solução seja robusta para o cenário atual, o escalonamento massivo pode exigir os seguintes upgrades:

1. **Processamento Assíncrono (Filas/Jobs):** * *Melhoria:* Em vez de chamar o repositório diretamente na requisição síncrona dentro do `IngestBatchService`, o serviço pode simplesmente despachar os eventos validados para uma fila (ex: Redis/SQS). O *worker* absorverá o impacto de gravação no banco, permitindo que a API responda em milissegundos.
2. **Banco de Dados Analítico:**
* *Melhoria:* Atualmente usando repositórios padrão (possivelmente SQL), o modelo de ingestão alta sofrerá degradação em consultas de Dashboard (rota `/overview` mapeada em `v1.php`). Migrar a tabela base do `TrackingEventsRepositoryInterface` para um banco focado em OLAP, como ClickHouse ou Snowflake.


3. **Bufferization no Frontend:**
* *Melhoria:* Sugerir a implementação de um `ServiceWorker` ou uso de `navigator.sendBeacon()` no frontend para garantir que a fila de eventos seja entregue mesmo quando o usuário fechar a aba abruptamente.
