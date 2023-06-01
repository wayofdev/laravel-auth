<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Providers\Bearer;

use JsonException;
use WayOfDev\Auth\Contracts\Authenticatable;
use WayOfDev\Auth\Contracts\UserFactory as Factory;
use WayOfDev\Auth\Providers\Oidc\User;

use function base64_decode;
use function explode;
use function json_decode;
use function substr_count;

final class UserFactory implements Factory
{
    public function supports(string $identifier): bool
    {
        return null !== $this->extractPayload($identifier);
    }

    public function createUser(string $identifier): Authenticatable
    {
        return User::create($this->extractPayload($identifier) ?? []);
    }

    private function extractPayload(string $jwt): ?array
    {
        // Format: <header>.<payload>.<signature>
        if (2 !== substr_count($jwt, '.')) {
            return null;
        }

        $payload = explode('.', $jwt)[1];

        if (false === $json = base64_decode($payload, true)) {
            return null;
        }

        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return null;
        }
    }
}
