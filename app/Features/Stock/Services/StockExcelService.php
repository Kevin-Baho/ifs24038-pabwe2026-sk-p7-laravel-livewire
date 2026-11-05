<?php

declare(strict_types=1);

namespace App\Features\Stock\Services;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Repositories\StockRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockExcelService
{
    private const HEADERS = ['No', 'Name', 'SKU', 'Quantity', 'Unit', 'Price', 'Min Stock', 'Status'];

    public function export(Collection $stocks): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $this->writeHeaders($sheet);
        $this->writeData($sheet, $stocks);
        $this->applyStyles($sheet, $stocks->count());

        return new StreamedResponse(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="stocks-' . now()->format('Y-m-d') . '.xlsx"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Parse uploaded Excel file and return array of stock data arrays.
     *
     * @param string $filePath Absolute path to the uploaded file
     * @return array<int, array<string, mixed>>
     */
    public function import(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();

        $rows   = $sheet->toArray(null, true, true, true);
        $result = [];

        // Skip header row (row 1)
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex === 1) {
                continue; // header
            }

            $name = trim((string) ($row['B'] ?? ''));
            if ($name === '') {
                continue;
            }

            $quantity  = max(0, (int) ($row['D'] ?? 0));
            $minStock  = max(1, (int) ($row['G'] ?? 5));
            $statusRaw = strtolower(trim((string) ($row['H'] ?? 'available')));

            // Map label to enum value
            $status = match (true) {
                in_array($statusRaw, ['habis', 'out_of_stock', 'out of stock'], true) => StockStatus::OutOfStock,
                in_array($statusRaw, ['stok menipis', 'low_stock', 'low stock'], true) => StockStatus::LowStock,
                default => StockStatus::fromQuantity($quantity, $minStock),
            };

            $result[] = [
                'name'      => $name,
                'sku'       => trim((string) ($row['C'] ?? '')),
                'quantity'  => $quantity,
                'unit'      => trim((string) ($row['E'] ?? 'pcs')),
                'price'     => (float) ($row['F'] ?? 0),
                'min_stock' => $minStock,
                'status'    => $status->value,
            ];
        }

        return $result;
    }

    private function writeHeaders(Worksheet $sheet): void
    {
        foreach (self::HEADERS as $col => $header) {
            $cell = chr(65 + $col) . '1'; // A1, B1, ...
            $sheet->setCellValue($cell, $header);
        }
    }

    private function writeData(Worksheet $sheet, Collection $stocks): void
    {
        $row = 2;
        foreach ($stocks as $i => $stock) {
            /** @var Stock $stock */
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $stock->name);
            $sheet->setCellValue('C' . $row, $stock->sku ?? '');
            $sheet->setCellValue('D' . $row, $stock->quantity);
            $sheet->setCellValue('E' . $row, $stock->unit);
            $sheet->setCellValue('F' . $row, $stock->price ?? 0);
            $sheet->setCellValue('G' . $row, $stock->min_stock);
            $sheet->setCellValue('H' . $row, $stock->status instanceof StockStatus
                ? $stock->status->label()
                : (string) $stock->status,
            );
            $row++;
        }
    }

    private function applyStyles(Worksheet $sheet, int $dataCount): void
    {
        $lastRow  = $dataCount + 1;
        $lastCell = 'H' . $lastRow;

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // All borders
        if ($lastRow > 1) {
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:' . $lastCell)->applyFromArray($borderStyle);
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getRowDimension(1)->setRowHeight(24);
    }
}
