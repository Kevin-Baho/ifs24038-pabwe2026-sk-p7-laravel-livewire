<?php

declare(strict_types=1);

namespace App\Features\Stock\Services;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use Illuminate\Database\Eloquent\Collection;
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
    private const HEADERS = [
        'No',
        'Name',
        'SKU',
        'Description',
        'Quantity',
        'Unit',
        'Price',
        'Min Stock',
        'Status',
        'Created At',
    ];

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
     * Parse uploaded Excel/CSV file and return array of stock data arrays.
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

        if (count($rows) <= 1 && empty(array_filter($rows[1] ?? []))) {
            return $result;
        }

        $headerRow = $rows[1] ?? [];
        $hasDescription = false;
        foreach ($headerRow as $colVal) {
            if (is_string($colVal) && str_contains(strtolower($colVal), 'desc')) {
                $hasDescription = true;
                break;
            }
        }

        // Skip header row (row 1)
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex === 1) {
                continue; // header
            }

            $name = trim((string) ($row['B'] ?? ''));
            if ($name === '') {
                continue;
            }

            if ($hasDescription) {
                $sku         = trim((string) ($row['C'] ?? ''));
                $description = trim((string) ($row['D'] ?? ''));
                $quantity    = max(0, (int) ($row['E'] ?? 0));
                $unit        = trim((string) ($row['F'] ?? 'pcs'));
                $price       = (float) ($row['G'] ?? 0);
                $minStock    = max(1, (int) ($row['H'] ?? 5));
                $statusRaw   = strtolower(trim((string) ($row['I'] ?? 'available')));
            } else {
                $sku         = trim((string) ($row['C'] ?? ''));
                $description = '';
                $quantity    = max(0, (int) ($row['D'] ?? 0));
                $unit        = trim((string) ($row['E'] ?? 'pcs'));
                $price       = (float) ($row['F'] ?? 0);
                $minStock    = max(1, (int) ($row['G'] ?? 5));
                $statusRaw   = strtolower(trim((string) ($row['H'] ?? 'available')));
            }

            // Map label to enum value
            $status = match (true) {
                in_array($statusRaw, ['habis', 'out_of_stock', 'out of stock'], true) => StockStatus::OUT_OF_STOCK,
                in_array($statusRaw, ['stok menipis', 'low_stock', 'low stock'], true) => StockStatus::LOW_STOCK,
                default => StockStatus::determine($quantity, $minStock),
            };

            $result[] = [
                'name'        => $name,
                'sku'         => $sku,
                'description' => $description,
                'quantity'    => $quantity,
                'unit'        => $unit !== '' ? $unit : 'pcs',
                'price'       => $price,
                'min_stock'   => $minStock,
                'status'      => $status->value,
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
            $sheet->setCellValue('D' . $row, $stock->description ?? '');
            $sheet->setCellValue('E' . $row, $stock->quantity);
            $sheet->setCellValue('F' . $row, $stock->unit);
            $sheet->setCellValue('G' . $row, $stock->price ?? 0);
            $sheet->setCellValue('H' . $row, $stock->min_stock);
            $sheet->setCellValue('I' . $row, $stock->status instanceof StockStatus
                ? $stock->status->label()
                : (string) $stock->status,
            );
            $sheet->setCellValue('J' . $row, $stock->created_at?->format('Y-m-d H:i:s') ?? '');
            $row++;
        }
    }

    private function applyStyles(Worksheet $sheet, int $dataCount): void
    {
        $lastRow  = $dataCount + 1;
        $lastCell = 'J' . $lastRow;

        // Dark header style (#0F172A)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Thin borders
        if ($lastRow > 1) {
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '334155'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:' . $lastCell)->applyFromArray($borderStyle);
        }

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getRowDimension(1)->setRowHeight(26);
    }
}
