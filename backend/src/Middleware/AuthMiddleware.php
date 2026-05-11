<?php
namespace App\Middleware;

use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;

class AuthMiddleware {
    public static function handle(): void {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!str_starts_with($header, 'Bearer ')) {
            ResponseHelper::error('Token em falta', 401);
        }

        $token = substr($header, 7);
        $payload = JwtHelper::decode($token);

        if (empty($payload)) {
            ResponseHelper::error('Token inválido ou expirado', 401);
        }

        // Injjecta dados do utilizador autenticado no request
        $_REQUEST['auth_user_id'] = $payload['user_id'];
        $_REQUEST['auth_role']    = $payload['role'];
    }

    public static function requireRole(array $roles): void {
        self::handle();
        if (!in_array($_REQUEST['auth_role'], $roles)) {
            ResponseHelper::error('Acesso negado', 403);
        }
    }
}