<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Providers\Oidc;

use Exception;
use JsonException;
use WayOfDev\Auth\Contracts\Authenticatable;
use WayOfDev\Auth\Contracts\UserFactory as Factory;

use function base64_decode;
use function in_array;
use function json_decode;

final class UserFactory implements Factory
{
    private array $allowedTokenTypes;

    public function __construct(array $allowedTokenTypes)
    {
        $this->allowedTokenTypes = $allowedTokenTypes;
    }

    public function supports(string $identifier): bool
    {
        try {
            $attributes = $this->decodeIdentifier($identifier);

            return in_array($attributes['typ'] ?? '', $this->allowedTokenTypes, true);
        } catch (Exception|JsonException $e) {
            return false;
        }
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function createUser(string $identifier): Authenticatable
    {
        try {
            $attributes = $this->decodeIdentifier($identifier);

            return User::create($attributes);
        } catch (Exception $e) {
            throw new Exception('Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    private function decodeIdentifier(string $identifier): array
    {
        $json = base64_decode($identifier, true);
        if (false === $json) {
            throw new Exception('Error decoding identifier');
        }

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
