<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Controller;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Auth;

class UserController extends Controller
{
    public function __construct(private readonly Auth $auth)
    {
    }

    public function me()
    {
        return UserResource::make($this->auth->user());
    }
}
