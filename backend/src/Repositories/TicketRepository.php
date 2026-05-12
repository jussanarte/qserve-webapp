<?php
namespace App\Repositories;

use App\Config\Database;
use PDO;

class TicketRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findActiveByUser(int $userId, int $queueId): ?array {
        $stmt = $this->db->prepare(
            'SELECT * FROM queue_tickets
             WHERE user_id = :user_id AND queue_id = :queue_id
             AND status IN ("waiting","called")'
        );
        $stmt->execute(['user_id' => $userId, 'queue_id' => $queueId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByQueue(int $queueId, string $status = null): array {
        if ($status) {
            $stmt = $this->db->prepare(
                'SELECT qt.*, u.name as user_name
                 FROM queue_tickets qt JOIN users u ON qt.user_id = u.id
                 WHERE qt.queue_id = :queue_id AND qt.status = :status
                 ORDER BY qt.created_at ASC'
            );
            $stmt->execute(['queue_id' => $queueId, 'status' => $status]);
        } else {
            $stmt = $this->db->prepare(
                'SELECT qt.*, u.name as user_name
                 FROM queue_tickets qt JOIN users u ON qt.user_id = u.id
                 WHERE qt.queue_id = :queue_id
                 ORDER BY qt.created_at DESC'
            );
            $stmt->execute(['queue_id' => $queueId]);
        }
        return $stmt->fetchAll();
    }

    public function findMyTickets(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT qt.*, q.name as queue_name
             FROM queue_tickets qt JOIN queues q ON qt.queue_id = q.id
             WHERE qt.user_id = :user_id
             ORDER BY qt.created_at DESC LIMIT 10'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO queue_tickets (queue_id, user_id, ticket_number, status)
             VALUES (:queue_id, :user_id, :ticket_number, "waiting")'
        );
        $stmt->execute([
            'queue_id'      => $data['queue_id'],
            'user_id'       => $data['user_id'],
            'ticket_number' => $data['ticket_number'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool {
        $column = match($status) {
            'called'    => ', called_at = NOW()',
            'served'    => ', served_at = NOW()',
            'cancelled' => ', cancelled_at = NOW()',
            default     => '',
        };
        $stmt = $this->db->prepare(
            "UPDATE queue_tickets SET status = :status $column WHERE id = :id"
        );
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function getNextWaiting(int $queueId): ?array {
        $stmt = $this->db->prepare(
            'SELECT qt.*, u.name as user_name
             FROM queue_tickets qt JOIN users u ON qt.user_id = u.id
             WHERE qt.queue_id = :queue_id AND qt.status = "waiting"
             ORDER BY qt.created_at ASC LIMIT 1'
        );
        $stmt->execute(['queue_id' => $queueId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function generateTicketNumber(int $queueId): string {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM queue_tickets
             WHERE queue_id = :queue_id AND DATE(created_at) = CURDATE()'
        );
        $stmt->execute(['queue_id' => $queueId]);
        $count = (int) $stmt->fetchColumn();
        return 'Q' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    public function countWaiting(int $queueId): int {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM queue_tickets WHERE queue_id = :queue_id AND status = "waiting"'
        );
        $stmt->execute(['queue_id' => $queueId]);
        return (int) $stmt->fetchColumn();
    }
}