<?php

declare(strict_types=1);

namespace App\Providers;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Stock\Contracts\StockRepositoryInterface;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Policies\StockPolicy;
use App\Features\Stock\Repositories\StockRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);
    }

    public function boot(): void
    {
        // Force HTTPS in production or when FORCE_HTTPS=true
        if (
            app()->environment('production')
            || config('app.force_https', false)
            || env('FORCE_HTTPS', false) === true
            || env('FORCE_HTTPS') === 'true'
        ) {
            URL::forceScheme('https');
        }

        // Register policies
        Gate::policy(Stock::class, StockPolicy::class);

        // Register layout hint path
        View::addNamespace('layouts', resource_path('views/components/layouts'));
    }
}
