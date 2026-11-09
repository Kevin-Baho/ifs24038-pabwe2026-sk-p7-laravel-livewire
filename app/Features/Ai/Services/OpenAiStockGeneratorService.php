<?php

declare(strict_types=1);

namespace App\Features\Ai\Services;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Ai\DTO\GeneratedStockItem;
use RuntimeException;

class OpenAiStockGeneratorService implements AiClientInterface
{
    public function __construct(
        private readonly ?AiClientInterface $client = null,
    ) {}

    public function complete(string $prompt, int $maxTokens = 2048): string
    {
        if ($this->client !== null) {
            return $this->client->complete($prompt, $maxTokens);
        }

        return '';
    }

    /**
     * Generate stock items for the given theme/category.
     *
     * @return GeneratedStockItem[]
     */
    public function generate(string $theme, int $count = 5): array
    {
        $prompt = $this->buildPrompt($theme, $count);
        $raw    = $this->complete($prompt);

        return $this->parseResponse($raw);
    }

    private function buildPrompt(string $theme, int $count): string
    {
        return <<<PROMPT
Kamu adalah asisten inventaris. Buatkan {$count} item stok untuk kategori: "{$theme}".

Jawab HANYA dalam format JSON array berikut (tanpa teks lain):
[
  {
    "name": "Nama Produk",
    "sku": "SKU-001",
    "description": "Deskripsi singkat dalam Markdown",
    "quantity": 50,
    "unit": "pcs",
    "price": 15000,
    "min_stock": 5
  }
]
PROMPT;
    }

    /**
     * @return GeneratedStockItem[]
     * @throws RuntimeException
     */
    private function parseResponse(string $raw): array
    {
        // Extract JSON from possible markdown code blocks
        $json = preg_replace('/^```(?:json)?\s*/m', '', $raw);
        $json = preg_replace('/^```\s*/m', '', (string) $json);
        $json = trim((string) $json);

        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('AI response could not be parsed as JSON array.');
        }

        return array_map(
            fn (array $item): GeneratedStockItem => GeneratedStockItem::fromArray($item),
            $decoded,
        );
    }
}
