<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Providers;

use WayOfDev\Auth\Contracts\Authenticatable;

use function get_object_vars;

final class TokenFootprint
{
    private string $id;

    private string $party;

    private string $realm;

    public static function fromUser(Authenticatable $user): self
    {
        return new self(
            $user->getAuthIdentifier(),
            $user->getAuthorizedParty(),
            $user->getRealm()
        );
    }

    public function __construct(string $id, string $party, string $realm)
    {
        $this->id = $id;
        $this->party = $party;
        $this->realm = $realm;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }

    public function getParty(): string
    {
        return $this->party;
    }

    public function getRealm(): string
    {
        return $this->realm;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
