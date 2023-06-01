<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Providers\Oidc;

use WayOfDev\Auth\Providers\AbstractUser;
use WayOfDev\Auth\Providers\GenericUser;
use WayOfDev\Auth\Providers\JWTClaim;

use function array_map;
use function array_push;
use function end;
use function explode;
use function str_replace;
use function strtoupper;

class User extends AbstractUser
{
    use GenericUser;

    private const DEFAULT_ROLE = 'ROLE_USER';

    private array $attributes;

    private ?array $roles = null;

    private ?array $scopes = null;

    public static function create(array $attributes): self
    {
        return new self($attributes);
    }

    public function getUsername(): string
    {
        return $this->getPreferredUsername();
    }

    public function getGroups(): array
    {
        return $this->attributes['groups'] ?? [];
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getFirstName(): ?string
    {
        return $this->attributes['given_name'] ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->attributes['family_name'] ?? null;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAuthIdentifier()
    {
        return $this->attributes[$this->getAuthIdentifierName()];
    }

    public function getRealm(): string
    {
        $parts = explode('/', $this->attributes[JWTClaim::ISSUER->value]);

        return end($parts);
    }

    public function getAuthIdentifierName(): string
    {
        return JWTClaim::SUBJECT->value;
    }

    public function getAuthorizedParty(): string
    {
        return $this->attributes[JWTClaim::AUTHORIZED_PARTY->value];
    }

    public function getPreferredUsername(): string
    {
        return $this->attributes['preferred_username'];
    }

    public function getEmail(): ?string
    {
        return $this->attributes['email'] ?? null;
    }

    private function __construct(array $attributes)
    {
        $this->attributes = $attributes;

        $this->roles = $this->extractRoles($attributes);
        $this->scopes = $this->extractScopes($attributes);
    }

    private function roleFactory(string $role): string
    {
        return 'ROLE_' . strtoupper(str_replace('-', '_', $role));
    }

    private function extractRoles(array $attributes): array
    {
        $roles = $attributes['realm_access']['roles'] ?? [];
        array_push($roles, ...$attributes['roles'] ?? []);
        $roles = array_map([$this, 'roleFactory'], $roles);

        $roles[] = self::DEFAULT_ROLE;

        return $roles;
    }

    private function extractScopes(array $attributes): array
    {
        return explode(' ', $attributes['scope'] ?? '');
    }
}
