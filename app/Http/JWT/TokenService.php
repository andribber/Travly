<?php

namespace App\Http\JWT;

use App\Models\User;

class TokenService
{
    public function __construct(
        private readonly User $user,
        private ?int $ttl = null,
        private readonly bool $refresh = false,
    ) {
        $this->ttl = is_null($ttl) ? config('jwt.ttl') : $ttl;
    }

    public function token(): string
    {
        $token = auth()->setTTL($this->ttl)->claims(['user_id' => $this->user->id]);

        if ($this->refresh) {
            return $token->refresh();
        }

        return $token->login($this->user);
    }
}
