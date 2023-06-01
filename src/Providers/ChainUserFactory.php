<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Providers;

use WayOfDev\Auth\Contracts\Authenticatable;
use WayOfDev\Auth\Contracts\UserFactory;
use WayOfDev\Auth\Exceptions\UserNotCreatableException;

final class ChainUserFactory implements UserFactory
{
    /**
     * @var UserFactory[]
     */
    private array $factories;

    /**
     * @param UserFactory[] $factories
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    public function supports(string $identifier): bool
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($identifier)) {
                return true;
            }
        }

        return false;
    }

    public function createUser(string $identifier): Authenticatable
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($identifier)) {
                return $factory->createUser($identifier);
            }
        }

        throw new UserNotCreatableException('No suitable factory found to create User.');
    }
}
