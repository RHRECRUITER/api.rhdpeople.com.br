# 🚀 RHdPeople

O **RHdPeople** é uma plataforma backend robusta desenvolvida em **Laravel 13** (Experimental/Latest). O sistema atua como o núcleo de API para o ecossistema RHdPeople, oferecendo serviços avançados de autenticação *stateful* para SPA, proteção contra bots via captcha atômico, ingestão escalável de eventos (tracking), gestão de leads B2B e um complexo motor de assinaturas e licenciamento de usuários.

A arquitetura foi desenhada com foco em modularidade, separando de forma clara as responsabilidades de segurança, análise de dados, conversão e regras de negócio.

---

## 📚 Sumário da Documentação Modular

Para entender a fundo as decisões arquiteturais, fluxos de dados, regras de negócio e como realizar testes em cada contexto, consulte as documentações específicas de cada módulo:

1. 🔐 **[Core & Autenticação (Sanctum, CORS, Bootstrap)](/Docs/Core/README-Core-Stateful-Auth.md)**
2. 🛡️ **[Segurança (Captcha Atômico e Validação de Contexto)](/Docs/Security/Captcha.README.md)**
3. 📊 **[Tracking e Analytics (Ingestão de Eventos em Lote)](/Docs/Tracking/Tracking.README.md)**
4. 🧲 **[Captação de Leads (Landing Page)](/Docs/Landing/Lead.README.md)**
5. 💳 **[Planos e Assinaturas (Tiers, Licenças e Contratos)](/Docs/Data/planos_readme.md)**

---

## 🛠️ Requisitos de Infraestrutura

* **PHP:** `^8.3` ou superior (Extensão **GD** é obrigatória para a geração de Captchas).
* **Base de Dados:** MySQL 8.0 (O sistema utiliza múltiplas conexões para isolamento de dados: `geral` e `landingpage`).
* **Cache e Filas:** Redis (Altamente recomendado para gerenciar Throttling e Cache de uso único do Captcha).
* **Runtime:** Docker com Laravel Sail (Pré-configurado para um ambiente isolado).

---

## ⚙️ Guia de Instalação (Desenvolvimento)

### 1. Clonagem e Dependências

```bash
composer install

```

### 2. Configuração de Variáveis de Ambiente

O projeto possui diferentes arquivos de configuração de base dependendo do ambiente (`.env.develop.example`, `.env.sail.example`, `.env.production.example`). Para desenvolvimento local, copie o arquivo de desenvolvimento:

```bash
cp .env.develop.example .env
php artisan key:generate

```

Atenção: Configure corretamente as variáveis `FRONTEND_URL`, `SANCTUM_STATEFUL_DOMAINS` e `CORS_ALLOWED_ORIGINS` para que a comunicação Stateful com a SPA funcione corretamente.

### 3. Banco de Dados e Cache

Prepare as tabelas base para o ecossistema do Laravel:

```bash
php artisan cache:table
php artisan queue:table
php artisan queue:failed-table
php artisan queue:batches-table
php artisan migrate --seed

```

*(Nota: O parâmetro `--seed` é crucial para popular as configurações base de Planos, Tiers e Módulos).*

### 4. Inicialização

Suba o servidor embutido ou utilize o Laravel Sail:

```bash
php artisan serve

```

---

## 🛡️ Principais Pilares Arquiteturais

* 
**Autenticação Stateful para SPA:** Utiliza cookies HTTP-Only no lugar de Bearer Tokens tradicionais, mitigando riscos de roubo de credenciais via XSS.


* **Defesa em Camadas contra Bots:** Os formulários públicos utilizam *Honeypots* invisíveis, validação de tempo mínimo de preenchimento e um sistema de Captcha visual de processamento em memória, consumido de forma atômica (`Cache::pull`).
* **Ingestão Otimizada (Tracking):** Endpoint de telemetria que recebe eventos em lote (batching) e garante idempotência, mitigando sobrecarga no banco de dados.
* **Licenciamento Centralizado:** O acesso de um usuário ao sistema não é medido por uma coluna estática `status`, mas sim validado dinamicamente pela **Licença** vinculada ao Plano e Contrato atual da empresa.

---

## 🗺️ Roadmap / Próximos Passos

O desenvolvimento do **RHdPeople API** segue em ritmo acelerado. As próximas entregas focarão na consolidação do acesso, armazenamento definitivo de métricas e rastreabilidade administrativa:

* [ ] **Módulo de Autenticação (Auth API):**
* Criação completa dos endpoints de `login`, `logout` e recuperação dos dados da sessão (`/me`).
* Implementação do sistema de **Roles de Acesso**, diferenciando estritamente os perfis (Dono, Operacional e Colaborador) baseados nas licenças ativas.


* [ ] **Integração de Leads (Database):**
* Substituição do atual `InMemoryLeadsStore` pela persistência real via Eloquent no banco de dados da `landingpage`, ajustando divergências de esquema.


* [ ] **Consolidação de Analytics:**
* Criação de um endpoint `GET` (ex: `/api/v1/analytics/overview`) encarregado de agregar, calcular e retornar os dados consolidados do tracking (visualizações, conversões, drop-off) para os dashboards do frontend.


* [ ] **Sistema de Auditoria e Logs:**
* Desenvolvimento de um módulo interno para registro histórico de ações do sistema (System Logs).
* Criação de endpoint para consulta administrativa com filtros avançados (por período de datas, todo o histórico, categorias de erro/ação e paginação).



---

**🚧 Projetado e Desenvolvido para RHdPeople**
