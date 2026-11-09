<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Ai\DTO\GeneratedStockItem;
use App\Features\Ai\Services\OpenAiStockGeneratorService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class OpenAiStockGeneratorServiceTest extends TestCase
{
    public function test_generate_parses_json_successfully(): void
    {
        $json = json_encode([
            [
                'name'        => 'Buku Tulis',
                'sku'         => 'BK-001',
                'description' => 'Isi 38 lembar',
                'quantity'    => 50,
                'unit'        => 'pcs',
                'price'       => 5000,
                'min_stock'   => 5,
            ],
            [
                'name'        => 'Penghapus',
                'sku'         => 'ER-001',
                'description' => 'Penghapus pensil',
                'quantity'    => 100,
                'unit'        => 'pcs',
                'price'       => 2000,
                'min_stock'   => 10,
            ],
        ]);

        $client = $this->createStub(AiClientInterface::class);
        $client->method('complete')->willReturn($json);

        $service = new OpenAiStockGeneratorService($client);
        $items   = $service->generate('Alat Tulis');

        $this->assertCount(2, $items);
        $this->assertInstanceOf(GeneratedStockItem::class, $items[0]);
        $this->assertSame('Buku Tulis', $items[0]->name);
        $this->assertSame(50, $items[0]->quantity);
        $this->assertSame(5000.0, $items[0]->price);
    }

    public function test_generate_handles_markdown_code_fences(): void
    {
        $raw = "```json\n" . json_encode([
            [
                'name'        => 'Oli Mesin',
                'sku'         => 'OIL-001',
                'description' => '1 Liter',
                'quantity'    => 10,
                'unit'        => 'botol',
                'price'       => 75000,
                'min_stock'   => 2,
            ],
        ]) . "\n```";

        $client = $this->createStub(AiClientInterface::class);
        $client->method('complete')->willReturn($raw);

        $service = new OpenAiStockGeneratorService($client);
        $items   = $service->generate('Sparepart Motor');

        $this->assertCount(1, $items);
        $this->assertSame('Oli Mesin', $items[0]->name);
    }

    public function test_generate_throws_exception_on_invalid_json(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('AI response could not be parsed as JSON array.');

        $client = $this->createStub(AiClientInterface::class);
        $client->method('complete')->willReturn('This is not JSON at all.');

        $service = new OpenAiStockGeneratorService($client);
        $service->generate('Invalid');
    }

    public function test_complete_delegation_and_fallback(): void
    {
        $client = $this->createStub(AiClientInterface::class);
        $client->method('complete')->willReturn('AI result');

        $service = new OpenAiStockGeneratorService($client);
        $this->assertSame('AI result', $service->complete('prompt'));

        // Without client
        $serviceNoClient = new OpenAiStockGeneratorService(null);
        $this->assertSame('', $serviceNoClient->complete('prompt'));
    }
}
