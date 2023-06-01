<?php

declare(strict_types=1);

namespace WayOfDev\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

final class ChainProvider implements UserProvider
{
    public function retrieveById($identifier): ?Contracts\Authenticatable
    {
        return null;
    }

    public function retrieveByToken($identifier, $token): ?Contracts\Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        // @todo implement
    }

    public function retrieveByCredentials(array $credentials): ?Contracts\Authenticatable
    {
        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return false;
    }
}
