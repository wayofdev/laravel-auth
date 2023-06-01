<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Tests\Bridge\Laravel\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;

    public function secret(Request $request)
    {
        return 'protected';
    }

    public function public(Request $request)
    {
        return 'public';
    }
}
