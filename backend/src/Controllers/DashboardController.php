<?php
namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Middleware\AuthMiddleware;
use App\Services\DashboardService;

class DashboardController {
    private DashboardService $service;

    public function __construct() {
        $this->service = new DashboardService();
    }

    public function stats(): void {
        AuthMiddleware::requireRole(['admin']);
        ResponseHelper::success($this->service->stats());
    }
}
