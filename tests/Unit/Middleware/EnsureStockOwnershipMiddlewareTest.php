<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Features\Stock\Models\Stock;
use App\Http\Middleware\EnsureStockOwnership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
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
        $request = Request::create('/stocks/1', 'GET');

        $response = $this->middleware->handle($request, function () {
            $this->fail('Should not be called');
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(route('login'), $response->getTargetUrl());
    }

    public function test_admin_bypasses_ownership_check(): void
    {
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->member()->create();
        $stock = Stock::factory()->create(['user_id' => $otherUser->id]);

        $request = Request::create('/stocks/' . $stock->id, 'GET');
        $request->setUserResolver(fn () => $admin);

        $route = (new Route('GET', '/stocks/{stock}', []))->bind($request);
        $route->setParameter('stock', $stock);
        $request->setRouteResolver(fn () => $route);

        $called = false;
        $response = $this->middleware->handle($request, function () use (&$called) {
            $called = true;
            return new Response('OK');
        });

        $this->assertTrue($called);
        $this->assertSame('OK', $response->getContent());
    }

    public function test_owner_can_access_own_stock_model_or_numeric_id(): void
    {
        $user = User::factory()->member()->create();
        $stock = Stock::factory()->create(['user_id' => $user->id]);

        // Model parameter
        $request = Request::create('/stocks/' . $stock->id, 'GET');
        $request->setUserResolver(fn () => $user);

        $route = (new Route('GET', '/stocks/{stock}', []))->bind($request);
        $route->setParameter('stock', $stock);
        $request->setRouteResolver(fn () => $route);

        $called = false;
        $response = $this->middleware->handle($request, function () use (&$called) {
            $called = true;
            return new Response('OK');
        });
        $this->assertTrue($called);

        // Numeric id parameter
        $requestNumeric = Request::create('/stocks/' . $stock->id, 'GET');
        $requestNumeric->setUserResolver(fn () => $user);

        $routeNumeric = (new Route('GET', '/stocks/{stock}', []))->bind($requestNumeric);
        $routeNumeric->setParameter('stock', (string) $stock->id);
        $requestNumeric->setRouteResolver(fn () => $routeNumeric);

        $calledNumeric = false;
        $responseNumeric = $this->middleware->handle($requestNumeric, function () use (&$calledNumeric) {
            $calledNumeric = true;
            return new Response('OK');
        });
        $this->assertTrue($calledNumeric);
    }

    public function test_non_owner_is_forbidden(): void
    {
        $this->expectException(HttpException::class);

        $user = User::factory()->member()->create();
        $otherUser = User::factory()->member()->create();
        $stock = Stock::factory()->create(['user_id' => $otherUser->id]);

        $request = Request::create('/stocks/' . $stock->id, 'GET');
        $request->setUserResolver(fn () => $user);

        $route = (new Route('GET', '/stocks/{stock}', []))->bind($request);
        $route->setParameter('stock', $stock);
        $request->setRouteResolver(fn () => $route);

        try {
            $this->middleware->handle($request, function () {});
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
            throw $e;
        }
    }

    public function test_route_without_stock_passes(): void
    {
        $user = User::factory()->member()->create();
        $request = Request::create('/other', 'GET');
        $request->setUserResolver(fn () => $user);

        $route = (new Route('GET', '/other', []))->bind($request);
        $request->setRouteResolver(fn () => $route);

        $called = false;
        $response = $this->middleware->handle($request, function () use (&$called) {
            $called = true;
            return new Response('OK');
        });

        $this->assertTrue($called);
    }
}
