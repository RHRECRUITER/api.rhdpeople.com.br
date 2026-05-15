# Documentação Core: Autenticação Stateful, CORS e Bootstrap (API V1)

Este documento detalha o núcleo de configuração da aplicação RHDPeople, abrangendo a estratégia de autenticação de Single Page Applications (SPA) via Laravel Sanctum, as políticas de Cross-Origin Resource Sharing (CORS) e o intercetador global de exceções da API.

---

## 1. Visão Geral e Decisões Arquiteturais

O núcleo da API foi desenhado para servir a uma SPA (ex: React, Vue), substituindo a autenticação baseada em tokens (Bearer Tokens) pela autenticação *Stateful* baseada em cookies HTTP-Only de sessão.

**Principais Decisões Arquiteturais:**

* **Autenticação Stateful (Sanctum):** O middleware `EnsureFrontendRequestsAreStateful` é adicionado antes de todas as rotas da API. Isto instrui o Laravel a gerir a autenticação através de cookies de sessão, proporcionando maior segurança contra ataques XSS (Cross-Site Scripting).
* **Contrato de Resposta (JSON):** A aplicação força a que todas as exceções ocorridas em `/api/*` retornem um contrato JSON estrito (`success`, `message`, `data`, `meta`), independentemente de ser um erro de validação (422) ou um erro crítico de servidor (500).
* **CORS Dinâmico e Restrito:** A configuração de CORS permite o envio de credenciais (`supports_credentials => true`), uma exigência técnica para que o frontend consiga enviar os cookies de sessão nas chamadas à API.
* **Segurança CSRF:** A proteção CSRF (Cross-Site Request Forgery) está ativada. É necessário que o frontend solicite primeiro um *cookie* CSRF na rota `sanctum/csrf-cookie` antes de realizar operações de escrita.

---

## 2. Configuração de Variáveis de Ambiente (Environment)

Para que a autenticação de cookies entre domínios funcione corretamente, as variáveis de ambiente devem estar milimetricamente alinhadas. O ficheiro `.env` possui uma secção dedicada à autenticação da SPA.

* **`FRONTEND_URL`**: O endereço base do cliente web.
* **`SANCTUM_STATEFUL_DOMAINS`**: Define quais domínios irão receber os cookies stateful do Sanctum. Em desenvolvimento, utiliza-se `localhost,127.0.0.1`. Em produção, deve-se colocar apenas o domínio da aplicação (ex: `app.rhdpeople.com.br`).
* **`CORS_ALLOWED_ORIGINS`**: Lista as origens permitidas pelo navegador. Regra geral, partilha o mesmo valor do `FRONTEND_URL`.
* **`SESSION_DOMAIN`**: Fundamental para partilha de sessão entre subdomínios. Em produção, definir com um ponto inicial (ex: `.rhdpeople.com.br`).

---

## 3. Tratamento Global de Exceções

O ficheiro de bootstrap `app.php` foi refatorado para intercetar e formatar as exceções nativas do Laravel em respostas de API consistentes.

* **Erros de Validação (422):** Retornam a lista detalhada de falhas no campo `errors`.
* **Erros de Segurança (401/403/419):** Exceções como o `Token Mismatch` (419) gerado pela falta do *cookie* CSRF são convertidos para o erro de sessão expirada (401).
* **Proteção de Dados Sensíveis (500/QueryException):** Em ambiente de produção (`APP_DEBUG=false`), os erros de base de dados e exceções genéricas (500) omitem a *stacktrace* para evitar o vazamento de detalhes estruturais, retornando apenas "Internal server error". Se o debug estiver ativo, a linha, o ficheiro e a classe da exceção são enviados no nó `extra`.

---

## 4. Como Implementar no Frontend (SPA)

Para consumir a API de forma autenticada, o cliente web (ex: Axios) necessita de ser configurado para trocar *cookies* e cabeçalhos com o backend.

### Configuração do Axios

```javascript
import axios from 'axios';

const api = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_URL, // Ex: http://localhost:8000
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    },
    // CRÍTICO: Permite o envio/recebimento dos cookies de Sessão e CSRF
    withCredentials: true 
});

```

### Fluxo de Login (CSRF Handshake)

Antes do primeiro POST (ex: submeter o login), a SPA deve garantir que possui o *cookie* CSRF.

```javascript
async function login(email, password) {
    // 1. Inicializa a proteção CSRF do Laravel Sanctum
    await api.get('/sanctum/csrf-cookie');

    // 2. Realiza a autenticação (os cookies CSRF e Session já irão atachados)
    const response = await api.post('/api/v1/auth/login', {
        email,
        password
    });

    console.log("Login efetuado com sucesso!", response.data);
}

```

---

## 5. Guia de Resolução de Problemas (Troubleshooting)

Ao lidar com CORS e Sessões, alguns erros clássicos podem ocorrer:

1. **Erro CORS (Network Error no browser):**
* *Causa:* O domínio da SPA não consta na variável `CORS_ALLOWED_ORIGINS`.
* *Solução:* Verifique no `.env` e confirme que não há uma barra final (`/`) na origem (ex: `http://localhost:3000`, e nunca `http://localhost:3000/`).


2. **Erro 419 (Session Expired / Token Mismatch):**
* *Causa:* O frontend não chamou o `/sanctum/csrf-cookie` antes do POST, ou o `SESSION_DOMAIN` está incorreto.
* *Solução:* Certifique-se de que a configuração `withCredentials` está ativa no Axios e que o domínio do frontend está listado em `SANCTUM_STATEFUL_DOMAINS`.


3. **Sessão a Desligar Prematuramente:**
* *Causa:* O condutor de sessões está focado no limite padrão.
* *Solução:* Verifique `SESSION_LIFETIME=120` (em minutos) no ficheiro `.env`. O driver atual é `database`, o que significa que o estado está gravado na base de dados.



---

## 6. Pontos de Melhoria Futuros (Roadmap)

1. **Transição de Sessão para Redis:** Atualmente, o driver de sessão (`SESSION_DRIVER`) está configurado para `database`. Ao escalar o projeto, a tabela de sessões pode gerar estrangulamento (I/O Bottleneck). Migrar para o Redis (`SESSION_DRIVER=redis`) reduzirá drasticamente o tempo de resposta.
2. **Rotação Automática de CSRF:** Pode ser implementado um mecanismo que atualize proativamente o *cookie* CSRF se o frontend permanecer com uma aba aberta inativa durante muito tempo, evitando falhas silenciosas na primeira requisição após inatividade.
