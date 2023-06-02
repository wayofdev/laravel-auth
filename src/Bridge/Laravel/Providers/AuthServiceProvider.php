<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Bridge\Laravel\Providers;

use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Authenticatable as IlluminateAuthenticatable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use WayOfDev\Auth\ChainProvider;
use WayOfDev\Auth\Contracts\Authenticatable;
use WayOfDev\Auth\Contracts\UserFactory as Factory;
use WayOfDev\Auth\Guard;
use WayOfDev\Auth\Providers\Bearer\UserFactory as BearerUserFactory;
use WayOfDev\Auth\Providers\ChainUserFactory;
use WayOfDev\Auth\Providers\Oidc\UserFactory as OidcUserFactory;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../../../config/gateway.php' => config_path('gateway.php'),
            ], 'config');

            $this->registerConsoleCommands();
        }

        $this->configureGuard();
    }

    public function register(): void
    {
        $this->app->bind(Authenticatable::class, IlluminateAuthenticatable::class);

        $this->app->bind(BearerUserFactory::class, function () {
            return new BearerUserFactory();
        });
        $this->app->bind(OidcUserFactory::class, function (Application $app): OidcUserFactory {
            return new OidcUserFactory(['Bearer']);
        });

        $this->app->bind(Factory::class, function (Application $app): Factory {
            return new ChainUserFactory([
                $app->make(BearerUserFactory::class),
            ]);
        });
    }

    /**
     * @throws BindingResolutionException
     */
    private function configureGuard(): void
    {
        Auth::provider('chain', function ($app, array $config) {
            return $app->make(ChainProvider::class);
        });

        Auth::resolved(function ($auth): void {
            $auth->extend('gateway', function ($app, $name, array $config) use ($auth) {
                return tap($this->createGuard($auth, $config), function ($guard): void {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * @throws BindingResolutionException
     */
    private function createGuard($auth, $config): RequestGuard
    {
        return new RequestGuard(
            new Guard(
                $auth,
                $this->app->make(Factory::class),
                $this->app->make(Dispatcher::class),
            ),
            request(),
            $auth->createUserProvider($config['provider'] ?? null)
        );
    }

    private function registerConsoleCommands(): void
    {
        $this->commands([]);
    }
}
