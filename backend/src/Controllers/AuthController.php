<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Helpers\ResponseHelper;

class AuthController {
    private AuthService $service;

    public function __construct() {
        $this->service = new AuthService();
    }

    public function register(): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            $result = $this->service->register($data);
            ResponseHelper::success($result, 'Conta criada com sucesso', 201);
        } catch (\InvalidArgumentException $e) {
            ResponseHelper::error('Dados inválidos', 422, json_decode($e->getMessage(), true));
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 409);
        }
    }

    public function login(): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            $result = $this->service->login($data['email'] ?? '', $data['password'] ?? '');
            ResponseHelper::success($result, 'Login efectuado');
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 401);
        }
    }

    public function forgotPassword(): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            $result = $this->service->forgotPassword($data['email'] ?? '');
            ResponseHelper::success($result, 'Pedido de recuperacao registado');
        } catch (\InvalidArgumentException $e) {
            ResponseHelper::error('Dados invalidos', 422, json_decode($e->getMessage(), true));
        }
    }

    public function resetPassword(): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            $this->service->resetPassword($data['token'] ?? '', $data['password'] ?? '');
            ResponseHelper::success(null, 'Password actualizada');
        } catch (\InvalidArgumentException $e) {
            ResponseHelper::error('Dados invalidos', 422, json_decode($e->getMessage(), true));
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 400);
        }
    }
}
