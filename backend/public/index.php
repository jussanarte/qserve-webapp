<?php
// Carrega autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Carrega variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Headers CORS — sempre primeiro
$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = array_filter(array_map(
    'trim',
    explode(',', $_ENV['ALLOWED_ORIGINS'] ?? ($_ENV['ALLOWED_ORIGIN'] ?? '*'))
));

if (in_array('*', $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: *');
} elseif ($requestOrigin !== '' && in_array($requestOrigin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
    header('Vary: Origin');
} elseif (!empty($allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $allowedOrigins[0]);
}
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Responder preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

use App\Router\Router;
use App\Controllers\AuthController;
use App\Controllers\QueueController;
use App\Controllers\ReportController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;

$router = new Router();

// ─── Rotas de Autenticação ───────────────────────
$router->add('POST', '/api/auth/register', fn() => (new AuthController())->register());
$router->add('POST', '/api/auth/login',    fn() => (new AuthController())->login());
$router->add('POST', '/api/auth/forgot-password', fn() => (new AuthController())->forgotPassword());
$router->add('POST', '/api/auth/reset-password',  fn() => (new AuthController())->resetPassword());

// ─── Rotas de Filas ──────────────────────────────
$router->add('GET',   '/api/queues',                fn()      => (new QueueController())->index());
$router->add('POST',  '/api/queues',                fn()      => (new QueueController())->store());
$router->add('GET',   '/api/queues/:id',            fn($id)   => (new QueueController())->show($id));
$router->add('PUT',   '/api/queues/:id',            fn($id)   => (new QueueController())->update($id));
$router->add('DELETE','/api/queues/:id',            fn($id)   => (new QueueController())->destroy($id));
$router->add('PATCH', '/api/queues/:id/status',     fn($id)   => (new QueueController())->changeStatus($id));

// ─── Rotas de Tickets ────────────────────────────
$router->add('POST',  '/api/tickets',               fn()      => (new QueueController())->join());
$router->add('GET',   '/api/tickets/mine',          fn()      => (new QueueController())->myTickets());
$router->add('POST',  '/api/tickets/call-next',     fn()      => (new QueueController())->callNext());
$router->add('PATCH', '/api/tickets/:id/status',    fn($id)   => (new QueueController())->updateTicketStatus($id));
$router->add('GET',   '/api/queues/:id/tickets',    fn($id)   => (new QueueController())->queueTickets($id));

$router->add('GET', '/api/dashboard/stats',  fn() => (new DashboardController())->stats());
$router->add('GET', '/api/reports/tickets',  fn() => (new ReportController())->tickets());

$router->add('GET',    '/api/admin/attendants',     fn()      => (new UserController())->attendants());
$router->add('POST',   '/api/admin/attendants',     fn()      => (new UserController())->createAttendant());
$router->add('PUT',    '/api/admin/attendants/:id', fn($id)   => (new UserController())->updateAttendant($id));
$router->add('DELETE', '/api/admin/attendants/:id', fn($id)   => (new UserController())->deleteAttendant($id));

$router->dispatch();
