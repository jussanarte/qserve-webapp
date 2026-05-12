<?php
namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Middleware\AuthMiddleware;
use App\Services\UserService;

class UserController {
    private UserService $service;

    public function __construct() {
        $this->service = new UserService();
    }

    public function attendants(): void {
        AuthMiddleware::requireRole(['admin']);
        ResponseHelper::success($this->service->attendants());
    }

    public function createAttendant(): void {
        AuthMiddleware::requireRole(['admin']);
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            ResponseHelper::success($this->service->createAttendant($data), 'Funcionario criado', 201);
        } catch (\InvalidArgumentException $e) {
            ResponseHelper::error('Dados invalidos', 422, json_decode($e->getMessage(), true));
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 409);
        }
    }

    public function updateAttendant(int $id): void {
        AuthMiddleware::requireRole(['admin']);
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            ResponseHelper::success($this->service->updateAttendant($id, $data), 'Funcionario actualizado');
        } catch (\InvalidArgumentException $e) {
            ResponseHelper::error('Dados invalidos', 422, json_decode($e->getMessage(), true));
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 404);
        }
    }

    public function deleteAttendant(int $id): void {
        AuthMiddleware::requireRole(['admin']);
        try {
            $this->service->deactivateAttendant($id);
            ResponseHelper::success(null, 'Funcionario removido');
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 404);
        }
    }
}
