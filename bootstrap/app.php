<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['api', 'auth:sanctum']],
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup(
            'api',
            \App\Http\Middleware\ForceJsonResponse::class,
        );
        $middleware->alias([
            'pin' => \App\Http\Middleware\RequirePin::class,
            'verified.email' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'subscribed' => \App\Http\Middleware\EnsureActiveSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (
            Request $request,
            Throwable $e,
        ) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });

        $exceptions->render(function (
            AccessDeniedHttpException $e,
            Request $request,
        ) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => $e->getMessage() ?: 'This action is unauthorized.',
                    ],
                    403,
                );
            }
        });

        $exceptions->render(function (
            \Illuminate\Auth\Access\AuthorizationException $e,
            Request $request,
        ) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ],
                    403,
                );
            }
        });

        $exceptions->render(function (
            NotFoundHttpException $e,
            Request $request,
        ) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Route not found.',
                    ],
                    404,
                );
            }
        });
        $exceptions->render(function (
            ModelNotFoundException $e,
            Request $request,
        ) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Record not found.',
                    ],
                    404,
                );
            }
        });

        $exceptions->render(function (
            MethodNotAllowedHttpException $e,
            Request $request,
        ) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => "The {$request->method()} method is not allowed for this endpoint.",
                    ],
                    405,
                );
            }
        });
    })
    ->create();
