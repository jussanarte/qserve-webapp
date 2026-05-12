<?php
namespace App\Services;

use App\Repositories\ReportRepository;

class ReportService {
    private ReportRepository $repo;

    public function __construct() {
        $this->repo = new ReportRepository();
    }

    public function tickets(string $dateFrom, string $dateTo): array {
        if (!$this->isDate($dateFrom) || !$this->isDate($dateTo)) {
            throw new \InvalidArgumentException('Datas invalidas');
        }

        return $this->repo->ticketReport($dateFrom, $dateTo);
    }

    private function isDate(string $value): bool {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
