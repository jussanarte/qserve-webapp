<?php
namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService {
    private DashboardRepository $repo;

    public function __construct() {
        $this->repo = new DashboardRepository();
    }

    public function stats(): array {
        $byStatus = ['waiting' => 0, 'called' => 0, 'served' => 0, 'cancelled' => 0];
        foreach ($this->repo->ticketsByStatusToday() as $row) {
            if (array_key_exists($row['status'], $byStatus)) {
                $byStatus[$row['status']] = (int)$row['total'];
            }
        }

        $byHour = array_fill(0, 24, 0);
        foreach ($this->repo->ticketsByHourToday() as $row) {
            $byHour[(int)$row['hour']] = (int)$row['total'];
        }

        return [
            'total_users'       => $this->repo->countActiveUsers(),
            'active_queues'     => $this->repo->countActiveQueues(),
            'tickets_today'     => $this->repo->countTicketsToday(),
            'avg_service_time'  => $this->repo->averageServiceTimeToday(),
            'tickets_by_status' => $byStatus,
            'tickets_by_hour'   => $byHour,
            'queue_summaries'   => array_map(
                fn(array $row): array => [
                    'id' => (int)$row['id'],
                    'name' => $row['name'],
                    'waiting' => (int)$row['waiting'],
                    'served_today' => (int)$row['served_today'],
                ],
                $this->repo->queueSummaries()
            ),
        ];
    }
}
