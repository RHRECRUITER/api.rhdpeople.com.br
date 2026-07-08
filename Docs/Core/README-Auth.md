# 📖 Módulo de Autenticação e Registro - RHdPeople

> Documentação do módulo responsável pelo processo de autenticação, registro de empresas e gerenciamento inicial de licenças da plataforma **RHdPeople**.

---

# 📑 Índice

- [Visão Geral](#-1-visão-geral)
- [Arquitetura e Padrões Utilizados](#-2-arquitetura-e-padrões-utilizados)
- [Configuração do Ambiente](#-3-configuração-do-ambiente)
  - [Variáveis de Ambiente](#31-variáveis-de-ambiente-env)
  - [Estrutura de Migrations](#32-estrutura-de-migrations-customizada)
  - [Preparação Inicial](#33-preparação-inicial-seed--tinker)
- [Endpoints da API](#-4-endpoints-da-api-v1)
- [Testes](#-5-testes-e-limitações-do-postman)
- [Roadmap](#-6-roadmap)
- [Tecnologias Utilizadas](#-7-tecnologias-utilizadas)

---

# 📌 1. Visão Geral

O módulo de autenticação é responsável pelo gerenciamento da entrada de usuários e empresas dentro do ecossistema **RHdPeople**.

A  arquitetura foi desenvolvida para um ambiente **SaaS B2B Multi-Tenant**, onde a autorização não está vinculada diretamente ao usuário, mas sim ao estado de uma **Licença**, associada a um **Plano** e a uma **Empresa (Tenant)**.

A autenticação utiliza o **Laravel Sanctum** operando em modo **Stateful (Cookies + Sessões)**, proporcionando:

- Melhor desempenho (sem geração e armazenamento de JWT a cada login);
- Maior segurança para aplicações SPA;
- Aproveitamento da proteção CSRF nativa do Laravel;
- Menor complexidade na revogação de sessões.

---

# 🏗️ 2. Arquitetura e Padrões Utilizados

A implementação segue princípios de separação de responsabilidades e arquitetura em camadas.

## Controllers

Responsáveis apenas por:

- Receber requisições HTTP;
- Invocar Services;
- Retornar Responses.

Toda regra de negócio permanece fora dos Controllers.

---

## Form Requests

Utilizados para validação dos dados recebidos.

Exemplos:

- `RegisterRequest`
- `LoginRequest`

Benefícios:

- Centralização das validações;
- Código limpo;
- Reutilização.

---

## DTOs (Data Transfer Objects)

Exemplo:

- `RegisterOwnerDTO`

Responsáveis por transportar dados entre Controller e Service de forma tipada.

Benefícios:

- Maior organização;
- Segurança de tipos;
- Facilidade de manutenção.

---

## Services

Toda regra de negócio permanece nos Services.

Principais:

- `AuthenticationService`
- `RegisterAndContractService`

Responsabilidades:

- Registro de empresas;
- Criação de contratos;
- Licenciamento;
- Login;
- Logout;
- Transações de banco de dados.

---

## Model Helpers

O Model `User` possui métodos auxiliares para facilitar verificações de autorização.

Exemplos:

```php
$user->isCompanyAdmin();

$user->currentCompany();

$user->currentLicense();
```

---

# ⚙️ 3. Configuração do Ambiente

## 3.1 Variáveis de Ambiente (`.env`)

Para que o Laravel Sanctum funcione corretamente utilizando Cookies Stateful, utilize sempre o mesmo domínio.

> **Não misture `localhost` com `127.0.0.1`.**

```env
APP_URL=http://127.0.0.1:8000

FRONTEND_URL=http://127.0.0.1:8000

SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000

CORS_ALLOWED_ORIGINS=http://127.0.0.1:8000

SESSION_DOMAIN=127.0.0.1

SESSION_DRIVER=database
```

---

## 3.2 Estrutura de Migrations Customizada

As migrations foram organizadas em subpastas para facilitar a manutenção do projeto.

Execute utilizando:

```bash
php artisan migrate --path=/database/migrations/pasta_especifica
```

## 3.3 Preparação Inicial (Seed / Tinker)

O processo de registro exige previamente:

- um **Plano (Plan Tier)**;
- um **Range de Funcionários**.

Crie registros iniciais utilizando o Tinker.

### Abrindo o Tinker

```bash
php artisan tinker
```

### Criando um plano
```php
tier = App\Models\PlanTier::create([ 
    'name' => 'Start Teste', 
    'base_price' => 499.90, 
    'annual_discount_pct' => 10, 
    'is_custom_pricing' => false, 
    'is_active' => true 
    ]); 
 ```
 ---

 ### Criando um Employee Range
    
```php
    $range = App\Models\EmployeeRangeMultiplier::create([ 
        'plan_tier_id' => $tier->id, 
        'range_label' => '1 a 10', 
        'min_employees' => 1, 
        'price_multiplier' => 1.00 
        ]); 
        
        echo "\n--- USE ESTES UUIDS NO POSTMAN ---\n"; 
        echo "plan_tier_id: " . $tier->id . 
        "\n"; echo "employee_range_id: " . $range->id . 
        "\n"; echo "----------------------------------\n";
```

---

# 🌐 4. Endpoints da API (v1)

## Registro de Empresa

```
POST /api/v1/auth/register
```

### Descrição

Realiza em uma única transação:

- criação do usuário proprietário;
- criação da empresa;
- criação do contrato;
- criação do plano inicial;
- criação da licença.

### Limitação Atual

Ainda não existe integração com Gateway de Pagamentos.

---

## Login

```
POST /api/v1/auth/login
```

### Descrição

Autentica o usuário utilizando Laravel Sanctum Stateful.

### Retorno

- Dados do usuário;
- Roles;
- Empresa atual;
- Licença ativa.

---

## Logout

```
POST /api/v1/auth/logout
```

### Descrição

- Encerra a sessão;
- Regenera o token CSRF;
- Remove o acesso autenticado.

---

# 🧪 5. Testes e Limitações do Postman

## Problema

Como o projeto utiliza autenticação baseada em **Cookies + CSRF**, o Postman pode gerar erros falsos como:

- `401 Unauthorized`
- `419 Page Expired`

Isso ocorre devido à ausência do comportamento natural de um navegador na persistência dos cookies e do cabeçalho `X-XSRF-TOKEN`.

---

## Solução

Foi criada uma interface de testes em Blade que simula o comportamento de uma SPA.

### Acesso

```
http://127.0.0.1:8000/dev-tester
```

### Recursos

- Handshake CSRF;
- Login;
- Logout;
- Consumo de rotas protegidas;
- Envio de payloads JSON;
- Gerenciamento automático de cookies pelo navegador.

---

# 🚀 6. Roadmap

## 🔒 Aprimoramento de Autorização (Gates & Policies)

**Status**

> ⏳ Pendente

### Objetivo

Implementar Policies do Laravel para impedir vulnerabilidades como **IDOR (Insecure Direct Object Reference)**.

Exemplo:

Um usuário da Empresa A não poderá acessar registros da Empresa B manipulando IDs na URL.

---

## 👥 Gerenciamento de Papéis e Permissões (ACL)

**Status**

> ⏳ Pendente

### Objetivo

Criar endpoints para gerenciamento de grupos de permissões.

Exemplo:

```
/api/v1/company/roles
```

Permitirá:

- criação de cargos personalizados;
- permissões modulares;
- controle granular de acesso.

---

## ✉️ Convite de Colaboradores

**Status**

> ⏳ Pendente

### Objetivo

Criar fluxo de convites utilizando tokens temporários.

Exemplo:

```
/api/v1/company/invites
```

Fluxo esperado:

- envio de e-mail;
- criação do usuário;
- consumo de licença disponível;
- associação ao Tenant existente.

---

## 💼 Criação e Parametrização de Planos

**Status**

> ⏳ Pendente

### Objetivo

Criar CRUD administrativo para gerenciamento dos planos da plataforma.

Exemplo:

```
/api/v1/admin/plans
```

Permitirá:

- cadastrar novos planos;
- alterar preços;
- configurar multiplicadores;
- definir ranges de funcionários;
- eliminar dependência do Tinker.

---

## 💳 Gateway de Pagamentos

**Status**

> ⏳ Pendente

### Objetivo

Integrar o fluxo de pagamento ao processo de contratação.

Funcionalidades previstas:

- assinatura online;
- webhooks;
- renovação automática;
- suspensão por inadimplência;
- reativação automática após pagamento.

---

# 🛠️ 7. Tecnologias Utilizadas

| Tecnologia | Finalidade |
|------------|------------|
| Laravel 13 | Framework Backend |
| Laravel Sanctum | Autenticação Stateful |
| MySQL | Banco de Dados |
| PHP 8.3+ | Linguagem |
| DTO Pattern | Transporte de Dados |
| Service Layer | Regras de Negócio |
| Form Requests | Validação |
| UUID | Identificadores Globais |
| REST API | Comunicação Backend |
| Multi-Tenant | Isolamento entre Empresas |

---

# 📄 Licença

Este módulo faz parte do projeto **RHdPeople** e destina-se ao gerenciamento de autenticação, registro de empresas e controle inicial de licenciamento em ambiente SaaS Multi-Tenant.
