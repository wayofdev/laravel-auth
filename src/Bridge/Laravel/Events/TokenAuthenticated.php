<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Bridge\Laravel\Events;

class TokenAuthenticated
{
    /**
     * The personal access token that was authenticated.
     */
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}
