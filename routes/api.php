<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\UserController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('api')->group(function (Router $router) {
    $router->get('/ping', fn () => 'pong');

    $router->group(['prefix' => 'auth', 'controller' => AuthController::class], function (Router $router) {
        $router->post('/register', 'register')->name('v1.auth.register');
        $router->post('/login', 'login')->name('v1.auth.login');

        $router->group(['middleware' => 'auth:api'], function (Router $router) {
            $router->post('/logout', 'logout')->name('v1.auth.logout');
            $router->post('/refresh', 'refresh')->name('v1.auth.refresh');
        });
    });

    $router->group(['middleware' => 'auth:api'], function (Router $router) {
        $router->get('/', [UserController::class, 'me'])->name('v1.user.me');

        $router->group(['prefix' => 'travels', 'controller' => TravelController::class], function (Router $router) {
            $router->get('/', 'index')->name('v1.travels.index');
            $router->get('/{travel}', 'show')->name('v1.travels.show');

            $router->post('/', 'store')->name('v1.travels.store');

            $router->put('/{travel}', 'update')->name('v1.travels.update');

            $router->delete('/{travel}', 'delete')->name('v1.travels.delete');
        });
    });
});
