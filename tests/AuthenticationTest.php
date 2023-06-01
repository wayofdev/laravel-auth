<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Tests;

use Illuminate\Support\Facades\Auth;
use WayOfDev\Auth\Providers\Oidc\User;

class AuthenticationTest extends TestCase
{
    public static function provideHttpMethods(): array
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideHttpMethods
     */
    public function it_authenticates_user_when_requesting_a_private_endpoint_with_token($httpMethod): void
    {
        $this->withKeycloakToken()->json($httpMethod, '/foo/secret');
        /** @var User $user */
        $user = Auth::user();

        // dd($user);

        $this::assertEquals('ben.gue', $user->getUsername());
    }

    /**
     * @test
     */
    public function it_authenticates_user_when_requesting_an_public_endpoint_with_token(): void
    {
        $this->withKeycloakToken()->json('GET', '/foo/public');
        /** @var User $user */
        $user = Auth::user();
        $this::assertEquals('ben.gue', $user->getUsername());
    }

    /**
     * @test
     */
    public function it_throws_forbidden_when_requesting_protected_endpoint_without_token(): void
    {
        $response = $this->json('GET', '/foo/secret');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function laravel_default_interface_works_for_authenticated_users(): void
    {
        $this->withKeycloakToken()->json('GET', '/foo/secret');

        $this::assertTrue(Auth::hasUser());
        $this::assertFalse(Auth::guest());
        $this::assertEquals('840fd512-0f37-4896-bc19-a4638c3c00f6', Auth::id());
    }

    /**
     * @test
     */
    public function laravel_default_interface_works_for_non_authenticated_users(): void
    {
        $this->json('GET', '/foo/public');

        $this::assertFalse(Auth::hasUser());
        $this::assertTrue(Auth::guest());
        $this::assertNull(Auth::id());
    }
}
