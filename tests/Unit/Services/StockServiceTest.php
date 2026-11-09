<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

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

    public function test_service_crud_and_cover_handling(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        // Create with cover image
        $cover = UploadedFile::fake()->image('cover.jpg');
        $stock = $this->service->create($user->id, [
            'name'      => 'Laptop Dell',
            'quantity'  => 10,
            'unit'      => 'unit',
            'price'     => 12000000,
            'min_stock' => 5,
        ], $cover);

        $this->assertNotNull($stock->cover_image_path);
        Storage::disk('public')->assertExists($stock->cover_image_path);

        // List, all, find, allForAdmin
        $list = $this->service->listForUser($user->id);
        $this->assertSame(1, $list->total());

        $all = $this->service->allForUser($user->id);
        $this->assertCount(1, $all);

        $found = $this->service->findForUser($user->id, $stock->id);
        $this->assertSame($stock->id, $found->id);

        $adminAll = $this->service->allForAdmin($user->id);
        $this->assertCount(1, $adminAll);

        // Update with new cover (old cover should be deleted)
        $oldPath = $stock->cover_image_path;
        $newCover = UploadedFile::fake()->image('new_cover.jpg');
        $updated = $this->service->update($stock, ['name' => 'Laptop Dell XPS'], $newCover);

        $this->assertSame('Laptop Dell XPS', $updated->name);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($updated->cover_image_path);

        // Update without cover
        $updatedNoCover = $this->service->update($updated, ['quantity' => 12]);
        $this->assertSame(12, $updatedNoCover->quantity);

        // Adjust quantity, increment, decrement
        $adjusted = $this->service->adjustQuantity($updatedNoCover, 3);
        $this->assertSame(15, $adjusted->quantity);

        $inc = $this->service->increment($adjusted);
        $this->assertSame(16, $inc->quantity);

        $dec = $this->service->decrement($inc);
        $this->assertSame(15, $dec->quantity);

        // Delete (should delete cover from storage as well)
        $currentCover = $dec->cover_image_path;
        $this->service->delete($dec);
        Storage::disk('public')->assertMissing($currentCover);
        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);

        // Delete stock without cover
        $stockNoCover = $this->service->create($user->id, ['name' => 'Kabel USB']);
        $this->service->delete($stockNoCover);
        $this->assertDatabaseMissing('stocks', ['id' => $stockNoCover->id]);
    }
}
