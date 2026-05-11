<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtHelper {
    public static function encode(array $payload): string {
        $payload['iat'] = time();
        $payload['exp'] = time() + (int)$_ENV['JWT_EXPIRY'];

        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }

    public static function decode(string $token): array {
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return [];
        }
    }
}