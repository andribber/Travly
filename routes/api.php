<?php

use App\Http\Controllers\AuthController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (Router $router) {
    $router->get('/ping', fn () => 'pong');

    $router->controller(AuthController::class)
        ->prefix('auth')
        ->middleware('api')
        ->group(function (Router $router) {
            $router->post('/register', 'register')->name('v1.auth.register');
            $router->post('/login', 'login')->name('v1.auth.login');

            $router->middleware('auth:api')->group(function (Router $router) {
                $router->post('/logout', 'logout')->middleware('auth:api')->name('v1.auth.logout');
                $router->post('/refresh', 'refresh')->middleware('auth:api')->name('v1.auth.refresh');
                $router->post('/me', 'me')->middleware('auth:api')->name('v1.auth.me');
            });
        });
});
