<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable as IlluminateAuthenticatable;
use WayOfDev\Auth\Providers\TokenFootprint;

interface Authenticatable extends IlluminateAuthenticatable
{
    public function getRealm(): string;

    public function getAuthorizedParty(): string;

    public function getFootprint(): TokenFootprint;
}
