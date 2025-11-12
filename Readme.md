# 🚀 Guia de Execução do Projeto

Este guia explica como configurar e executar o **back-end** e o **front-end** do projeto.

## 🧱 Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:

- [Node.js](https://nodejs.org/) (versão 16 ou superior)  
- [npm](https://www.npmjs.com/) (instalado junto com o Node.js)  
- [PHP](https://www.php.net/) (versão 8.0 ou superior)  
- [Composer](https://getcomposer.org/)  
- Um gerenciador de banco de dados (como **DBeaver** ou **Beekeeper Studio**)  
- Servidor Postgre em execução  

---

## 🧭 Passo a Passo Completo

1. Abra o terminal e entre na pasta do **back-end**:
   ```bash
   cd backend
   ```

2. Instale as dependências do back-end:
   ```bash
   composer install
   ```

3. Inicie o servidor local do Laravel:
   ```bash
   php artisan serve
   ```
   O back-end será executado na porta indicada no terminal, geralmente:
   ```
   http://localhost:8000
   ```

4. Abra outro terminal (ou uma nova aba) e entre na pasta do **front-end**:
   ```bash
   cd frontend
   ```

5. Instale as dependências do front-end:
   ```bash
   npm install
   ```

6. Inicie o servidor de desenvolvimento do front-end:
   ```bash
   npm run dev
   ```
   O front-end será executado na porta exibida no terminal, geralmente:
   ```
   http://localhost:5173
   ```

7. Localize o arquivo `database.sql` dentro da pasta do **back-end**.

8. Abra seu gerenciador de banco de dados (como **DBeaver** ou **Beekeeper Studio**) e execute o arquivo `database.sql` para criar o banco de dados da aplicação.

---

## ✅ Finalização

Após seguir todos os passos acima:

- O **back-end** estará rodando e conectado ao banco de dados.  
- O **front-end** estará em execução e se comunicando com o back-end.  
- A aplicação estará pronta para uso localmente 🚀
