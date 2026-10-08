# 🚀 Workflow, CI/CD e Qualidade de Código

Este documento estabelece os padrões de versionamento, estruturação de repositório, integração contínua (CI/CD) e as diretrizes para gestão de qualidade de código (débito técnico) da API RHdPeople.

---

## 🌳 1. Fluxo de Git (Git Flow)

Adotamos um modelo adaptado baseado em ramos permanentes e ramos de trabalho curtos. Ninguém escreve diretamente nos ramos permanentes.

### Branchs Permanentes
*   `main`: Código de produção. Cada commit aqui é uma versão publicável, com tag (ex: `v0.3.0`).
*   `staging`: Código em homologação para validação de QA e produto antes de ir para produção.
*   `develop`: Integração da equipe. É uma branch padrão do repositório onde o trabalho validado se junta.

### Branchs de Trabalho (Vida Curta)
As branchs de trabalho nascem sempre a partir da `develop` e o nome deve seguir o formato `tipo/descricao-curta` em minúsculas e separados por hífen.
*   `feat/*`: Funcionalidade ou comportamento novo.
*   `fix/*`: Correção de defeito que ainda não está em produção.
*   `hotfix/*`: Correção urgente do que está em produção (nasce da `main` e volta para a `main`, depois é propagada para as restantes).
*   `chore/*`, `ci/*`, `refactor/*`, `test/*`, `docs/*`: Atualizações estruturais, configuração, testes ou documentação.

---

## ✍️ 2. Padrão de Commits

Utilizamos o padrão **Conventional Commits 1.0.0** para gerar listas de mudanças rastreáveis.
Formato exigido: `tipo(escopo): descrição curta no imperativo, sem ponto final`.

**Exemplos aceites:**
*   `feat(leads): grava empresa e faixa de funcionarios`
*   `fix(convite): corrige nome do metodo na rota`
*   `ci(api): adiciona workflow com mysql e redis`

---

## 🔀 3. Pull Requests (PRs) e Proteção

Todo o código só entra na `develop` através de um Pull Request com CI verde e revisão.

*   **Tamanho:** O PR deve ser focado numa única tarefa.
*   **Modelo de PR:** O repositório utiliza o ficheiro `.github/pull_request_template.md` como checklist de evidências.
*   **Codereview:** Exige aprovação de donos por área.

---

## 🤖 4. Integração Contínua (CI/CD GitHub Actions)

Toda a abertura ou atualização de Pull Request dispara o nosso workflow `ci.yml`. O CI sobe um ambiente Ubuntu efêmero no GitHub e executa:

1.  **Serviços Locais:** Sobe contentores Docker descartáveis do MySQL 8.0 e Redis.
2.  **Segurança:** Audita falhas e CVEs com `composer audit`.
3.  **Formatação (Pint):** Roda `./vendor/bin/pint --test` para garantir que o código segue o padrão estético do repositório.
4.  **Análise Estática (PHPStan):** Roda o Larastan no **Nível 5** para caçar erros lógicos e de tipagem (ver secção de Débito Técnico abaixo).
5.  **Testes:** Executa o `php artisan test` validando as integrações no banco de dados real.

Se algum passo falhar, o PR será bloqueado.

---

##  debt 5. Gestão de Dívida Técnica e Exceções Atuais

Para conseguirmos travar novos problemas de código sem paralisar o desenvolvimento da equipe com o legado, adotámos as seguintes estratégias de contenção:

### 5.1 PHPStan Baseline (Código Legado)
Ativámos o Larastan no **Nível 5**. No entanto, para não quebrar a esteira com os erros antigos do sistema, gerámos um ficheiro de baseline (`phpstan-baseline.neon`).
*   **O que isto significa:** A baseline é uma "print" de todos os erros de tipagem/lógica atuais(DATA 07/10/2026). O CI ignora estes erros conhecidos, mas **quebrará imediatamente** se qualquer código novo introduzir um erro ou se um erro legado for alterado de forma incorreta.
*   **Objetivo:** Eliminar a introdução de novos bugs e ir limpando a baseline gradualmente.

### 5.2 Testes Unitários Incompletos (Bypass Temporário)
Atualmente, alguns testes de integração (Analytics e AuditLogs) esbarram em erros `401 Unauthenticated` devido à exigência de tokens Sanctum/Sessões Stateful que não estão sendo corretamente injetados nesses testes antigos.
*   **Abordagem:** Marcámos estes testes explicitamente com `$this->markTestIncomplete('Pendente ajuste de autenticação 401.')`.
*   **O que isto significa:** O PHPUnit ignora a falha momentaneamente (exibindo como aviso amarelo), permitindo que o CI da equipe passe verde para o resto da aplicação. A equipe deve corrigir os headers de autenticação e remover o `markTestIncomplete` no futuro.