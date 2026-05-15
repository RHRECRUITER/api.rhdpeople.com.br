# Documentação do Módulo de Captcha e Segurança (Security V1)

Este documento detalha a solução de Captcha proprietária e o fluxo de proteção de endpoints sensíveis (como a criação de Leads) na API RHDPeople. A solução foi projetada para mitigar ataques de bots utilizando a biblioteca GD nativa do PHP, adotando um fluxo de "Token de Contexto" atômico.

---

## 1. Visão Geral e Decisões Arquiteturais

A arquitetura de proteção baseia-se em um fluxo de duas etapas (Two-Step Verification) para isolar a validação do desafio visual da regra de negócio principal.

**Principais Decisões Arquiteturais:**

* **Geração Nativa (Zero Dependencies):** O captcha é gerado via processamento de imagem em memória utilizando a biblioteca GD do PHP, o que remove a dependência de serviços de terceiros (como Google reCAPTCHA) e mantém o processamento sob total controle da infraestrutura.
* **Atomic Pull (Uso Único):** A validação do código do captcha consome o registro no cache imediatamente através do método `Cache::pull`, prevenindo ataques de repetição (Replay Attacks) com o mesmo desafio.
* **Token de Contexto:** Após acertar o captcha, o usuário não realiza a ação final imediatamente. Ele recebe um `validation_token` de 64 caracteres amarrado a um `context` específico (ex: `leads_landing`). Isso permite que um único serviço de captcha proteja múltiplos endpoints independentes.
* **Fingerprint e Vínculo de Sessão:** Para impedir que um atacante valide um captcha em uma máquina e use o token em outra, o middleware `CheckCaptchaToken` cruza o ID da Sessão ou um Hash de Fingerprint (IP + User Agent).
* **Throttling e Proteção Dupla:** Tanto a geração quanto a verificação do captcha possuem um limite rígido de 10 requisições por minuto por IP. O endpoint de Leads possui também regras adicionais, como o "Honeypot" (campo `website` que deve vir vazio) e validação de tempo de preenchimento do formulário (rejeita submissões menores que 2 segundos).

---

## 2. Como Implementar no Frontend

A implementação exige a orquestração de três requisições sequenciais. Abaixo está o fluxo de integração para a página de Leads.

### Passo 1: Solicitar o Desafio

Ao abrir o formulário, o frontend deve buscar a imagem.

```javascript
// GET /api/v1/security/captcha/generate
const response = await fetch('/api/v1/security/captcha/generate');
const data = await response.json();

// O frontend deve armazenar data.data.captcha_key
const captchaKey = data.data.captcha_key; 
// O frontend deve renderizar a imagem Base64 na tag <img src="...">
const captchaImage = data.data.captcha_image; 

```

### Passo 2: Validar o Desafio

O usuário digita os 5 caracteres da imagem e o frontend envia para validação.

```javascript
// POST /api/v1/security/captcha/verify
const verifyResponse = await fetch('/api/v1/security/captcha/verify', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
  body: JSON.stringify({
    captcha_key: captchaKey,
    captcha_code: "A3f9P", // Valor digitado pelo usuário
    context: "leads_landing" // Deve ser exatamente o contexto exigido na rota final
  })
});

const verifyData = await verifyResponse.json();
// Token para autorizar o cadastro do lead
const validationToken = verifyData.data.validation_token; 

```

### Passo 3: Consumir o Endpoint Final (Leads)

O frontend envia o payload do Lead incluindo o token gerado.

```javascript
// POST /api/v1/landing/leads
const leadResponse = await fetch('/api/v1/landing/leads', {
  method: 'POST',
  headers: { 
    'Content-Type': 'application/json', 
    'Accept': 'application/json',
    'X-Validation-Token': validationToken // Passado via header
  },
  body: JSON.stringify({
    name: "João Silva",
    company_name: "Tech Corp",
    corporate_email: "joao@techcorp.com",
    phone: "11999999999",
    number_employees: 50,
    objective: { time_tracking: true, payroll: false },
    form_started_at: 1715801700, // Timestamp gerado ao iniciar o formulário
    website: "" // Honeypot: OBRIGATÓRIO ficar vazio
  })
});

```

---

## 3. Como Realizar Testes

### Testes Automatizados (PHPUnit / Pest)

1. **Mock do Serviço (Geração):** Como o código é gerado aleatoriamente, utilize `Cache::shouldReceive('put')` nos testes de geração para garantir que o sistema está tentando persistir o UUID corretamente.
2. **Mock de Persistência (Validação):** Nos testes do `CaptchaController`, insira um par "chave/código" manualmente no Redis/Cache fake antes de disparar o teste para contornar a imprevisibilidade da GD.
3. **Teste de Fingerprint:** Simule requisições alterando o header `User-Agent` ou manipulando o IP da requisição (usando `$this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])`) ao consumir a rota `/leads` para confirmar se o `CheckCaptchaToken` rejeitará a chamada.

### Testes Manuais (Postman/Insomnia)

* **Geração:** Chame `GET /api/v1/security/captcha/generate`.
* **Validação:** Pegue o `captcha_key` e tente adivinhar o `captcha_code` usando `POST /api/v1/security/captcha/verify` com `context` preenchido como `leads_landing`.
* **Replay Attack:** Tente chamar a rota de verificação duas vezes seguidas com os mesmos dados. A segunda deve retornar `422 Validation failed` porque o token foi consumido.
* **Deduplicação de Leads:** Envie o mesmo Lead válido duas vezes dentro de 1 hora. O endpoint deverá retornar status `409 Conflict` (Lead already submitted recently).

---

## 4. Guia de Manutenção

* **Dificuldade da Imagem:** Para tornar a imagem mais difícil (ou fácil) para bots, altere a quantidade de linhas de ruído ou a quantidade de pontos (atual: 10 linhas e 4080 pontos de ruído) no método `generateCaptcha` dentro de `CaptchaService.php`.
* **Tamanho do Código:** O comprimento atual é de 5 caracteres. Pode ser alterado editando a constante `CAPTCHA_LENGTH = 5` no `CaptchaService`.
* **Tempos de Expiração:** * O código da imagem expira em **3 minutos**.
* O token de validação de contexto expira em **5 minutos**.



---

## 5. Impactos da Solução

* **Segurança Profunda:** A estratégia combinada (Captcha Visual + Validação de Tempo em `LeadsRequest` + Honeypot Invisível) constrói uma barreira formidável contra scripts automatizados, sem precisar pagar por APIs externas.
* **Impacto em Memória (RAM):** A manipulação de imagens usando `imagecreatetruecolor()` tem um custo pequeno de CPU e memória. Em picos de tráfego extremos (milhares de acessos simultâneos solicitando a imagem), isso poderá gerar um gargalo de processamento. O limitador de requisições `RateLimiter::for('captcha')` amortece esse risco.
* **Experiência do Usuário (UX):** Captchas baseados em imagem distorcida geram fricção inerente. Usuários mobile ou pessoas com deficiências visuais podem ter dificuldade de resolução.

---

## 6. Pontos de Melhoria Futuros (Roadmap)

1. **Acessibilidade (Audio Captcha):** Implementar uma variante de geração do desafio em formato de áudio para garantir conformidade com diretrizes WCAG (Web Content Accessibility Guidelines).
2. **Migração para Captcha Invisível / Proof of Work (PoW):** Caso o processamento da imagem (GD) se torne custoso ou obsoleto contra redes neurais avançadas de OCR, uma alternativa nativa excelente seria exigir um *Proof of Work* criptográfico silencioso no browser, minimizando a fricção humana.
3. **Limpeza Assíncrona de Arquivos:** No momento todo cache é auto-gerenciado, mas a longo prazo monitorar o tamanho de indexações órfãs do cache (`validated_action:*` e `captcha:*`) garantirá saúde do Redis/Memcached.
