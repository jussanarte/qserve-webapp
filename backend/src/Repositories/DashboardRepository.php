<?php
namespace App\Repositories;

use App\Config\Database;
use PDO;

class DashboardRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function countActiveUsers(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM users WHERE is_active = 1');
        return (int)$stmt->fetchColumn();
    }

    public function countActiveQueues(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM queues WHERE status = "open"');
        return (int)$stmt->fetchColumn();
    }

    public function countTicketsToday(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM queue_tickets WHERE DATE(created_at) = CURDATE()');
        return (int)$stmt->fetchColumn();
    }

    public function averageServiceTimeToday(): int {
        $stmt = $this->db->query(
            'SELECT AVG(TIMESTAMPDIFF(MINUTE, called_at, served_at))
             FROM queue_tickets
             WHERE served_at IS NOT NULL AND DATE(created_at) = CURDATE()'
        );
        return (int)round((float)($stmt->fetchColumn() ?: 0));
    }

    public function ticketsByStatusToday(): array {
        $stmt = $this->db->query(
            'SELECT status, COUNT(*) as total
             FROM queue_tickets
             WHERE DATE(created_at) = CURDATE()
             GROUP BY status'
        );
        return $stmt->fetchAll();
    }

    public function ticketsByHourToday(): array {
        $stmt = $this->db->query(
            'SELECT HOUR(created_at) as hour, COUNT(*) as total
             FROM queue_tickets
             WHERE DATE(created_at) = CURDATE()
             GROUP BY HOUR(created_at)'
        );
        return $stmt->fetchAll();
    }

    public function queueSummaries(): array {
        $stmt = $this->db->query(
            'SELECT q.id, q.name,
                SUM(CASE WHEN qt.status = "waiting" THEN 1 ELSE 0 END) as waiting,
                SUM(CASE WHEN qt.status = "served" AND DATE(qt.created_at) = CURDATE() THEN 1 ELSE 0 END) as served_today
             FROM queues q
             LEFT JOIN queue_tickets qt ON q.id = qt.queue_id
             GROUP BY q.id, q.name
             ORDER BY q.name ASC'
        );
        return $stmt->fetchAll();
    }
}
