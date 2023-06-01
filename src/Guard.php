<?php

declare(strict_types=1);

namespace WayOfDev\Auth;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use WayOfDev\Auth\Bridge\Laravel\Events\TokenAuthenticated;
use WayOfDev\Auth\Contracts\UserFactory;

final class Guard
{
    protected AuthFactory $auth;

    protected UserFactory $userFactory;

    protected Dispatcher $eventDispatcher;

    public function __construct(AuthFactory $auth, UserFactory $userFactory, Dispatcher $eventDispatcher)
    {
        $this->auth = $auth;
        $this->userFactory = $userFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Request $request): ?Contracts\Authenticatable
    {
        $bearerToken = $request->bearerToken();

        if (null !== $bearerToken && $this->userFactory->supports($bearerToken)) {
            $user = $this->userFactory->createUser($bearerToken);

            $this->eventDispatcher->dispatch(new TokenAuthenticated($bearerToken));

            return $user;
        }

        return null;
    }
}
