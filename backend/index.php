<?php
// backend/index.php
require_once 'utils/Response.php';
require_once 'config/database.php';

// Configuração de CORS (Essencial para o Angular conseguir aceder)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Lógica simples de roteamento baseada no parâmetro 'url' (via .htaccess)
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

// Exemplo de roteamento para API de Fila
if ($url === 'api/queue') {
    // Aqui chamaríamos o controller correspondente
    Response::send(['message' => 'Qserve API ativa', 'status' => 'online']);
} else {
    // Rota não encontrada
    // Response::send(['error' => 'Endpoint não encontrado'], 404);
}