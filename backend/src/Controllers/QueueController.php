<?php
namespace App\Controllers;

use App\Services\QueueService;
use App\Helpers\ResponseHelper;
use App\Middleware\AuthMiddleware;

class QueueController {
    private QueueService $service;

    public function __construct() {
        $this->service = new QueueService();
    }

    public function index(): void {
        AuthMiddleware::handle();
        ResponseHelper::success($this->service->getAll());
    }

    public function show(int $id): void {
        AuthMiddleware::handle();
        try {
            ResponseHelper::success($this->service->getById($id));
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 404);
        }
    }

    public function store(): void {
        AuthMiddleware::requireRole(['admin']);
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            $queue = $this->service->create($data, $_REQUEST['auth_user_id']);
            ResponseHelper::success($queue, 'Fila criada', 201);
        } catch (\InvalidArgumentException $e) {
            ResponseHelper::error('Dados inválidos', 422, json_decode($e->getMessage(), true));
        }
    }

    public function update(int $id): void {
        AuthMiddleware::requireRole(['admin']);
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            ResponseHelper::success($this->service->update($id, $data));
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 404);
        }
    }

    public function changeStatus(int $id): void {
        AuthMiddleware::requireRole(['admin', 'attendant']);
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            ResponseHelper::success($this->service->changeStatus($id, $data['status'] ?? ''));
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 400);
        }
    }

    public function destroy(int $id): void {
        AuthMiddleware::requireRole(['admin']);
        try {
            $this->service->delete($id);
            ResponseHelper::success(null, 'Fila eliminada');
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 400);
        }
    }

    public function join(): void {
        AuthMiddleware::handle();
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        try {
            $result = $this->service->joinQueue(
                (int)($data['queue_id'] ?? 0),
                $_REQUEST['auth_user_id']
            );
            ResponseHelper::success($result, 'Entrou na fila', 201);
        } catch (\RuntimeException $e) {
            ResponseHelper::error($e->getMessage(), 400);
        }
    }

    public function callNext(): void {
        AuthMiddleware::requireRole(['admin', 'attendant']);
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $ticket = $this->service->callNext((int)($data['queue_id'] ?? 0));
        ResponseHelper::success($ticket, $ticket ? 'Próximo chamado' : 'Sem tickets em espera');
    }

    public function updateTicketStatus(int $ticketId): void {
        AuthMiddleware::requireRole(['admin', 'attendant']);
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->service->updateTicketStatus($ticketId, $data['status'] ?? '');
        ResponseHelper::success(null, 'Status actualizado');
    }

    public function myTickets(): void {
        AuthMiddleware::handle();
        ResponseHelper::success($this->service->getMyTickets($_REQUEST['auth_user_id']));
    }

    public function queueTickets(int $queueId): void {
        AuthMiddleware::requireRole(['admin', 'attendant']);
        ResponseHelper::success($this->service->getQueueTickets($queueId));
    }
}