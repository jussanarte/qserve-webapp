<?php
namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Middleware\AuthMiddleware;
use App\Services\ReportService;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController {
    private ReportService $service;

    public function __construct() {
        $this->service = new ReportService();
    }

    public function tickets(): void {
        AuthMiddleware::requireRole(['admin']);

        $format   = $_GET['format']    ?? 'csv';
        $dateFrom = $_GET['date_from'] ?? date('Y-m-d');
        $dateTo   = $_GET['date_to']   ?? date('Y-m-d');

        if (!in_array($format, ['csv', 'pdf'], true)) {
            ResponseHelper::error('Formato invalido', 422);
        }

        try {
            $tickets = $this->service->tickets($dateFrom, $dateTo);
        } catch (\InvalidArgumentException $e) {
            ResponseHelper::error($e->getMessage(), 422);
        }

        if ($format === 'pdf') {
            $this->generatePdf($tickets, $dateFrom, $dateTo);
        } else {
            $this->generateCsv($tickets, $dateFrom, $dateTo);
        }
    }

    private function generateCsv(array $tickets, string $from, string $to): never {
        $filename = "qserve-report-$from-$to.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo "\xEF\xBB\xBF"; // BOM — Excel lê acentos correctamente

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Ticket', 'Fila', 'Utilizador', 'Status', 'Criado em', 'Servido em'], ';');
        foreach ($tickets as $t) {
            fputcsv($out, array_values($t), ';');
        }
        fclose($out);
        exit;
    }

    private function generatePdf(array $tickets, string $from, string $to): never {
        $rows = '';
        foreach ($tickets as $t) {
            $status = htmlspecialchars($t['status']);
            $color  = match($t['status']) {
                'served'    => '#22C55E',
                'called'    => '#F24C00',
                'cancelled' => '#EF4444',
                default     => '#F59E0B',
            };
            $ticketNumber = htmlspecialchars($t['ticket_number']);
            $queue = htmlspecialchars($t['fila']);
            $user = htmlspecialchars($t['utilizador']);
            $createdAt = htmlspecialchars($t['created_at']);
            $servedAt = htmlspecialchars($t['served_at']);
            $rows .= "<tr>
                <td><strong>{$ticketNumber}</strong></td>
                <td>{$queue}</td>
                <td>{$user}</td>
                <td><span style='color:{$color};font-weight:600'>{$status}</span></td>
                <td>{$createdAt}</td>
                <td>{$servedAt}</td>
            </tr>";
        }

        $total = count($tickets);
        $html  = "
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset='UTF-8'>
        <style>
          body        { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #030027; }
          .header     { background: #030027; color: #F2F3D9; padding: 20px; margin-bottom: 20px; }
          .brand      { font-size: 28px; font-weight: bold; color: #F24C00; letter-spacing: 3px; }
          .sub        { font-size: 13px; color: #F2F3D9; margin-top: 4px; }
          .period     { font-size: 12px; color: #9CA3AF; margin-top: 8px; }
          table       { width: 100%; border-collapse: collapse; margin-top: 16px; }
          th          { background: #030027; color: #F2F3D9; padding: 10px 12px; text-align: left;
                        font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
          td          { padding: 9px 12px; border-bottom: 1px solid #E0E1C8; font-size: 12px; }
          tr:nth-child(even) td { background: #F2F3D9; }
          .footer     { margin-top: 20px; font-size: 11px; color: #6B7280; text-align: right; }
          .total      { font-weight: bold; color: #030027; }
        </style>
        </head>
        <body>
          <div class='header'>
            <div class='brand'>QSERVE</div>
            <div class='sub'>Sistema de Atendimento de Fila do Refeitório</div>
            <div class='period'>Período: $from a $to — Gerado em: " . date('d/m/Y H:i') . "</div>
          </div>
          <table>
            <thead>
              <tr>
                <th>Ticket</th><th>Fila</th><th>Utilizador</th>
                <th>Status</th><th>Criado em</th><th>Servido em</th>
              </tr>
            </thead>
            <tbody>$rows</tbody>
          </table>
          <div class='footer'>
            <span class='total'>Total de registos: $total</span> &nbsp;|&nbsp; Qserve — ISPTEC DCSA
          </div>
        </body>
        </html>";

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = "qserve-report-$from-$to.pdf";
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo $dompdf->output();
        exit;
    }
}
