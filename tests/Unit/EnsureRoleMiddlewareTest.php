<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Middleware\EnsureRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureRoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private EnsureRole $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new EnsureRole();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $request  = Request::create('/stocks', 'GET');
        $response = $this->middleware->handle($request, fn () => new Response(), 'member');

        $this->assertTrue($response->isRedirection());
        $this->assertSame(route('login'), $response->headers->get('Location'));
    }

    public function test_user_with_wrong_role_is_aborted_403(): void
    {
        $this->expectException(HttpException::class);

        $member  = User::factory()->member()->make();
        $request = Request::create('/admin/users', 'GET');
        $request->setUserResolver(fn () => $member);

        $this->middleware->handle($request, fn () => new Response(), 'admin');
    }

    public function test_user_with_correct_role_passes(): void
    {
        $member  = User::factory()->member()->make();
        $request = Request::create('/stocks', 'GET');
        $request->setUserResolver(fn () => $member);

        $response = $this->middleware->handle($request, fn () => new Response('OK'), 'member');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getContent());
    }
}

