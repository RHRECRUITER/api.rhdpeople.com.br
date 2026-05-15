# Documentação do Módulo de Leads (Landing V1)

Este documento detalha o módulo de captação de Leads da aplicação RHDPeople. Este módulo é responsável por receber, sanitizar, validar e armazenar as solicitações de demonstração vindas da landing page pública.

Atualmente, o armazenamento definitivo em banco de dados está pendente devido a divergências de dados em homologação, operando temporariamente com um repositório em memória.

---

## 1. Visão Geral e Decisões Arquiteturais

A arquitetura foi pensada para blindar o endpoint público contra spans e submissões maliciosas, garantindo a qualidade dos dados antes que cheguem à camada de persistência.

**Principais Decisões Arquiteturais:**

* **Abstração de Repositório (Repository Pattern):** A persistência depende da interface `LeadsStore`. Atualmente, o sistema utiliza a implementação `InMemoryLeadsStore`, facilitando a futura troca para um banco de dados real sem alterar o controlador.
* **Deduplicação de Leads:** O `LeadsController` impede submissões repetidas através do cache. Um hash SHA-1 do e-mail corporativo é salvo, bloqueando novas tentativas com o mesmo e-mail por 1 hora e retornando o status HTTP 409.
* **Herança de Requests:** A classe `LeadsRequest` estende `PublicFormRequest`, que por sua vez estende `BaseFormRequest`, garantindo que a autorização padrão para endpoints públicos retorne sempre `true` de forma limpa.
* **Logging de Desenvolvimento:** As tentativas de submissão bem-sucedidas são registradas via `Log::info` para fins de monitoramento e debug em ambiente de desenvolvimento.

---

## 2. Tratamento e Validação de Dados

A classe `LeadsRequest` funciona como um firewall de dados, atuando em três frentes essenciais:

* **Sanitização Prévia (`prepareForValidation`):** Antes de validar, o sistema limpa os dados. Remove espaços duplicados em nomes, força o e-mail para letras minúsculas, extrai apenas os números do telefone e normaliza o array de objetivos (módulos), garantindo que todos existam no payload com o valor booleano adequado.
* **Barreiras Anti-Bot:**
* **Honeypot:** O campo `website` é exigido no payload (`present`), mas deve obrigatoriamente estar vazio (`max:0`). Bots preenchem todos os campos que encontram, caindo nesta armadilha.
* **Validação de Tempo Humano:** O sistema compara o `form_started_at` com o tempo atual do servidor. Rejeita preenchimentos rápidos demais (menos de 2 segundos) ou formulários expirados (mais de 2 horas abertos).


* **Regras de Negócio Estritas (`withValidator`):** Garante que nenhum módulo não-autorizado seja enviado e força a seleção de pelo menos um módulo válido (ex: `time_tracking`, `payroll`) para prosseguir.

---

## 3. Como Implementar no Frontend

O formulário deve gerar um timestamp no momento em que é renderizado e garantir que o campo `website` permaneça invisível para o usuário humano.

### Estrutura do Payload Esperada

```javascript
// O campo 'form_started_at' deve ser gerado assim que o form carregar
const formLoadTime = Math.floor(Date.now() / 1000); 

const payload = {
  name: "Maria Oliveira",
  company_name: "Tech Solutions Ltda",
  corporate_email: "maria@techsolutions.com.br",
  phone: "11988887777", // Pode conter pontuação, será limpo no backend
  number_employees: 150,
  objective: {
    time_tracking: true,
    payroll: true
    // Não enviar módulos inexistentes, o backend rejeitará
  },
  form_started_at: formLoadTime, // Ex: 1715801700
  website: "" // HONEYPOT: Ocultar no HTML e deixar vazio
}

```

### Exemplo de Chamada (Axios)

O envio deve ser atrelado à validação do Captcha (detalhado na documentação de Security).

```javascript
async function submitLead(payload, captchaToken) {
  try {
    const response = await axios.post('/api/v1/landing/leads', payload, {
      headers: {
        'X-Validation-Token': captchaToken // Gerado na rota de verificação de captcha
      }
    });
    console.log('Lead registrado:', response.data);
  } catch (error) {
    if (error.response?.status === 409) {
      alert("Você já solicitou uma demonstração recentemente.");
    }
  }
}

```

---

## 4. Como Realizar Testes

### Testes Automatizados (PHPUnit / Pest)

1. **Mock do Tempo:** Para testar a regra de "Submitted too fast", use ferramentas de manipulação de tempo do framework (ex: `this->travel(3)->seconds()`) entre a geração do `form_started_at` e a submissão.
2. **Teste de Deduplicação:** Envie o mesmo payload duas vezes consecutivas. A primeira deve retornar `201 Created` e a segunda deve retornar `409 Conflict`.
3. **Teste do Honeypot:** Envie o payload com o campo `website` contendo "[www.teste.com](https://www.teste.com)". O validador deve retornar erro 422 na chave `website`.

### Testes Manuais (Postman/Insomnia)

* **Gerar o Timestamp:** Utilize ferramentas do Postman (como `{{$timestamp}}`) para injetar o valor correto em `form_started_at`. Subtraia de 5 a 10 segundos desse timestamp para simular o tempo de digitação humana e não cair na regra de bloqueio.

---

## 5. Pontos de Melhoria Futuros (Roadmap)

Como a solução já possui uma arquitetura sólida e regras de negócio bem definidas, as próximas etapas naturais de evolução são:

1. **Integração Definitiva com Banco de Dados:** Assim que as divergências de dados em homologação forem resolvidas, criar a classe `EloquentLeadsStore` implementando a interface `LeadsStore` e alterar o bind no provedor de serviços (AppServiceProvider).
2. **Integração com CRM/Webhook:** Após persistir no banco, despachar um Job assíncrono para enviar os dados do Lead formatados diretamente para o sistema de CRM de vendas (ex: Hubspot, RD Station).
3. **Filtragem de E-mails Descartáveis:** Implementar uma regra no `LeadsRequest` que rejeite domínios de e-mail gratuitos ou temporários (ex: gmail.com, mailinator.com), forçando o preenchimento apenas com e-mails corporativos reais.
