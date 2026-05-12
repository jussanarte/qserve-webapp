<?php
namespace App\Repositories;

use App\Config\Database;
use PDO;

class ReportRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function ticketReport(string $dateFrom, string $dateTo): array {
        $stmt = $this->db->prepare(
            'SELECT qt.ticket_number, q.name as fila, u.name as utilizador,
                    qt.status, qt.created_at, COALESCE(qt.served_at, "-") as served_at
             FROM queue_tickets qt
             JOIN queues q ON qt.queue_id = q.id
             JOIN users u ON qt.user_id = u.id
             WHERE DATE(qt.created_at) BETWEEN :date_from AND :date_to
             ORDER BY qt.created_at DESC'
        );
        $stmt->execute(['date_from' => $dateFrom, 'date_to' => $dateTo]);
        return $stmt->fetchAll();
    }
}
