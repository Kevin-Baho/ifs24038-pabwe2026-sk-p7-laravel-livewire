<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Stock\Contracts\StockRepositoryInterface;
use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Services\StockService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StockService::class);
    }

    public function test_service_delegations(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        // Create with cover
        $cover = UploadedFile::fake()->image('cover.jpg');
        $stock = $this->service->create($user->id, [
            'name'      => 'Buku',
            'quantity'  => 10,
            'unit'      => 'pcs',
            'min_stock' => 5,
        ], $cover);

        $this->assertNotNull($stock->cover_image_path);
        Storage::disk('public')->assertExists($stock->cover_image_path);

        // List, All, Find
        $list = $this->service->listForUser($user->id);
        $this->assertSame(1, $list->total());

        $all = $this->service->allForUser($user->id);
        $this->assertCount(1, $all);

        $found = $this->service->findForUser($user->id, $stock->id);
        $this->assertSame($stock->id, $found->id);

        $adminAll = $this->service->allForAdmin($user->id);
        $this->assertCount(1, $adminAll);

        // Update with new cover
        $newCover = UploadedFile::fake()->image('new.jpg');
        $updated = $this->service->update($stock, ['name' => 'Buku Gambar'], $newCover);
        $this->assertSame('Buku Gambar', $updated->name);

        // Increment & Decrement
        $inc = $this->service->increment($updated);
        $this->assertSame(11, $inc->quantity);

        $dec = $this->service->decrement($inc);
        $this->assertSame(10, $dec->quantity);

        // Delete
        $this->service->delete($dec);
        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }
}

