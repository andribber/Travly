<?php

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;
    use WithFaker;

    public function setAuthenticatedUser($user, $guard = null): self
    {
        $token = auth()->login($user);

        $this->withHeader('Authorization', "Bearer {$token}");

        return $this;
    }
}
