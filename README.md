# 🍽️ Qserve — Sistema de Atendimento de Fila do Refeitório

[![Angular](https://img.shields.io/badge/Angular-21-red)](https://angular.io)
[![PHP](https://img.shields.io/badge/PHP-8.2-blue)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)](https://mysql.com)

> Plataforma web de gestão digital de filas para refeitórios universitários.

## Stack
- **Frontend:** Angular 21, @ngx-translate, Chart.js, QRCode.js
- **Backend:** PHP 8.2 puro, JWT (php-jwt), dompdf
- **Base de Dados:** MySQL 8.0
- **Autenticação:** JWT HS256

## Funcionalidades
- ✅ Autenticação completa (registo, login, recuperação de senha)
- ✅ 3 perfis: Admin, Funcionário, Estudante
- ✅ Gestão de filas em tempo real (polling 5s)
- ✅ QR Code por ticket
- ✅ Dashboard analytics
- ✅ Exportação PDF e CSV
- ✅ Dark mode persistente
- ✅ i18n: Português + Inglês
- ✅ Totalmente responsivo

## Instalação

### Pré-requisitos
- PHP 8.2
- MySQL 8.0
- Composer
- Node.js 20+ e npm
- Servidor local tipo XAMPP ou similar

### Como clonar o projeto
```bash
git clone https://github.com/jussanarte/qserve-webapp.git
cd qserve-webapp
```

### Configurar o backend
1. Navegue para a pasta do backend:
   ```bash
   cd backend
   ```
2. Instale as dependências PHP:
   ```bash
   composer install
   ```
3. Configure a base de dados MySQL usando o ficheiro `database/init.sql`.
4. Ajuste as credenciais de ligação à base de dados no ficheiro de configuração apropriado, se existir.
5. Inicie o servidor PHP local ou use o Apache do XAMPP apontando para `backend/public`.

### Configurar o frontend
1. No diretório raiz do projeto, vá para a pasta `frontend`:
   ```bash
   cd frontend
   ```
2. Instale as dependências do Angular:
   ```bash
   npm install
   ```
3. Inicie a aplicação Angular:
   ```bash
   npm start
   ```
4. Abra o navegador em `http://localhost:4200`.

> Se estiver a usar XAMPP, assegure-se de que o Apache e o MySQL estão em execução. O backend deve ser servido a partir de `backend/public` e o frontend a partir de `frontend`.

## Arquitectura

```
qserve-webapp/
├── backend/
│   ├── composer.json
│   ├── public/
│   │   └── index.php
│   ├── src/
│   │   ├── Config/
│   │   │   └── Database.php
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   └── QueueController.php
│   │   ├── Helpers/
│   │   │   ├── JwtHelper.php
│   │   │   ├── ResponseHelper.php
│   │   │   └── Validator.php
│   │   ├── Middleware/
│   │   │   └── AuthMiddleware.php
│   │   ├── Repositories/
│   │   │   ├── QueueRepository.php
│   │   │   ├── TicketRespository.php
│   │   │   └── UserRepository.php
│   │   ├── Router/
│   │   │   └── Router.php
│   │   └── Services/
│   │       ├── AuthService.php
   │       └── QueueService.php
│   └── vendor/
├── database/
│   └── init.sql
├── docs/
├── frontend/
│   ├── angular.json
│   ├── package.json
│   ├── src/
│   │   ├── app/
│   │   │   ├── app.config.ts
│   │   │   ├── app.html
│   │   │   ├── app.routes.ts
│   │   │   ├── app.scss
│   │   │   ├── app.spec.ts
│   │   │   ├── app.ts
│   │   │   ├── core/
│   │   │   ├── features/
│   │   │   └── layout/
│   │   ├── assets/
│   │   │   └── i18n/
│   │   ├── environments/
│   │   │   └── environment.ts
│   │   ├── index.html
│   │   ├── main.ts
│   │   ├── styles.css
│   │   └── styles.scss
│   └── tsconfig.json
└── README.md
```

## Licença

MIT