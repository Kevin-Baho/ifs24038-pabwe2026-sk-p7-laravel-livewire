<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Services\StockExcelService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class StockExcelServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockExcelService $excelService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->excelService = new StockExcelService();
    }

    public function test_export_generates_valid_streamed_xlsx_response(): void
    {
        $user = User::factory()->create();
        Stock::factory()->count(2)->create(['user_id' => $user->id]);

        $stocks = Stock::where('user_id', $user->id)->get();

        $response = $this->excelService->export($stocks);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertNotEmpty($content);
    }

    public function test_import_with_10_columns_including_description(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['No', 'Name', 'SKU', 'Description', 'Quantity', 'Unit', 'Price', 'Min Stock', 'Status', 'Created At'],
            [1, 'Buku Tulis', 'BK-001', 'Kertas bergaris', 50, 'pcs', 5000, 5, 'Tersedia', '2026-11-01 10:00:00'],
            [2, 'Pensil 2B', 'PS-002', 'Pensil hitam', 0, 'pcs', 2000, 5, 'Habis', '2026-11-01 10:00:00'],
            [3, 'Penghapus', 'ER-003', 'Penghapus karet', 3, 'pcs', 1000, 5, 'Stok Menipis', '2026-11-01 10:00:00'],
            [4, '', '', '', '', '', '', '', '', ''], // empty row
        ]);

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test_stock_10col_' . uniqid() . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        try {
            $parsed = $this->excelService->import($tempPath);

            $this->assertCount(3, $parsed);

            $this->assertSame('Buku Tulis', $parsed[0]['name']);
            $this->assertSame('BK-001', $parsed[0]['sku']);
            $this->assertSame('Kertas bergaris', $parsed[0]['description']);
            $this->assertSame(50, $parsed[0]['quantity']);
            $this->assertSame(StockStatus::AVAILABLE->value, $parsed[0]['status']);

            $this->assertSame('Pensil 2B', $parsed[1]['name']);
            $this->assertSame(0, $parsed[1]['quantity']);
            $this->assertSame(StockStatus::OUT_OF_STOCK->value, $parsed[1]['status']);

            $this->assertSame('Penghapus', $parsed[2]['name']);
            $this->assertSame(3, $parsed[2]['quantity']);
            $this->assertSame(StockStatus::LOW_STOCK->value, $parsed[2]['status']);
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    public function test_import_with_legacy_8_columns(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['No', 'Name', 'SKU', 'Quantity', 'Unit', 'Price', 'Min Stock', 'Status'],
            [1, 'Spidol', 'SP-001', 25, 'pcs', 8000, 5, 'available'],
            [2, 'Penggaris', 'PG-002', 1, 'pcs', 3000, 5, 'low_stock'],
        ]);

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test_stock_8col_' . uniqid() . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        try {
            $parsed = $this->excelService->import($tempPath);

            $this->assertCount(2, $parsed);
            $this->assertSame('Spidol', $parsed[0]['name']);
            $this->assertSame('', $parsed[0]['description']);
            $this->assertSame(25, $parsed[0]['quantity']);
            $this->assertSame(StockStatus::AVAILABLE->value, $parsed[0]['status']);

            $this->assertSame('Penggaris', $parsed[1]['name']);
            $this->assertSame(1, $parsed[1]['quantity']);
            $this->assertSame(StockStatus::LOW_STOCK->value, $parsed[1]['status']);
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    public function test_import_empty_file(): void
    {
        $spreadsheet = new Spreadsheet();
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test_empty_' . uniqid() . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        try {
            $parsed = $this->excelService->import($tempPath);
            $this->assertSame([], $parsed);
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }
}
