<?php

namespace App\Http\Controllers;

use App\Http\JWT\JWT;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class AuthController extends Controller
{
    public function __construct(private readonly User $user, private readonly Factory $auth, private readonly JWT $jwt)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return response()->json(
            $this->jwt->authenticate($this->user->create($request->validated())),
            Response::HTTP_CREATED,
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = $this->user->where(['email' => $credentials['email']])->first();

        if (is_null($user) || !$this->auth->validate($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($this->jwt->authenticate($user), Response::HTTP_OK);
    }

    public function logout()
    {
        $this->auth->logout();

        return response()->json(Response::HTTP_NO_CONTENT);
    }
}
