<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Auth;

class UserController extends Controller
{
    public function __construct(private readonly Auth $auth)
    {
    }

    public function me(): UserResource
    {
        return UserResource::make($this->auth->user());
    }
}
