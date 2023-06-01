<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Providers;

enum JWTClaim: string
{
    case SUBJECT = 'sub';
    case ISSUER = 'iss';
    case AUTHORIZED_PARTY = 'azp';
}
