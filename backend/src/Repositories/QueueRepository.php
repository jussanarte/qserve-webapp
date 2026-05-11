<?php
namespace App\Repositories;

use App\Config\Database;
use PDO;

class QueueRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findAll(): array {
        $stmt = $this->db->query(
            'SELECT q.*, u.name as created_by_name,
                    (SELECT COUNT(*) FROM queue_tickets qt WHERE qt.queue_id = q.id AND qt.status = "waiting") as waiting_count
             FROM queues q
             JOIN users u ON q.created_by = u.id
             ORDER BY q.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM queues WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO queues (name, description, status, max_capacity, avg_service_time, created_by)
             VALUES (:name, :description, :status, :max_capacity, :avg_service_time, :created_by)'
        );
        $stmt->execute([
            'name'             => $data['name'],
            'description'      => $data['description'] ?? null,
            'status'           => $data['status'] ?? 'closed',
            'max_capacity'     => $data['max_capacity'] ?? 50,
            'avg_service_time' => $data['avg_service_time'] ?? 5,
            'created_by'       => $data['created_by'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE queues SET name = :name, description = :description,
             max_capacity = :max_capacity, avg_service_time = :avg_service_time
             WHERE id = :id'
        );
        return $stmt->execute([...$data, 'id' => $id]);
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare('UPDATE queues SET status = :status WHERE id = :id');
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM queues WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}