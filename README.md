# TruckPag API

## Descrição

O TruckPag API é uma API desenvolvida para um desafio de programação. Ele oferece um conjunto de funcionalidades robustas, incluindo integração com MongoDB Atlas, Elasticsearch para buscas otimizadas, Unit Tests e um sistema de alerta para falhas na sincronização de produtos. Para sincronização de dados foi utilizado o projeto Open Food Facts.

## Tecnologias Utilizadas

- **Linguagem:** PHP 8.3
- **Framework:** Laravel 12
- **Banco de Dados:** MongoDB Atlas
- **Busca Avançada:** Elasticsearch
- **Containerização:** Docker
- **Servidor Web:** Nginx

## Instalação e Uso

### **1. Clonar o repositório**

```sh
  git clone git@github.com:lucasledo/truckpagtest.git
  cd truckpagtest
```

### **2. Configurar o ambiente**

Crie um arquivo `.env` com base no `.env.example` e preencha as credenciais necessárias.

```sh
  cp .env.example .env
```

Edite o `.env` para configurar o banco de dados MongoDB Atlas, Elasticsearch, API TOKEN e Email Notification:

```env
DB_CONNECTION=mongodb
DB_HOST=seu_host_mongo_atlas
DB_PORT=27017
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

ELASTICSEARCH_HOST=http://localhost:9200

API_TOKEN=Generate Hash

EMAIL_NOTIFICATION=seuemail@test.com
```

### **3. Subir os containers com Docker**

```sh
  docker compose  up -d --build
```

Isso irá iniciar os containers do PHP, Nginx, MongoDB e Elasticsearch.

### **4. Importar Produtos**

```sh
    docker exec -it truckpagapi bash
```

E depois rode o comando para importar:

```sh
    php artisan app:import-products
```

### **5. Testar a API**

A API estará rodando em `http://localhost:8000`.

Para testar os endpoints documentados, utilize a documentação gerada com Scribe em `http://localhost:8000/docs`.

---

### **6. Teste Unit**

```sh
    docker exec -it truckpagapi bash
```

E depois rode o comando para testar:

```sh
    php artisan test --filter=ProductTest
```

## Challenge by Coodesh

This is a challenge by [Coodesh](https://coodesh.com).

