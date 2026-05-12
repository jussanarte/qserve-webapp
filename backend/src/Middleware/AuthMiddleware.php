<?php
namespace App\Middleware;

use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;

class AuthMiddleware {
    public static function handle(): void {
        $header = self::authorizationHeader();

        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            ResponseHelper::error('Token em falta', 401);
        }

        $token = trim($matches[1]);
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

    private static function authorizationHeader(): string {
        $candidates = [
            $_SERVER['HTTP_AUTHORIZATION'] ?? '',
            $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '',
            $_SERVER['Authorization'] ?? '',
        ];

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            foreach ($headers as $name => $value) {
                if (strtolower($name) === 'authorization') {
                    $candidates[] = $value;
                }
            }
        }

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return '';
    }
}
