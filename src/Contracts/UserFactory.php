<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Contracts;

interface UserFactory
{
    public function supports(string $identifier): bool;

    public function createUser(string $identifier): Authenticatable;
}
