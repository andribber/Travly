<?php

namespace Tests\Integration\Controllers;

use App\Http\JWT\JWT;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    private readonly User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(now());

        $this->user = User::factory()->create();
    }

    public function test_it_will_register_user(): void
    {
        $name = $this->faker->name;
        $email = $this->faker->email;

        $jwtMock = $this->mock(JWT::class);
        $jwtMock->shouldReceive('authenticate')
            ->once()
            ->andReturn([
                'id' => 123456789,
                'expires_at' => now()->addDays(10)->timestamp,
                'token' => 'testing_token',
            ]);

        $this->app->instance(JWT::class, $jwtMock);

        $this->postJson(route('v1.auth.register', [
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]))
            ->assertCreated()
            ->assertJson([
                'id' => 123456789,
                'expires_at' => now()->addDays(10)->timestamp,
                'token' => 'testing_token',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function test_it_will_login_user(): void
    {
        $jwtMock = $this->mock(JWT::class);
        $jwtMock->shouldReceive('authenticate')
            ->once()
            ->andReturn([
                'id' => 123456789,
                'expires_at' => now()->addDays(10)->timestamp,
                'token' => 'testing_token',
            ]);

        $this->app->instance(JWT::class, $jwtMock);

        $this->postJson(route('v1.auth.login', ['email' => $this->user->email, 'password' => 'password']))
            ->assertOk()
            ->assertJson([
                'id' => 123456789,
                'expires_at' => now()->addDays(10)->timestamp,
                'token' => 'testing_token',
            ]);
    }

    public function test_it_will_logout_user(): void
    {
        auth()->login($this->user);

        $this->assertTrue(auth()->check());

        $this->setAuthenticatedUser($this->user)->postJson(route('v1.auth.logout'))->assertNoContent();

        $this->assertFalse(auth()->check());
    }

    public function test_if_it_fails_when_wrong_password(): void
    {
        $this->postJson(route('v1.auth.login', ['email' => $this->user->email, 'password' => 'wrong']))
            ->assertUnauthorized();
    }

    public function test_if_it_fails_when_email_was_not_found(): void
    {
        $this->postJson(route('v1.auth.login', ['email' => $this->faker->email, 'password' => 'password']))
            ->assertUnauthorized();
    }

    public function test_if_fails_when_incorrect_email_register(): void
    {
        $this->postJson(route('v1.auth.register', [
            'name' => $this->faker->name,
            'email' => 'test-this-is-not-a-valid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]))
            ->assertUnprocessable()
            ->assertJsonFragment(['email' => ['O campo email deve ser um endereço de e-mail válido.']]);
    }

    public function test_if_fails_when_email_already_exists(): void
    {
        $this->postJson(route('v1.auth.register', [
            'name' => $this->faker->name,
            'email' => $this->user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]))
            ->assertUnprocessable()
            ->assertJsonFragment(['email' => ['O campo email já está sendo utilizado.']]);
    }

    public function test_if_fails_when_name_not_passed(): void
    {
        $this->postJson(route('v1.auth.register', [
            'email' => $this->faker->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]))
            ->assertUnprocessable()
            ->assertJsonFragment(['name' => ['O campo nome é obrigatório.']]);
    }

    public function test_if_fails_when_email_not_passed(): void
    {
        $this->postJson(route('v1.auth.register', [
            'name' => $this->faker->name,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]))
            ->assertUnprocessable()
            ->assertJsonFragment(['email' => ['O campo email é obrigatório.']]);
    }

    public function test_if_fails_when_password_is_not_valid(): void
    {
        $this->postJson(route('v1.auth.register', [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => '1234',
            'password_confirmation' => '1234',
        ]))
            ->assertUnprocessable()
            ->assertJsonFragment(['password' => ['O campo senha deve ter pelo menos 6 caracteres.']]);
    }

    public function test_if_fails_when_password_confirmation_is_not_valid(): void
    {
        $this->postJson(route('v1.auth.register', [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'password_confirmation' => '1234',
        ]))
            ->assertUnprocessable()
            ->assertJsonFragment(['password' => ['O campo senha de confirmação não confere.']]);
    }

    public function test_if_fails_when_password_confirmation_was_not_send(): void
    {
        $this->postJson(route('v1.auth.register', [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
        ]))
            ->assertUnprocessable()
            ->assertJsonFragment(['password' => ['O campo senha de confirmação não confere.']]);
    }
}
