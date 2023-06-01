<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Providers;

trait GenericUser
{
    public function getAuthPassword(): string
    {
        return '';
    }

    public function getRememberToken(): string
    {
        return '';
    }

    public function setRememberToken($value): string
    {
        return '';
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}
