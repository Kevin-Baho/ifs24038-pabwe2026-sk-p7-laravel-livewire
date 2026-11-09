<?php

declare(strict_types=1);

namespace Tests\Unit\Providers;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Ai\Services\OpenAiStockGeneratorService;
use App\Features\Stock\Contracts\StockRepositoryInterface;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Policies\StockPolicy;
use App\Features\Stock\Repositories\StockRepository;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_bindings(): void
    {
        $repo = app(StockRepositoryInterface::class);
        $this->assertInstanceOf(StockRepository::class, $repo);

        $ai = app(AiClientInterface::class);
        $this->assertInstanceOf(OpenAiStockGeneratorService::class, $ai);
    }

    public function test_policies_and_rate_limiters(): void
    {
        $provider = new AppServiceProvider($this->app);
        $provider->register();
        $provider->boot();

        $policy = Gate::getPolicyFor(Stock::class);
        $this->assertInstanceOf(StockPolicy::class, $policy);

        // Rate limiters: 1000-per-second, global, api
        $requestWithUser = Request::create('/', 'GET');
        $user = new User(['id' => 42]);
        $requestWithUser->setUserResolver(fn () => $user);

        $requestWithoutUser = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);

        foreach (['1000-per-second', 'global', 'api'] as $limiterName) {
            $limiterCallback = RateLimiter::limiter($limiterName);
            $this->assertNotNull($limiterCallback, "Limiter {$limiterName} should be registered");

            $limitWithUser = $limiterCallback($requestWithUser);
            $this->assertSame(1000, $limitWithUser->maxAttempts);

            $limitWithoutUser = $limiterCallback($requestWithoutUser);
            $this->assertSame(1000, $limitWithoutUser->maxAttempts);
        }
    }

    public function test_force_https_condition(): void
    {
        config(['app.force_https' => true]);

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $url = URL::to('/test');
        $this->assertStringStartsWith('https://', $url);
    }
}
