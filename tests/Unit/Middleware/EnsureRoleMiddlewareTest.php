<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Http\Middleware\EnsureRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureRoleMiddlewareTest extends TestCase
{
    private EnsureRole $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new EnsureRole();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $request = Request::create('/admin/users', 'GET');

        $response = $this->middleware->handle($request, function () {
            $this->fail('Next closure should not be called');
        }, 'admin');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(route('login'), $response->getTargetUrl());
    }

    public function test_user_with_incorrect_role_aborts_403(): void
    {
        $this->expectException(HttpException::class);

        $user = new User(['role' => 'member']);
        $request = Request::create('/admin/users', 'GET');
        $request->setUserResolver(fn () => $user);

        try {
            $this->middleware->handle($request, function () {}, 'admin');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
            throw $e;
        }
    }

    public function test_user_with_correct_role_passes(): void
    {
        $user = new User(['role' => 'admin']);
        $request = Request::create('/admin/users', 'GET');
        $request->setUserResolver(fn () => $user);

        $called = false;
        $response = $this->middleware->handle($request, function ($req) use (&$called) {
            $called = true;
            return new Response('OK');
        }, 'admin');

        $this->assertTrue($called);
        $this->assertSame('OK', $response->getContent());
    }
}
