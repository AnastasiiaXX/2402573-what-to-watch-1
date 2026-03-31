<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => App\Http\Middleware\EnsureUserHasRole::class
        ]);
        $middleware->redirectGuestsTo(fn() => response()->json([
            'message' => 'Запрос требует аутентификации.'
        ], 401));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json(['message' => 'Запрос требует аутентификации.'], 401);
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            return response()->json(['message' => 'Действие не разрешено.'], 403);
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            return response()->json(['message' => 'Действие не разрешено.'], 403);
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json(['message' => 'Переданные данные не корректны.',
                'errors' => $e->errors()], 422);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json(['message' => 'Запрашиваемая страница не существует.'], 404);
        });
    })->create();
