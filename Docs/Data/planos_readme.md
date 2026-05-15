# Módulo de Planos e Assinaturas

## Visão Geral

O módulo de assinaturas controla tudo relacionado à contratação de planos, licenciamento de usuários e configuração de recursos disponíveis por tier. A premissa central é que **o acesso de um usuário ao sistema é governado pela licença, não por um status no model User**.

---

## Estrutura de Dados

### Configuração de Planos (dados administrativos)

Estas tabelas são populadas via seeders e raramente mudam. São elas que alimentam o configurador do front sem hardcode.

```
plan_tiers                  → os 4 planos disponíveis (Start, Growth, Pro, Enterprise)
employee_range_multipliers  → faixas de colaboradores com multiplicador de preço por tier
modules                     → catálogo de módulos funcionais
add_ons                     → catálogo de complementos avançados
plan_tier_modules           → quais módulos cada tier inclui ou permite comprar como extra
plan_tier_add_ons           → quais add-ons cada tier disponibiliza
```

### Contratação (dados por empresa)

Criados no momento em que o cliente finaliza a contratação.

```
contracts    → acordo comercial: vigência, ciclo de cobrança, gateway de pagamento
plans        → assinatura ativa: preços calculados e travados no momento da contratação
plan_modules → módulos selecionados no plano (incluso ou pago)
plan_add_ons → add-ons selecionados no plano
licenses     → vínculo entre usuários e o plano, com tipo de acesso
```

---

## Fluxo de Contratação

```
1. Admin configura os tiers, módulos e add-ons via seeders/painel
         ↓
2. Cliente acessa o configurador
   - Escolhe o tier          → plan_tiers
   - Escolhe a faixa         → employee_range_multipliers
   - Seleciona módulos       → plan_tier_modules (filtra o que está disponível)
   - Seleciona add-ons       → plan_tier_add_ons (filtra o que está disponível)
   - Escolhe ciclo           → monthly ou annual
         ↓
3. Backend calcula e persiste nesta ordem:
   contracts → plans → plan_modules → plan_add_ons
         ↓
4. Licença do Dono criada automaticamente (consome 1 vaga do range)
         ↓
5. Admin convida colaboradores via token (employee_invite_token)
         ↓
6. Colaborador aceita → user criado → license criada
```

---

## Fluxo de Acesso

O acesso ao sistema é 100% governado pela licença. Não existe coluna `status` no model `User`.

```
Requisição chega
      ↓
Middleware verifica license do usuário autenticado
      ↓
is_active = true  → acesso liberado conforme type_license
is_active = false → acesso negado
```

---

## Enums

### `TypeLicense`
Define o nível de acesso do usuário dentro do plano.

| Case | Valor | Descrição |
|---|---|---|
| `DONO` | `dono` | Titular do plano. Nunca pode ser desativado. Sempre conta como 1 licença. |
| `OPERACIONAL` | `operacional` | Acesso operacional completo ao sistema. |
| `COLABORADOR` | `colaborador` | Acesso restrito ao portal do colaborador. |

### `SubscriptionInterval`
Define o ciclo de cobrança do contrato.

| Case | Valor | Descrição |
|---|---|---|
| `MONTH` | `monthly` | Cobrança mensal sem desconto. |
| `YEAR` | `annual` | Cobrança anual com desconto definido por tier. |

---

## Helpers dos Models

### `PlanTier`

| Método | Descrição |
|---|---|
| `annualMonthlyPrice()` | Retorna o preço mensal equivalente com desconto anual aplicado. |
| `acceptsEmployeeCount(int $count)` | Verifica se o tier aceita um número de colaboradores. |

### `EmployeeRangeMultiplier`

| Método | Descrição |
|---|---|
| `containsCount(int $count)` | Verifica se um número de colaboradores se encaixa nesta faixa. |
| `applyTo(float $basePrice)` | Calcula o preço final aplicando o multiplicador ao preço base. |

### `Module`

| Método | Descrição |
|---|---|
| `isIncluded()` | Verifica se o módulo é incluso (sem custo extra). |
| `displayPrice()` | Retorna o preço formatado (`Incluso` ou `+R$ valor`). |

### `AddOn`

| Método | Descrição |
|---|---|
| `displayPrice()` | Retorna o preço formatado (`+R$ valor`). |

### `Contract`

| Método | Descrição |
|---|---|
| `isExpired()` | Verifica se o contrato está vencido. |
| `isDueSoon(int $days)` | Verifica se o contrato vence nos próximos N dias (default 30). |
| `isActive()` | Verifica se o contrato ainda está vigente. |

### `Plan`

| Método | Descrição |
|---|---|
| `isActive()` | Verifica se o plano está ativo. |
| `isTrial()` | Verifica se o plano está em trial. |
| `isSuspended()` | Verifica se o plano está suspenso. |
| `isCancelled()` | Verifica se o plano está cancelado. |
| `activeLicensesCount()` | Retorna o total de licenças ativas no plano. |
| `hasAvailableSlots()` | Verifica se o plano ainda tem vagas disponíveis. |
| `availableSlots()` | Retorna quantas vagas ainda estão disponíveis. `null` = ilimitado. |

### `PlanModule`

| Método | Descrição |
|---|---|
| `isIncluded()` | Verifica se o módulo foi incluso sem custo neste plano. |
| `isPaid()` | Verifica se o módulo foi contratado como extra pago. |

### `License`

| Método | Descrição |
|---|---|
| `isDono()` | Verifica se a licença pertence ao dono do plano. |
| `isOperacional()` | Verifica se a licença é operacional. |
| `isColaborador()` | Verifica se a licença é de colaborador. |
| `canBeDeactivated()` | Verifica se a licença pode ser desativada. Dono retorna `false`. |
| `status()` | Retorna o status inferido (`active` ou `inactive`) sem coluna no banco. |

---

## Gestão de Vagas

- O limite de usuários é definido pelo `max_employees` da faixa contratada (`employee_range_multipliers`)
- O dono sempre conta como 1 vaga — não pode ser desativado
- O sistema **não desativa licenças automaticamente** em caso de downgrade do plano. O tratamento deve ser feito por meio de services e lógica das regras de negócio
- Em downgrade, o backend bloqueia novas ativações e sinaliza quantas licenças precisam ser desativadas — o admin escolhe quais

---

## Seeders

Os seeders populam os dados base necessários para o configurador funcionar. Devem ser rodados nesta ordem:

```bash
php artisan db:seed
php artisan migrate:fresh --seed
```

| Seeder | Descrição |
|---|---|
| `PlanTierSeeder` | Os 4 planos com preços e descontos |
| `EmployeeRangeMultiplierSeeder` | Faixas de colaboradores por tier |
| `ModuleSeeder` | Catálogo de módulos com preços |
| `AddOnSeeder` | Catálogo de add-ons com preços |
| `PlanTierModuleSeeder` | Relação entre tiers e módulos |
| `PlanTierAddOnSeeder` | Relação entre tiers e add-ons |

---

## Modelagem de dados

---

### Tabela `plan_tiers` (Tipos de Plano)
Armazena os planos base disponíveis para contratação (Start, Growth, Pro, Enterprise). É a tabela que alimenta o **front** e será usado pelo administrativo da RH Labs. Qualquer ajuste comercial é feito aqui, sem necessidade de alteração no código. Isto permite também a criação e manipulação do a partir de um painel administrativo da RH. Pensei este também com a possibilidade de criar a tela de "simulação" de preços especiais / promocionais com tempo limitado, além dos tier que já existirem no plano de trabalho normal. Este plano customizado será um tier `custom_world_cup_2026`, por exemplo.

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| name | String | Não nulo, unique | Nome do plano (Start, Growth, Pro, Enterprise) |
| description | String | Nullable | Subtítulo exibido no card do plano |
| base_price | Decimal(10,2) | Não nulo | Preço mensal base |
| annual_discount_pct | Decimal(5,2) | Não nulo | % de desconto no plano anual |
| max_employees | Integer | Nullable | Null = ilimitado (Enterprise) |
| is_custom_pricing | Boolean | Não nulo, default false | Se true, preço é sob consulta |
| is_active | Boolean | Não nulo, default true | Se o plano está disponível para contratação |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- `plan_tiers` → `employee_range_multipliers` : 1:N — cada tier tem suas faixas de colaboradores
- `plan_tiers` → `plan_tier_modules` : 1:N — cada tier define quais módulos inclui ou permite comprar
- `plan_tiers` → `plan_tier_add_ons` : 1:N — cada tier define quais add-ons disponibiliza
- `plan_tiers` → `plans` : 1:N — um tier pode ser contratado por várias empresas

**Extração de dados:**
- `getAllActive` — Retorna todos os tiers com `is_active = true` para exibição no front
- `getWithModulesAndAddOns` — Retorna o tier com módulos, add-ons e faixas para montar o configurador

---

### Tabela `employee_range_multipliers` (Multiplicadores por Faixa de Colaboradores)
Define os multiplicadores de preço conforme a quantidade de colaboradores. É o que o protótipo exibe na etapa que configura o plano manual pro cliente — cada faixa tem um fator que multiplica o preço base do tier escolhido. Entenda que a faixa de preço do multiplicador pode mudar com o tempo, mas as condições dos clientes não necessariamente irão mudar seguindo este fluxo. Este método também dá autonomia na criação de faixas de preços especiais para cliente recorrentes ou sem multiplicador, que agreguem valor de outra forma. Isto também permite que sejam criados multiplicadores diferentes para níveis diferentes, ao colocarmos um mínimo ou um máximo nesta tabela, e por meio de lógica de negócio nos services, definir quando um multiplicador diferente é aplicado com base na quantidade. Veja que o multiplicador é obrigatoriamente vinculado a um Tier que pode ser ou não ser ativo, daando a administrador a oportunidade e a autonomia para criar ranges que não necessariamente serão exibidos no front, mas que estão ativos para planos em andamento.

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| plan_tier_id | UUID | FK → plan_tiers, não nulo | Tier ao qual a faixa pertence |
| range_label | String | Não nulo | Label exibido no front (ex: "26 a 100 colaboradores") |
| min_employees | Integer | Não nulo | Limite inferior da faixa |
| max_employees | Integer | Nullable | Null = sem limite superior |
| price_multiplier | Decimal(4,2) | Não nulo, default 1.0 | Multiplicador aplicado ao preço base |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- Planos base — N:1 — A faixa é obrigatoriamente vinculada a apenas um tier
- Planos contratados — 1:N — Uma faixa pode estar vinculada a vários planos contratados

**Extração de dados:**
- `getByPlanTier` — Retorna todas as faixas de um tier para montar o seletor da etapa 2
- `findByEmployeeCount` — Identifica a faixa correta dado um número de colaboradores informado

---

### Tabela `modules` (Módulos Funcionais)
Catálogo de módulos funcionais da plataforma. Pode estar incluso no tier ou ser contratado como extra pago. É o que alimenta a etapa do plano customizado dos módulos. Também existem as tabelas de relação que contem mais informações.

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| key | String | Não nulo, unique | Chave interna (ex: `payroll`, `time_tracking`) |
| label | String | Não nulo | Nome exibido no front (ex: "Folha de Pagamento") |
| extra_price | Decimal(10,2) | Nullable | Null = incluso no tier sem custo; valor = preço adicional |
| description | String | Nullable | Descrição exibida em tooltip ou detalhe |
| is_active | Boolean | Não nulo, default true | Se o módulo está disponível para seleção |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- `plan_tier_modules` — 1:N — Um módulo pode estar vinculado a vários tiers
- `plan_modules` — 1:N — Um módulo pode estar presente em vários planos contratados

**Extração de dados:**
- `getAllActive` — Retorna todos os módulos ativos para montar a etapa 3 do configurador
- `getIncludedByPlanTier` — Retorna os módulos inclusos em um tier específico
- `getAvailableByPlanTier` — Retorna os módulos disponíveis para contratação em um tier específico

---

### Tabela `add_ons` (Complementos Avançados)
Catálogo de itens opcionais de valor fixo que o cliente pode adicionar ao plano independentemente do tier. É o que alimenta a etapa do plano customizado dos addons.

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| key | String | Não nulo, unique | Chave interna (ex: `kyc_biometry`, `sso`) |
| label | String | Não nulo | Nome exibido no front (ex: "Assinatura digital jurídica") |
| price | Decimal(10,2) | Não nulo | Preço adicional fixo |
| description | String | Nullable | Descrição exibida no front |
| is_active | Boolean | Não nulo, default true | Se o add-on está disponível para seleção |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- `plan_tier_add_ons` — 1:N — Um add-on pode estar vinculado a vários tiers
- `plan_add_ons` — 1:N — Um add-on pode estar presente em vários planos contratados

**Extração de dados:**
- `getAllActive` — Retorna todos os add-ons ativos para montar a etapa 4 do configurador
- `getAvailableByPlanTier` — Retorna os add-ons disponíveis para um tier específico

---

### Tabela `plan_tier_modules` (Módulos por Tier — pivot)
Define quais módulos cada tier inclui ou permite contratar como extra. É a fonte de verdade para o configurador saber o que exibir como "Incluso" ou "+R$ valor" no final.

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| plan_tier_id | UUID | FK → plan_tiers, não nulo | Tier ao qual a regra se aplica |
| module_id | UUID | FK → modules, não nulo | Módulo referenciado |
| is_included | Boolean | Não nulo, default false | True = incluso no tier; false = extra pago |
| is_available | Boolean | Não nulo, default true | False = não aparece para este tier |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- Planos base — N:1 — O registro é obrigatoriamente vinculado a apenas um tier
- Módulos — N:1 — O registro é obrigatoriamente vinculado a apenas um módulo

---

### Tabela `plan_tier_add_ons` (Add-ons por Tier — pivot)
Define quais add-ons cada tier disponibiliza para contratação na etapa 4 do configurador.

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| plan_tier_id | UUID | FK → plan_tiers, não nulo | Tier ao qual a regra se aplica |
| add_on_id | UUID | FK → add_ons, não nulo | Add-on referenciado |
| is_available | Boolean | Não nulo, default true | False = não aparece para este tier |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- Planos base — N:1 — O registro é obrigatoriamente vinculado a apenas um tier
- Add-ons — N:1 — O registro é obrigatoriamente vinculado a apenas um add-on

---

### Tabela `contracts` (Contratos)
Registra o acordo comercial entre a empresa e a plataforma. Enquanto as licenças controlam *quem acessa e com qual permissão*, o contrato guarda *sob quais condições o plano foi fechado* — vigência, ciclo de cobrança e gateway de pagamento. É o registro jurídico-financeiro da assinatura, que muda raramente e precisa de rastreabilidade histórica. Considere esta parte um controle interno de faturamento para ser exibido para o cliente e para ter um mero controle interno da RHLabs. Por que não recomendo usar isso como relatório financeiro interno da RHLabs: Simples, esta tabela não tem a capacidade de fazer controle sobre taxas de serviço, iof e outros impostos aplicados durante a transação com o gateway de pagamento. Por ora, este deve ser considerado apenas para mostrar ao cliente o que ele pagou e quando, podendo ser usado para gerar a invoice. À equipe de Backend, deixei o `gateway_subscription_id` e `gateway_customer_id` para capturar informações do gateway de pagamento e reaproveitar em compras repetitivas, como endereço e forma de pagamento, sem que estas informações sejam responsabildades nossas (terceirizamos a responsabilidade com a LGPD, neste caso, para o gateway de pagamento)
> Obs: Com relação ao controle financeiro, deve-se pensar em um módulo específico para este controle interno, mas todo o caso, estes dados de contratos (que poderia ser mudado para `invoices` ou outro nome ao inves de `contracts`) podem ser aproveitados no futuro, pricipalmente as informações de gateway.

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| tenant_id | UUID | FK → companies, não nulo | Empresa titular do contrato |
| gateway_subscription_id | String | Nullable | ID da assinatura no gateway de pagamento |
| gateway_customer_id | String | Nullable | ID do cliente no gateway de pagamento |
| start_date | Date | Não nulo | Data de início do contrato |
| expiration_date | Date | Não nulo | Data de expiração |
| billing_cycle | Enum | Não nulo | `monthly` ou `annual` |
| auto_renew | Boolean | Não nulo, default true | Renovação automática ao vencer |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- Empresas — N:1 — O contrato é obrigatoriamente vinculado a uma empresa
- Planos contratados — 1:1 — O contrato é vinculado a apenas um plano ativo

**Extração de dados:**
- `filterAllExpired` — Contratos com `expiration_date` anterior à data atual
- `filterByBillingCycle` — Contratos filtrando pelo ciclo de cobrança
- `getDueSoon` — Contratos que vencem nos próximos N dias

---

### Tabela `plans` (Planos Contratados)
Representa a assinatura ativa de uma empresa. Armazena todas as escolhas feitas no configurador e os valores calculados e travados no momento da contratação. Veja que o plano é ligado a um tier (o tier customizado pelo cliente, especial ou o pré-pronto feito pela administração da RH Labs), o Tier guarda a referência dos módulos, addons, preços base e multiplicadores aplicados! **quando for criar o plano do cliente é esperado que, pela lógica, pegue as informações do tier selecionado, aplique as regras de negócio, faça os cálculos e armazene os DADOS BRUTOS**, caso contrário, pode afetar esta tabela como fonte de verdade para o contrato. Veja que o contrato não trata de **valores**, e sim de prazos e formas de pagamento, enquanto plano é enxuto e faz a relação entre todos os componentes de valor no plano. Este, deve ser **sempre o dado real da transação no dia que ela foi executada, SEM RECALCULO** - Ah, Matheus, mas o cliente pediu um ajuste. Simples, marca esse como cancelado e faz OUTRO.

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| tenant_id | UUID | FK → companies, não nulo | Empresa titular do plano |
| contract_id | UUID | FK → contracts, não nulo | Contrato vinculado |
| plan_tier_id | UUID | FK → plan_tiers, não nulo | Tier base escolhido |
| employee_range_id | UUID | FK → employee_range_multipliers, não nulo | Faixa de colaboradores escolhida |
| billing_cycle | Enum | Não nulo | `monthly` ou `annual` |
| price_base | Decimal(10,2) | Não nulo | Preço do tier base sem multiplicador |
| price_with_multiplier | Decimal(10,2) | Não nulo | Preço após aplicar o multiplicador de faixa |
| price_modules | Decimal(10,2) | Não nulo, default 0 | Soma dos módulos extras pagos |
| price_add_ons | Decimal(10,2) | Não nulo, default 0 | Soma dos add-ons selecionados |
| price_subtotal | Decimal(10,2) | Não nulo | Subtotal antes do desconto anual |
| price_discount | Decimal(10,2) | Não nulo, default 0 | Valor do desconto anual aplicado |
| price_total | Decimal(10,2) | Não nulo | Total final cobrado por mês |
| status | Enum | Não nulo, default trial | `active`, `suspended`, `cancelled`, `trial` |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- Empresas — N:1 — O plano é obrigatoriamente vinculado a uma empresa
- Contratos — 1:1 — O plano é obrigatoriamente vinculado a um contrato
- Tiers — N:1 — O plano referencia o tier base contratado
- Faixas — N:1 — O plano referencia a faixa de colaboradores contratada
- `plan_modules` — 1:N — Módulos selecionados neste plano
- `plan_add_ons` — 1:N — Add-ons selecionados neste plano
- `licenses` — 1:N — Licenças de usuários vinculadas a este plano

**Extração de dados:**
- `getFullSummary` — Plano com todos os relacionamentos para montar o resumo do configurador
- `getAllUsersInPlan` — Usuários vinculados ao plano via licenças ativas
- `getByStatus` — Filtra planos por status
- `getActivePlanByTenant` — Retorna o plano ativo (`active` ou `trial`) de uma empresa

---

### Tabela `plan_modules` (Módulos Selecionados por Plano — pivot)
**Registra quais módulos cada plano contratado possui, se são inclusos ou pagos, e o valor cobrado no momento — preservando histórico mesmo que o preço do módulo mude depois.**

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| plan_id | UUID | FK → plans, não nulo | Plano contratado |
| module_id | UUID | FK → modules, não nulo | Módulo selecionado |
| is_included | Boolean | Não nulo | True = incluso no tier; false = pago à parte |
| price_charged | Decimal(10,2) | Não nulo, default 0 | Valor cobrado (0 se incluso) |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- Planos contratados — N:1 — O registro é obrigatoriamente vinculado a um plano contratado
- Módulos — N:1 — O registro é obrigatoriamente vinculado a um módulo

---

### Tabela `plan_add_ons` (Add-ons Selecionados por Plano — pivot)
**Registra quais add-ons cada plano contratado possui e o valor cobrado no momento da contratação, preservando histórico de preço.**

| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| plan_id | UUID | FK → plans, não nulo | Plano contratado |
| add_on_id | UUID | FK → add_ons, não nulo | Add-on selecionado |
| price_charged | Decimal(10,2) | Não nulo | Valor cobrado no momento da contratação |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- Planos contratados — N:1 — O registro é obrigatoriamente vinculado a um plano contratado
- Add-ons — N:1 — O registro é obrigatoriamente vinculado a um add-on

---

### Tabela `licenses` (Licenças)
Autorização que define o conjunto de permissões e o nível de acesso do usuário à aplicação. É o intermediário entre planos e usuários — **o acesso ao sistema é governado inteiramente pela licença, não por um status no model User**. A licença é criada, desativada ou alterada conforme o time da empresa muda. Respostas: Sim, o middleware de auth vai ter que bater aqui e não precisa de services, pode chamar os *helpers programados para isto*. Pela lógica que foi passada para este dev, a conta só é criada SE o cliente pagar, mas como foi previsto lá em cima o "Trial", o fluxo deve seguir esta lógica:
1. Criou a conta: Gera uma licença de DONO.
2. Dono adiciona outras licenças que vão receber o "invite_token" ver a migration relacionada a isto
3. Ao abrir o invite token e se cadastrar sob o invite_token, no final do cadastro a licença é criada e vinculada ao usuário recém criado
4. Ao desativar uma licença, apagar uma licença ou qualquer coisa, não afeta a conta do usuário. Por que eu pensei nisto, porque se o funcionário sair de uma empresa para outra dentro do nosso nomínio, ele terá uma licença desativada, mas terá outra licença ativa, então ele poderá acessar a licença vinculada a empresa nova pelo **PLANO**. Então, na verdade, nenhum usuário além do dono está vinculado exclusivamente a empresa, somente o Dono. Olha que incrível. Isto também permite preservar o histórico do usuário naquela empresa a partir da sua licença. A ideia de "se quiser desativar a licença de um e passar para outro" pode ferir a necessidade da tabela de ser fonte de verdade para o usuário / sistema. Então, sempre que cadastrar um usuário novo, só estaremos criando licenças novas.
Obs, isto também permite ter uma tela onde o colaborador escolha qual empresa ele vai logar quando estiver entrando no ponto. tipo quando o cara tem dois empregos.


| Campo | Tipo | Restrição | Descrição |
|---|---|---|---|
| id | UUID | PK, não nulo | Identificador único |
| plan_id | UUID | FK → plans, não nulo | Plano ao qual a licença pertence |
| user_id | UUID | FK → users, não nulo | Usuário vinculado |
| type_license | Enum | Não nulo, default operacional | `dono`, `operacional`, `colaborador` |
| is_active | Boolean | Não nulo, default true | Se a licença está ativa |
| created_at | Timestamp | Não nulo | Criação do registro |
| updated_at | Timestamp | Não nulo | Última atualização |

**Relacionamentos:**
- Planos — N:1 — A licença é obrigatoriamente vinculada a um plano
- Usuários — N:1 — A licença é obrigatoriamente vinculada a um usuário

**Regras de negócio:**
- A licença do tipo `dono` nunca pode ser desativada
- O dono sempre conta como 1 vaga do range contratado
- Em downgrade de range, o sistema não desativa licenças automaticamente — bloqueia novas ativações e sinaliza quantas precisam ser desativadas, o admin escolhe quais

**Extração de dados:**
- `getByType` — Licenças de um plano filtradas por tipo
- `countByType` — Contagem de licenças por tipo em um plano
- `getAllUsersByTypeLicense` — Usuários ativos com um tipo de licença específico
- `countActiveByPlan` — Total de licenças ativas para validar o limite do range
- `getDonoByPlan` — Licença do dono de um plano
- `deactivate` — Desativa uma licença (bloqueado para o tipo `dono`)
- `activate` — Ativa uma licença

---

### Resumo dos Relacionamentos

```
plan_tiers ──────────────────── employee_range_multipliers  (1:N)
plan_tiers ──────────────────── plan_tier_modules           (1:N)
plan_tiers ──────────────────── plan_tier_add_ons           (1:N)
plan_tiers ──────────────────── plans                       (1:N)
modules ──────────────────────── plan_tier_modules          (1:N)
modules ──────────────────────── plan_modules               (1:N)
add_ons ──────────────────────── plan_tier_add_ons          (1:N)
add_ons ──────────────────────── plan_add_ons               (1:N)
companies ────────────────────── contracts                  (1:N)
companies ────────────────────── plans                      (1:N)
contracts ────────────────────── plans                      (1:1)
employee_range_multipliers ────── plans                     (1:N)
plans ────────────────────────── plan_modules               (1:N)
plans ────────────────────────── plan_add_ons               (1:N)
plans ────────────────────────── licenses                   (1:N)
licenses ─────────────────────── users                      (N:1)
```