<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Stock\Models\Stock;
use App\Http\Middleware\EnsureStockOwnership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureStockOwnershipMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private EnsureStockOwnership $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new EnsureStockOwnership();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $request  = Request::create('/stocks/1', 'GET');
        $response = $this->middleware->handle($request, fn () => new Response());

        $this->assertTrue($response->isRedirection());
        $this->assertSame(route('login'), $response->headers->get('Location'));
    }

    public function test_admin_bypasses_ownership(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->member()->create();
        $stock = Stock::factory()->create(['user_id' => $other->id]);

        $request = Request::create('/stocks/' . $stock->id, 'GET');
        $request->setUserResolver(fn () => $admin);
        $route = new \Illuminate\Routing\Route('GET', '/stocks/{stock}', []);
        $route->bind($request);
        $route->setParameter('stock', $stock);
        $request->setRouteResolver(fn () => $route);

        $response = $this->middleware->handle($request, fn () => new Response('OK'));
        $this->assertSame('OK', $response->getContent());
    }

    public function test_member_owner_passes(): void
    {
        $member = User::factory()->member()->create();
        $stock  = Stock::factory()->create(['user_id' => $member->id]);

        $request = Request::create('/stocks/' . $stock->id, 'GET');
        $request->setUserResolver(fn () => $member);
        $request->setRouteResolver(fn () => new class($stock) {
            public function __construct(private Stock $stock) {}
            public function parameter(string $name) { return $this->stock; }
        });

        // Test with route returning stock object
        $route = new \Illuminate\Routing\Route('GET', '/stocks/{stock}', []);
        $route->bind($request);
        $route->setParameter('stock', $stock);
        $request->setRouteResolver(fn () => $route);

        $response = $this->middleware->handle($request, fn () => new Response('OK'));
        $this->assertSame('OK', $response->getContent());
    }

    public function test_member_with_numeric_stock_param_owned_passes(): void
    {
        $member = User::factory()->member()->create();
        $stock  = Stock::factory()->create(['user_id' => $member->id]);

        $request = Request::create('/stocks/' . $stock->id, 'GET');
        $request->setUserResolver(fn () => $member);

        $route = new \Illuminate\Routing\Route('GET', '/stocks/{stock}', []);
        $route->bind($request);
        $route->setParameter('stock', (string) $stock->id);
        $request->setRouteResolver(fn () => $route);

        $response = $this->middleware->handle($request, fn () => new Response('OK'));
        $this->assertSame('OK', $response->getContent());
    }

    public function test_member_non_owner_aborts_403(): void
    {
        $this->expectException(HttpException::class);

        $member = User::factory()->member()->create();
        $other  = User::factory()->member()->create();
        $stock  = Stock::factory()->create(['user_id' => $other->id]);

        $request = Request::create('/stocks/' . $stock->id, 'GET');
        $request->setUserResolver(fn () => $member);

        $route = new \Illuminate\Routing\Route('GET', '/stocks/{stock}', []);
        $route->bind($request);
        $route->setParameter('stock', $stock);
        $request->setRouteResolver(fn () => $route);

        $this->middleware->handle($request, fn () => new Response('OK'));
    }
}
