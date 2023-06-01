<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Tests;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use JsonException;
use Orchestra\Testbench\TestCase as Orchestra;
use WayOfDev\Auth\Bridge\Laravel\Providers\AuthServiceProvider;
use WayOfDev\Auth\Tests\Bridge\Laravel\Http\Controllers\Controller;

use function base64_encode;
use function json_encode;

abstract class TestCase extends Orchestra
{
    public string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJmWG5HcUIxTWMyNXczWlhzMV9JYXVHTE9JdmZaWVR0X0JBakZuYTExYzBVIn0.eyJleHAiOjE2MjI3NDY2NDEsImlhdCI6MTYyMjc0NjM0MSwianRpIjoiOTEzMTNkODEtNzFjNi00MTk0LWJhYTEtZmI3NjkxMGZlYWMzIiwiaXNzIjoiaHR0cDovL2F1dGgubXEuZGV2LmRvY2tlci9hdXRoL3JlYWxtcy9mcm9udC1vZmZpY2UiLCJhdWQiOiJhY2NvdW50Iiwic3ViIjoiODQwZmQ1MTItMGYzNy00ODk2LWJjMTktYTQ2MzhjM2MwMGY2IiwidHlwIjoiQmVhcmVyIiwiYXpwIjoidXNlciIsInNlc3Npb25fc3RhdGUiOiI4NTk5ZmQ2Yi01ZDU3LTQxNzItOTcyMy0yNDEzZjQ5N2EyZWYiLCJhY3IiOiIxIiwiYWxsb3dlZC1vcmlnaW5zIjpbIioiXSwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIm9mZmxpbmVfYWNjZXNzIiwidW1hX2F1dGhvcml6YXRpb24iXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6ImVtYWlsIHByb2ZpbGUiLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6IkpvaG4gRG9lIiwicHJlZmVycmVkX3VzZXJuYW1lIjoiYmVuLmd1ZSIsImdpdmVuX25hbWUiOiJKb2huIiwiZmFtaWx5X25hbWUiOiJEb2UiLCJlbWFpbCI6ImJlbi5ndWVAZm9vLmJhciJ9.Z7dhx7kDNEp7ahc2gLtnNdJX2QnWG0nF0mX8P94zdI3Q5-JoiXJtbzTroypAIbAxv4B3ItHAM4a0y6iR7htRDxIABne_0iD18mRotGFeQuGdIVZCKo7zcVry6bgjAcMHY-Qx_6kpF6hShZvWRT3au2pTUXiprSdPWHbTwWrm0Phd4ugsLVeqh_vbhYf9p4oEZEZdf4TrPP-759RCl7OCyOZuXPOdM3hHwktPkbgsEjyhMZN4_dALQv0ndbYHym5D4X4K1MPHZ3hrOOAmaqlF7SPiS5eJatE_468X4v99k3EQk5J4zikQ8psQlc_Pk5t8wtXcITBnnJ_Wn5A9e4BvmQ';
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('auth', [
            'defaults' => [
                'guard' => 'api',
            ],
            'guards' => [
                'api' => [
                    'driver' => 'gateway',
                    'provider' => 'users',
                ],
            ],
            'providers' => [
                'users' => [
                    'driver' => 'chain',
                ],
            ],
        ]);
    }

    protected function getPackageProviders($app): array
    {
        Route::any('/foo/secret', Controller::class . '@secret')->middleware(Authenticate::class);
        Route::any('/foo/public', Controller::class . '@public');

        return [
            AuthServiceProvider::class,
        ];
    }

    protected function withKeycloakToken(): static
    {
        $this->withToken($this->token);

        return $this;
    }

    /**
     * @throws JsonException
     */
    protected function encode(array $data = []): string
    {
        return base64_encode(
            json_encode($data, JSON_THROW_ON_ERROR | 0, JSON_THROW_ON_ERROR)
        );
    }
}
