<?php

namespace App\Http\JWT;

use App\Models\User;
use Namshi\JOSE\JWS;

class JWT
{
    public function authenticate(User $user): array
    {
        $service = new TokenService($user);

        $token = $service->token();
        $payload = JWS::load($token)->getPayload();

        return [
            'id' => $payload['jti'],
            'expires_at' => $payload['exp'],
            'token' => $token,
        ];
    }
}
