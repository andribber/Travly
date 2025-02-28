<?php /** @noinspection PhpInconsistentReturnPointsInspection */

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\Exceptions\InvalidSortQuery;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->booting(function () {
        RateLimiter::for(
            'api',
            fn (Request $request) => Limit::perMinute(config('app.rate_limit.max_attempts'))
                ->by($request->user()?->id ?: $request->ip())
        );

        Route::middleware('api')->group(base_path('routes/api.php'));
    })
    ->withEvents(false)
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ]);

        $middleware->group('api', [
            'throttle:api',
            SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'password.confirm' => RequirePassword::class,
            'throttle' => ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception) {
            [$status, $message, $code] = match (true) {
                $exception instanceof ValidationException => [
                    $exception->status,
                    $exception->validator->errors(),
                    Response::$statusTexts[$exception->status],
                ],
                $exception instanceof AuthenticationException,
                    $exception instanceof TokenMismatchException,
                    $exception instanceof AccessDeniedHttpException => [
                    Response::HTTP_UNAUTHORIZED,
                    Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                    null,
                ],
                $exception instanceof AuthorizationException => [
                    Response::HTTP_FORBIDDEN,
                    Response::$statusTexts[Response::HTTP_FORBIDDEN],
                    null,
                ],
                $exception instanceof ThrottleRequestsException => [
                    Response::HTTP_TOO_MANY_REQUESTS,
                    Response::$statusTexts[Response::HTTP_TOO_MANY_REQUESTS],
                    null,
                ],
                $exception instanceof ModelNotFoundException,
                    $exception instanceof NotFoundHttpException,
                    $exception instanceof ItemNotFoundException => [
                    Response::HTTP_NOT_FOUND,
                    Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    null,
                ],
                $exception instanceof InvalidFilterQuery,
                    $exception instanceof InvalidSortQuery => [
                    Response::HTTP_BAD_REQUEST,
                    $exception->getMessage(),
                    null,
                ],
                $exception instanceof MethodNotAllowedHttpException => [
                    Response::HTTP_METHOD_NOT_ALLOWED,
                    Response::$statusTexts[Response::HTTP_METHOD_NOT_ALLOWED],
                    null,
                ],

                default => [null, null, null],
            };

            if (is_null($status) && is_null($message) && is_null($code)) {
                return;
            }

            return response()->json([
                ...isset($code) ? ['code' => $code] : [],
                isset($code) ? 'errors' : 'message' => $message,
            ], $status);
        });
    })
    ->create();
