<?php
namespace App\Services;

use App\Repositories\QueueRepository;
use App\Repositories\TicketRepository;
use App\Helpers\Validator;

class QueueService {
    private QueueRepository $queueRepo;
    private TicketRepository $ticketRepo;

    public function __construct() {
        $this->queueRepo  = new QueueRepository();
        $this->ticketRepo = new TicketRepository();
    }

    public function getAll(): array {
        return $this->queueRepo->findAll();
    }

    public function getById(int $id): array {
        $queue = $this->queueRepo->findById($id);
        if (!$queue) throw new \RuntimeException('Fila não encontrada');
        return $queue;
    }

    public function create(array $data, int $createdBy): array {
        $errors = Validator::validate($data, [
            'name' => 'required|min:3|max:100',
        ]);
        if (!empty($errors)) throw new \InvalidArgumentException(json_encode($errors));

        $id = $this->queueRepo->create([...$data, 'created_by' => $createdBy]);
        return $this->queueRepo->findById($id);
    }

    public function update(int $id, array $data): array {
        $this->getById($id);
        $this->queueRepo->update($id, $data);
        return $this->queueRepo->findById($id);
    }

    public function changeStatus(int $id, string $status): array {
        $allowed = ['open', 'paused', 'closed'];
        if (!in_array($status, $allowed)) throw new \RuntimeException('Status inválido');
        $this->queueRepo->updateStatus($id, $status);
        return $this->queueRepo->findById($id);
    }

    public function delete(int $id): bool {
        $waiting = $this->ticketRepo->countWaiting($id);
        if ($waiting > 0) throw new \RuntimeException('Não é possível eliminar uma fila com tickets em espera');
        return $this->queueRepo->delete($id);
    }

    public function joinQueue(int $queueId, int $userId): array {
        $queue = $this->getById($queueId);
        if ($queue['status'] !== 'open') throw new \RuntimeException('Esta fila não está aberta');

        $existing = $this->ticketRepo->findActiveByUser($userId, $queueId);
        if ($existing) throw new \RuntimeException('Já tem um ticket activo nesta fila');

        $waiting = $this->ticketRepo->countWaiting($queueId);
        if ($waiting >= $queue['max_capacity']) throw new \RuntimeException('Fila sem capacidade');

        $ticketNumber = $this->ticketRepo->generateTicketNumber($queueId);
        $id = $this->ticketRepo->create([
            'queue_id'      => $queueId,
            'user_id'       => $userId,
            'ticket_number' => $ticketNumber,
        ]);

        $position = $waiting + 1;
        $estimatedWait = $position * $queue['avg_service_time'];

        return [
            'ticket_id'      => $id,
            'ticket_number'  => $ticketNumber,
            'position'       => $position,
            'estimated_wait' => $estimatedWait,
            'queue_name'     => $queue['name'],
        ];
    }

    public function callNext(int $queueId): ?array {
        $ticket = $this->ticketRepo->getNextWaiting($queueId);
        if (!$ticket) return null;
        $this->ticketRepo->updateStatus($ticket['id'], 'called');
        $ticket['status'] = 'called';
        return $ticket;
    }

    public function updateTicketStatus(int $ticketId, string $status): bool {
        $allowed = ['waiting', 'called', 'served', 'cancelled'];
        if (!in_array($status, $allowed, true)) throw new \RuntimeException('Status invalido');
        return $this->ticketRepo->updateStatus($ticketId, $status);
    }

    public function getMyTickets(int $userId): array {
        return $this->ticketRepo->findMyTickets($userId);
    }

    public function getQueueTickets(int $queueId): array {
        return $this->ticketRepo->findByQueue($queueId);
    }
}
