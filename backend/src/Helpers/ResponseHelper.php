<?php
namespace App\Helpers;

class ResponseHelper {
    public static function success(mixed $data = null, string $message = '', int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
        exit;
    }

    public static function error(string $message, int $code = 400, array $errors = []): never {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ]);
        exit;
    }
}