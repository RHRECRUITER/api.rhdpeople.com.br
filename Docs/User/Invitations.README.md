# Modulos de Convidados
Módulo destinado para os donos de empresa convidarem os colaboradores para uso da plataforma.

## Finalidade
Usuários autorizados por sua licença possam efetuar convites a usuários externos para cadastro na plataforma.

## Funcionalidades

* **Gerar Convites:** É feita a validação e geração do convite com um link, podendo ser compartilhado com a pessoa alvo.

* **Envio de e-mail:** Disponibilização do envio automático do convite para o convidado via e-mail.

* **Gerar cadastro de Usuário:** Validação do convite e criação do usuário após ser aceito.

## Fluxo de Trabalho

**InviteController → InvitationValidator → InviteService → InvitationGenerator → Invite**

InviteController: recebe as requisições das rotas e encaminha os dados para o fluxo correspondente.
InvitationValidator: realiza as validações necessárias para criação e utilização dos convites.
InviteService: concentra as operações relacionadas ao convite.
InvitationGenerator: responsável pela geração do convite e seus dados necessários.
Invite: representa o convite utilizado pelo sistema.

A requisição é recebida pela rota, passa pelas validações necessárias e, estando de acordo com as regras do módulo, é encaminhada para o serviço responsável pela operação.

## Rotas Disponíveis

### Rotas Invite

### createInviteRegister

Finaliza a criação do cadastro do usuário, utilizando os dados do convite previamente validado.

**Método:** POST

**Endpoint:** `/api/v1/invite/createInviteRegister`

**Autenticação:** Token do convite

**Requisitos:** Convite válido e não utilizado.

**Parâmetros:** Nome, senha e token.

**Payload de resposta:** Status do pedido.

---

### validateToken

Busca e valida o convite através do código token informado.

**Método:** GET

**Endpoint:** `/api/v1/invite/validateToken/{token}`

**Autenticação:** Não necessita de autenticação do usuário convidado.

**Requisitos:** Token de convite existente.

**Parâmetros:** Token.

**Payload de resposta:** Status do pedido.

---

### PreRegister

Gera o convite coletando os dados iniciais e enviando o convite ao usuário.

**Método:** POST

**Endpoint:** `/api/v1/invite/PreRegister`

**Autenticação:** Acesso via token

**Requisitos:** Usuário autenticado e com licença autorizada para realizar convites.

**Parâmetros:** E-mail, tipo de licença e data de expiração.

**Payload de resposta:** Status do pedido.

## Parâmetros

### PreRegister

| Parâmetro         | Tipo   | Obrigatório | Descrição                                      |
| ----------------- | ------ | ----------- | ---------------------------------------------- |
| `email`           | string | Sim         | E-mail do usuário que receberá o convite.      |
| `license_type`    | string | Sim         | Tipo de licença que será atribuída ao usuário. |
| `expiration_date` | date   | Sim         | Data de expiração do convite.                  |

### validateToken

| Parâmetro | Tipo   | Obrigatório | Descrição                                   |
| --------- | ------ | ----------- | ------------------------------------------- |
| `token`   | string | Sim         | Token utilizado para identificar o convite. |

### createInviteRegister

| Parâmetro  | Tipo   | Obrigatório | Descrição                                           |
| ---------- | ------ | ----------- | --------------------------------------------------- |
| `name`     | string | Sim         | Nome do usuário.                                    |
| `password` | string | Sim         | Senha que será utilizada para acessar a plataforma. |
| `token`    | string | Sim         | Token recebido através do convite.                  |

## Como utilizar

### Envio do convite

Para utilizar é necessário enviar os parâmetros pela rota informada anteriormente.

Exemplo:

`POST /api/v1/invite/PreRegister`

```json
{
    "email": "usuario@exemplo.com",
    "license_type": "collaborator",
    "expiration_date": "2026-09-10"
}
```

Após o envio, os dados e as permissões do usuário serão validados. Caso estejam corretos, o convite será criado e enviado para o e-mail informado.

### Validação do convite

O usuário convidado deverá acessar o link recebido. O token presente no link será utilizado para validar o convite.

Exemplo:

`GET /api/v1/invite/validateToken/{token}`

Caso o convite seja válido, o usuário poderá continuar o cadastro.

### Cadastro do usuário

Após a validação do convite, o usuário poderá finalizar seu cadastro através da rota:

`POST /api/v1/invite/createInviteRegister`

Exemplo:

```json
{
    "name": "João Silva",
    "password": "senha_do_usuario",
    "token": "token_do_convite"
}
```

O token será utilizado para localizar o convite e obter os dados relacionados a ele. Após as validações, o cadastro do usuário será realizado.

## Regras de negócio

* Usuários que não possuem a licença autorizada não podem efetuar convites.

* O usuário solicitante deve possuir licença disponível para realizar um novo convite.

* O usuário não poderá criar uma conta caso não exista um convite válido.

* O e-mail utilizado no cadastro deve ser o mesmo informado no convite.

* Convites que não atendam aos requisitos não podem ser aceitos.

* Convites que estejam fora da data de expiração não podem ser utilizados.

* Um convite só pode ser utilizado uma vez.

* O token deve estar relacionado a um convite existente.

## Validação de dados

Por isso, foi desenvolvido um segmento específico para esse fim denominado `InvitationValidator`, que trata os tipos de autorização e validações necessárias para este módulo.

### Requisitos para geração de convites

Para convidar novos funcionários, deve-se autenticar com um usuário que possua a licença de dono, além de possuir licenças disponíveis no plano adquirido.

Para esse fim, temos algumas funções:

* **verifyLicenseTypeRequester**

Verifica se o usuário que está realizando o convite possui o tipo de licença necessário.

* **verifyAvailableLicenseCount**

Verifica se existem licenças disponíveis para o novo convite.

* **checkInvitationRequirements**

Verifica os requisitos necessários para criação do convite.

### Convites válidos

Um convite não será aceito após seu período de expiração e só poderá ser utilizado uma vez.

Para garantir essas regras são utilizadas algumas funções:

* **verifyInvite**

Verifica se o convite existe e se pode ser utilizado.

* **verifyToken**

Verifica se o token informado pertence ao convite.

## Payload

As respostas seguem o padrão global utilizado pela aplicação.

### Sucesso

```json
{
    "status": 200,
    "message": "Success",
    "data": {
        "email": "usuario@exemplo.com",
        "status": "pending"
    }
}
```

### Erro de validação

```json
{
    "status": 422,
    "message": "Validation failed",
    "data": null
}
```
