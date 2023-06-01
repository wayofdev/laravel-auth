<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Tests\Providers\Oidc;

use JsonException;
use WayOfDev\Auth\Providers\Oidc\User;
use WayOfDev\Auth\Providers\Oidc\UserFactory;
use WayOfDev\Auth\Tests\TestCase;

class UserFactoryTest extends TestCase
{
    private UserFactory $userFactory;

    private string $identifier;

    /**
     * @throws JsonException
     */
    public function setUp(): void
    {
        parent::setUp();

        $allowedTokenTypes = ['Bearer'];
        $this->userFactory = new UserFactory($allowedTokenTypes);

        $this->identifier = $this->encode(['typ' => 'Bearer']);
    }

    /**
     * @test
     */
    public function it_supports_valid_identifier(): void
    {
        $this::assertTrue($this->userFactory->supports($this->identifier));
    }

    public function it_does_not_support_invalid_base64_encoded_string(): void
    {
        self::assertFalse($this->userFactory->supports('"'));
    }

    /**
     * @test
     */
    public function it_does_not_support_invalid_json(): void
    {
        self::assertFalse($this->userFactory->supports('IQ=='));
    }

    /**
     * @test
     *
     * @throws JsonException
     */
    public function it_does_not_support_invalid_token_type(): void
    {
        $identifier = $this->encode(['typ' => 'Offline']);

        self::assertFalse($this->userFactory->supports($identifier));
    }

    /**
     * @test
     *
     * @throws JsonException
     */
    public function it_supports_valid_json(): void
    {
        $identifier = $this->encode(['typ' => 'Bearer']);

        self::assertTrue($this->userFactory->supports($identifier));
    }

    /**
     * @test
     *
     * @throws JsonException
     */
    public function it_creates_user(): void
    {
        $data = [
            'jti' => 'b82b82ba-37dd-4f5a-b0db-af71a8200fcc',
            'iss' => 'https://auth.wayof.dev:8080/auth/realms/customers',
            'aud' => 'account',
            'sub' => 'b461ddf1-5334-48ca-be20-32902e27e248',
            'typ' => 'Bearer',
            'azp' => 'frontend-app',
            'acr' => '1',
            'realm_access' => [
                'roles' => [0 => 'offline_access', 1 => 'uma_authorization'],
            ],
            'scope' => 'email profile',
            'email_verified' => true,
            'name' => 'FooName FooSurname',
            'preferred_username' => 'test',
            'given_name' => 'John',
            'family_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ];

        $identifier = $this->encode($data);

        $user = $this->userFactory->createUser($identifier);

        $this::assertInstanceOf(User::class, $user);
        self::assertEquals('b461ddf1-5334-48ca-be20-32902e27e248', $user->getAuthIdentifier());
        self::assertEquals('test', $user->getUsername());
        self::assertEquals('john.doe@example.com', $user->getEmail());
        self::assertEquals(['ROLE_OFFLINE_ACCESS', 'ROLE_UMA_AUTHORIZATION', 'ROLE_USER'], $user->getRoles());
        self::assertEquals(['email', 'profile'], $user->getScopes());
        self::assertEquals('John', $user->getFirstName());
        self::assertEquals('Doe', $user->getLastName());
        self::assertEquals('customers', $user->getRealm());
        self::assertEquals('frontend-app', $user->getAuthorizedParty());

        $footprint = $user->getFootprint();
        self::assertEquals('b461ddf1-5334-48ca-be20-32902e27e248', $footprint->getIdentity());
        self::assertEquals('customers', $footprint->getRealm());
        self::assertEquals('frontend-app', $footprint->getParty());
    }

    public function it_throws_exception_when_creating_user_with_invalid_identifier(): void
    {
        $this->expectException(JsonException::class);
        $this->userFactory->createUser('invalid identifier');
    }
}
