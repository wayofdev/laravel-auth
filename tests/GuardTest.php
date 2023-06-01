<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Tests;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use WayOfDev\Auth\Providers\Oidc\User;

final class GuardTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_get_user_from_bearer_token(): void
    {
        $factory = $this->app->make(AuthFactory::class);
        $requestGuard = $factory->guard('api');

        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJmWG5HcUIxTWMyNXczWlhzMV9JYXVHTE9JdmZaWVR0X0JBakZuYTExYzBVIn0.eyJleHAiOjE2MjI3NDY2NDEsImlhdCI6MTYyMjc0NjM0MSwianRpIjoiOTEzMTNkODEtNzFjNi00MTk0LWJhYTEtZmI3NjkxMGZlYWMzIiwiaXNzIjoiaHR0cDovL2F1dGgubXEuZGV2LmRvY2tlci9hdXRoL3JlYWxtcy9mcm9udC1vZmZpY2UiLCJhdWQiOiJhY2NvdW50Iiwic3ViIjoiODQwZmQ1MTItMGYzNy00ODk2LWJjMTktYTQ2MzhjM2MwMGY2IiwidHlwIjoiQmVhcmVyIiwiYXpwIjoidXNlciIsInNlc3Npb25fc3RhdGUiOiI4NTk5ZmQ2Yi01ZDU3LTQxNzItOTcyMy0yNDEzZjQ5N2EyZWYiLCJhY3IiOiIxIiwiYWxsb3dlZC1vcmlnaW5zIjpbIioiXSwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbIm9mZmxpbmVfYWNjZXNzIiwidW1hX2F1dGhvcml6YXRpb24iXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6ImVtYWlsIHByb2ZpbGUiLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6IkpvaG4gRG9lIiwicHJlZmVycmVkX3VzZXJuYW1lIjoiYmVuLmd1ZSIsImdpdmVuX25hbWUiOiJKb2huIiwiZmFtaWx5X25hbWUiOiJEb2UiLCJlbWFpbCI6ImJlbi5ndWVAZm9vLmJhciJ9.Z7dhx7kDNEp7ahc2gLtnNdJX2QnWG0nF0mX8P94zdI3Q5-JoiXJtbzTroypAIbAxv4B3ItHAM4a0y6iR7htRDxIABne_0iD18mRotGFeQuGdIVZCKo7zcVry6bgjAcMHY-Qx_6kpF6hShZvWRT3au2pTUXiprSdPWHbTwWrm0Phd4ugsLVeqh_vbhYf9p4oEZEZdf4TrPP-759RCl7OCyOZuXPOdM3hHwktPkbgsEjyhMZN4_dALQv0ndbYHym5D4X4K1MPHZ3hrOOAmaqlF7SPiS5eJatE_468X4v99k3EQk5J4zikQ8psQlc_Pk5t8wtXcITBnnJ_Wn5A9e4BvmQ');

        /** @var User $returnedUser */
        // @phpstan-ignore-next-line
        $returnedUser = $requestGuard->setRequest($request)->user();

        // dd($returnedUser);

        self::assertEquals('840fd512-0f37-4896-bc19-a4638c3c00f6', $returnedUser->getAuthIdentifier());
        self::assertEquals('ben.gue@foo.bar', $returnedUser->getEmail());
    }
}
