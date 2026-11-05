<?php

declare(strict_types=1);

namespace Tests\Unit;

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

    public function test_export_generates_streamed_response(): void
    {
        $user = User::factory()->create();
        Stock::factory()->count(2)->create(['user_id' => $user->id]);

        $stocks = Stock::where('user_id', $user->id)->get();

        $response = $this->excelService->export($stocks);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));

        // Capture streamed output
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertNotEmpty($content);
    }

    public function test_import_parses_excel_file_correctly(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Write header
        $sheet->fromArray([
            ['No', 'Name', 'SKU', 'Quantity', 'Unit', 'Price', 'Min Stock', 'Status'],
            [1, 'Buku Tulis', 'BK-001', 50, 'pcs', 5000, 5, 'Tersedia'],
            [2, 'Pensil Habis', 'PS-002', 0, 'pcs', 2000, 5, 'Habis'],
            [3, 'Penghapus Low', 'ER-003', 3, 'pcs', 1000, 5, 'Stok Menipis'],
            [4, '', '', '', '', '', '', ''], // Empty row should be skipped
        ]);

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test_stock_import_' . uniqid() . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        try {
            $parsed = $this->excelService->import($tempPath);

            $this->assertCount(3, $parsed);

            $this->assertSame('Buku Tulis', $parsed[0]['name']);
            $this->assertSame(50, $parsed[0]['quantity']);
            $this->assertSame(StockStatus::Available->value, $parsed[0]['status']);

            $this->assertSame('Pensil Habis', $parsed[1]['name']);
            $this->assertSame(0, $parsed[1]['quantity']);
            $this->assertSame(StockStatus::OutOfStock->value, $parsed[1]['status']);

            $this->assertSame('Penghapus Low', $parsed[2]['name']);
            $this->assertSame(3, $parsed[2]['quantity']);
            $this->assertSame(StockStatus::LowStock->value, $parsed[2]['status']);
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }
}

