<?php

namespace App\Services;

use App\Support\Reports\ReportDefinition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteExportService
{
    public function pdf(string $tipo, array $filtros, array $resultados): Response
    {
        $definition = ReportDefinition::get($tipo);

        return Pdf::loadView('reportes.pdf', compact('definition', 'filtros', 'resultados'))->setPaper('a4', 'landscape')->download("reporte-{$tipo}-".now()->format('Ymd-His').'.pdf');
    }

    public function excel(string $tipo, array $resultados): StreamedResponse
    {
        $definition = ReportDefinition::get($tipo);
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($definition['title'], 0, 31));
        $sheet->fromArray(array_values($definition['columns']), null, 'A1');
        $rows = array_map(fn (object $row): array => array_map(fn (string $key): mixed => $row->{$key} ?? null, array_keys($definition['columns'])), $resultados);
        if ($rows) {
            $sheet->fromArray($rows, null, 'A2');
        }
        $last = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$last}1")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle("A1:{$last}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2563EB');
        foreach (range('A', $last) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $filename = "reporte-{$tipo}-".now()->format('Ymd-His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
