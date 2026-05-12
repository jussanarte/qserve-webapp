# Guia do Projecto QSERVE

## Como Funciona

O QSERVE é uma aplicação para gestão digital de filas no refeitório universitário do ISPTEC. A ideia é substituir a espera desorganizada por um fluxo simples:

1. O estudante cria conta ou inicia sessão.
2. O estudante consulta as filas disponíveis.
3. Ao entrar numa fila aberta, recebe um ticket digital.
4. O ticket tem um QR code que pode ser apresentado no balcão.
5. O funcionário escolhe uma fila e chama o próximo ticket.
6. O estudante acompanha o estado do ticket.
7. O administrador acompanha estatísticas, gere filas, gere funcionários e exporta relatórios.

O sistema usa polling no frontend para manter as filas e os tickets actualizados sem exigir WebSockets. Isto simplifica a instalação em XAMPP/Apache e mantém o projecto fácil de demonstrar num ambiente académico.

## Perfis e Permissões

### Estudante

O estudante é o utilizador final do refeitório.

Pode:

- Criar conta.
- Iniciar sessão.
- Ver filas.
- Entrar numa fila aberta.
- Ver os seus tickets.
- Abrir o QR code do ticket.
- Acompanhar estados: em espera, chamado, servido ou cancelado.

Não pode:

- Criar, editar ou fechar filas.
- Chamar tickets.
- Ver dashboard administrativo.
- Exportar relatórios.
- Gerir funcionários.

### Funcionário

O funcionário é quem opera o atendimento no balcão.

Pode:

- Ver filas.
- Seleccionar uma fila.
- Ver tickets da fila.
- Chamar o próximo ticket.
- Marcar ticket como servido.
- Marcar ausência/cancelamento.

Não pode:

- Criar administradores.
- Gerir funcionários.
- Exportar relatórios administrativos.
- Apagar filas.

### Administrador

O administrador gere o sistema.

Pode:

- Criar conta como administrador no registo.
- Aceder ao dashboard.
- Criar, editar, abrir, pausar, fechar e eliminar filas.
- Criar, editar e remover funcionários.
- Exportar relatórios CSV e PDF.
- Usar também as áreas de fila e atendimento.

## Porque o Projecto Foi Modelado Deste Jeito

### Separação por responsabilidades

O backend foi dividido em Controllers, Services e Repositories para manter responsabilidades claras:

- Controllers tratam HTTP: recebem pedidos, lêem input e devolvem respostas.
- Services tratam regras de negócio: validações, decisões e fluxos.
- Repositories tratam SQL: toda a comunicação com a base de dados.

Esta separação facilita manutenção, testes e evolução. Por exemplo, uma regra como "não eliminar fila com tickets em espera" fica no service, enquanto a query que conta tickets fica no repository.

### Angular standalone

O frontend usa componentes standalone para evitar módulos pesados e tornar cada página mais independente. Isto combina bem com lazy loading nas rotas:

- `/queue` carrega apenas a área do estudante.
- `/attendant` carrega apenas o painel de atendimento.
- `/admin` carrega apenas a área administrativa.

### JWT e guards

O JWT mantém a API stateless: o backend não precisa guardar sessão no servidor. No frontend, guards funcionais (`CanActivateFn`) controlam acesso por autenticação e role.

### Polling em vez de WebSockets

O polling a cada 5 segundos foi escolhido por simplicidade operacional:

- Funciona bem em Apache/XAMPP.
- Evita servidor Node/WebSocket separado.
- É suficiente para o cenário de refeitório, onde poucos segundos de atraso são aceitáveis.

### Relatórios por CSV e PDF

CSV serve para análise no Excel. PDF serve para arquivo e partilha formal. O CSV inclui BOM UTF-8 para preservar acentos em ferramentas como Excel.

## Stack Técnica

- Frontend: Angular 21 standalone, RxJS, @ngx-translate, SCSS e `qrcode`.
- Backend: PHP 8.2 puro, PDO, JWT, Dotenv e dompdf.
- Base de dados: MySQL 8.
- Autenticação: JWT no header `Authorization: Bearer <token>`.
- API local: `http://localhost/qserve-webapp/backend/public/index.php/api`.
- Frontend local: `http://localhost:4200` ou `http://127.0.0.1:4200`.

## Módulos do Frontend

### Autenticação

Localização:

- `frontend/src/app/features/auth/components/login`
- `frontend/src/app/features/auth/components/register`

Inclui:

- Login.
- Registo de estudante ou administrador.
- Persistência de sessão em `sessionStorage`.
- Redireccionamento conforme role.
- Selector de idioma PT/EN.

### Filas do Estudante

Localização:

- `frontend/src/app/features/queue`

Inclui:

- Lista de filas.
- Polling a cada 5 segundos.
- Entrada em fila.
- Lista de tickets.
- Modal de QR code.

### Painel do Funcionário

Localização:

- `frontend/src/app/features/attendant`

Inclui:

- Selecção de fila.
- Polling dos tickets da fila.
- Chamada do próximo ticket.
- Marcação de servido ou cancelado.

### Administração

Localização:

- `frontend/src/app/features/admin`

Páginas:

- `/admin/dashboard`: estatísticas, gráfico SVG por hora e resumo por fila.
- `/admin/queues`: gestão de filas.
- `/admin/staff`: gestão de funcionários.
- `/admin/reports`: exportação CSV/PDF por intervalo de datas.

## Backend

### Organização

- `Controllers`: camada HTTP.
- `Services`: regras de negócio.
- `Repositories`: SQL e acesso a dados.
- `Middleware`: autenticação e autorização.
- `Helpers`: JWT, validação e resposta JSON.
- `Router`: registo e despacho das rotas.

### Endpoints Principais

Autenticação:

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/forgot-password`
- `POST /api/auth/reset-password`

Filas:

- `GET /api/queues`
- `POST /api/queues`
- `GET /api/queues/:id`
- `PUT /api/queues/:id`
- `DELETE /api/queues/:id`
- `PATCH /api/queues/:id/status`

Tickets:

- `POST /api/tickets`
- `GET /api/tickets/mine`
- `POST /api/tickets/call-next`
- `PATCH /api/tickets/:id/status`
- `GET /api/queues/:id/tickets`

Admin:

- `GET /api/dashboard/stats`
- `GET /api/reports/tickets?format=csv|pdf&date_from=YYYY-MM-DD&date_to=YYYY-MM-DD`
- `GET /api/admin/attendants`
- `POST /api/admin/attendants`
- `PUT /api/admin/attendants/:id`
- `DELETE /api/admin/attendants/:id`

## Estados

Estados de fila:

- `open`: aberta.
- `paused`: pausada.
- `closed`: fechada.

Estados de ticket:

- `waiting`: em espera.
- `called`: chamado.
- `served`: servido.
- `cancelled`: cancelado.

## Popular a Base de Dados

Foi criado o ficheiro:

- `database/seed.sql`

Ele cria dados de demonstração e índices úteis para desempenho.

Credenciais geradas:

- Admin: `admin@qserve.ao` / `Admin@123`
- Funcionário: `funcionario@qserve.ao` / `Funcionario@123`
- Estudante: `estudante@qserve.ao` / `Estudante@123`

Para executar no MySQL:

```bash
mysql -u root -p qserve < database/seed.sql
```

Se o utilizador root não tiver password no XAMPP:

```bash
mysql -u root qserve < database/seed.sql
```

## Configuração Local

Backend:

- `backend/.env`

Campos importantes:

```env
DB_HOST=localhost
DB_PORT=3307
DB_NAME=qserve
DB_USER=root
DB_PASS=
ALLOWED_ORIGINS=http://localhost:4200,http://127.0.0.1:4200
```

Se o MySQL estiver na porta padrão, usa:

```env
DB_PORT=3306
```

Frontend:

- `frontend/src/environments/environment.ts`

```ts
apiUrl: 'http://localhost/qserve-webapp/backend/public/index.php/api'
```

## Comandos Úteis

Frontend:

```bash
cd frontend
npm install
npm start
npm run build
```

Backend:

```bash
cd backend
composer install
php -l public/index.php
```

Teste rápido da ligação à base de dados:

```bash
php -r "require 'vendor/autoload.php'; Dotenv\\Dotenv::createImmutable(__DIR__)->load(); App\\Config\\Database::getInstance(); echo 'DB_OK';"
```

## Desempenho

Foram adicionadas optimizações simples:

- Índices para utilizadores por role/estado.
- Índices para tickets por fila, estado, utilizador e data.
- Contagem de tickets em espera por fila com agregação em vez de subquery por linha.

Estes pontos ajudam principalmente em:

- Polling de filas.
- Painel de atendimento.
- Dashboard.
- Relatórios por intervalo de datas.

## Problemas Frequentes

### CORS

Confirma se a origem usada no browser está em `ALLOWED_ORIGINS`.

### Base de dados não liga

Verifica:

- MySQL ligado.
- Porta correcta em `DB_PORT`.
- Base de dados `qserve` criada.
- Credenciais correctas no `.env`.

### Volta ao login depois de entrar

Normalmente significa que uma rota protegida devolveu `401`. Confirma:

- Token existe em `sessionStorage`.
- Apache está a preservar o header `Authorization`.
- `backend/public/.htaccess` está activo.

### Acesso negado

Confirma a role do utilizador. Dashboard, relatórios e gestão de funcionários exigem admin.
