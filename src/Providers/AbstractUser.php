<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Providers;

use WayOfDev\Auth\Contracts\Authenticatable;

abstract class AbstractUser implements Authenticatable
{
    public function getFootprint(): TokenFootprint
    {
        return TokenFootprint::fromUser($this);
    }
}
