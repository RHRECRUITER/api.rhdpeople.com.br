# Dashboard Analytics - Overview
Este documento tem a finalidade de descrever o funcionamento do módulo Dashboard Analytics. O objetivo do endpoint de overview é processar e disponibilizar as métricas consolidadas necessárias para a composição dos painéis e gráficos no front-end.
## Objetivo
Disponibilizar métricas e séries temporais para composição dos painéis (KPIs) e gráficos do Dashboard Analytics através de um endpoint REST.

## Métricas
Cada uma das métricas é disponibilizado a somatória e calculado a média.
    • Visualizações: Quantidade de visitantes na página.
    • Conversões: Quantidade de visitantes que realizaram a adesão.
    • Taxa de Rejeição: Percentual de visitantes que não aderiram.
    • Taxa de Conversão: Percentual de visitantes que aderiram 

## Como utilizar
As rotas da API foram construidas sob query parametrs, com isso, para seu uso basta apenas enviar os parametros requisitados pela url da rota, assim solicitando o processo.
As rotas da API utilizam Query Parameters para o recebimento dos parâmetros da requisição. Para o método GET, deve ser consumido informando o intervalo de datas na URL para que as métricas sejam calculadas dentro do período desejado.
### Exemplo
http://1.1.1.1/api/v1/analytics/dashboard/overview?start_date=2026-06-01&end_date=2026-06-30

---

## Rotas
Método - GET
- Endpoint: /api/v1/analytics/dashboard/overview
- Autenticação: Bearer Token. Rota protegida por middleware de autenticação do projeto.

## Parâmetros
| Nome | Formato | Tipo | Operação | Definição |
| :----------| :---------------  | :---------- | :------- | :-----------|
| start_date | Data(Ano-mês-dia) | Obrigatório | GET | Data inicial compondo intervalo do período a ser buscado. |
| end_date | Data(Ano-mês-dia) | Obrigatório | GET | Data final compondo intervalo do período a ser buscado. |


## Regras de Negócio
- O período consultado deve possuir data inicial e final válidas.
- Apenas os registros contidos dentro do intervalo informado são considerados.
- O intervalo de tempo padrão da consulta é de 7 dias após a data atual.
- As datas são manipuladas e tratadas internamente utilizando a biblioteca Carbon.

## Tratamentos de Erros a Dados Inválidos
- Datas inválidas: Caso as datas informadas na requisição sejam inválidas, a API retornará os dados baseados no intervalo de tempo padrão.
- Ausência de registros: Caso não existam registros no banco para o período solicitado, os totais retornados serão zerados.

---

## Retorno
A resposta segue o padrão de escopo de resposta global estabelecido no projeto, dividindo as informações em cards informativos e gráfico.
Cards (KPIs)
A resposta contém os seguintes indicadores para compor os painéis:
- Média de visualizações
- Média de conversões
- Média da taxa de rejeição
- Média da taxa de conversão
Gráfico
São disponibilizadas séries temporais (distribuídas em labels e datasets) contendo:
- Datas
- Visualizações
- Conversões
- Taxa de Conversão
- Taxa de Rejeição
### Exemplo de retorno bem-sucedido (200)
**JSON**
```
{
    "success": true,
    "message": "Dados obtidos com sucesso!",
    "data": {
        "KPIs": {
           "average_views": 125,
           "average_conversions": 35,
           "average_bounce_rates": 30,
           "average_conversion_rates": 31.67
        },
        "trends": {
           "labels": [
               "2026-06-01",
               "2026-06-03",
               "2026-06-06",
               "2026-06-08"
            ],
           "datasets": [
               {
                   "label": "Views",
                   "data": [100, 150, 100, 150]
               },
               {
                   "label": "Conversions",
                   "data": [50, 20, 50, 20]
               },
               {
                   "label": "Conversion Rates",
                   "data": [50, 13.33, 50, 13.33]
               },
               {
                   "label": "Bounce Rates",
                   "data": [25, 35, 25, 35]
               }
            ]
        }
    },
    "meta": {
        "timestamp": "2026-06-20T22:04:28+00:00"
    }
}
```
## Fluxo

OverviewController 
      │ (Validação)
      ▼
MetricsService 
      │ (Solicitação de dados)
      ▼
EloquentDailyMetricRepository 
      │ (Consulta na tabela daily_metrics)
      ▼
MetricsService 
      │ (Cálculos e Formatação)
      ▼
Cliente (JSON)

**Service – MetricsService**
Concentra a regra de negócio. Faz o processamento das métricas, os cálculos, monta a estrutura final para o dashboard e chama o Repository.

**Controller – OverviewController**
Recebe a chamada HTTP, valida os parâmetros da URL e repassa para o Service.

**Repository – EloquentDailyMetricRepository**
Fica responsável exclusivamente por fazer as consultas no banco de dados.

---

## Depedencias
- Todos os dados são originados e totalmente dependente da tabela “daily_metrics” presente no banco de dados.
- O acesso é restrito, sendo obrigatória a autenticação para consumir o endpoint.
- O tratamento de datas é realizado através da biblioteca Carbon. 
