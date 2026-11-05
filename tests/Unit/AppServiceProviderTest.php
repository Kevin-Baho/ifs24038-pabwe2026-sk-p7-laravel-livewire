<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Stock\Contracts\StockRepositoryInterface;
use App\Features\Stock\Repositories\StockRepository;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_bindings_and_boot(): void
    {
        $provider = new AppServiceProvider($this->app);
        $provider->register();

        $this->assertInstanceOf(StockRepository::class, $this->app->make(StockRepositoryInterface::class));

        // Test boot with force_https config
        config(['app.force_https' => true]);
        $provider->boot();
        $this->assertStringStartsWith('https://', URL::to('/'));
    }
}
