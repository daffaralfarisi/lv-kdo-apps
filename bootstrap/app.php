<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Frontend Blade memanggil /api/* memakai session + CSRF (Sanctum SPA).
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Sesi berakhir atau token tidak valid. Silakan masuk kembali.'], 401);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                $pesan = in_array($exception->getMessage(), ['', 'This action is unauthorized.'], true)
                    ? 'Anda tidak memiliki hak akses untuk tindakan ini.'
                    : $exception->getMessage();

                return response()->json(['message' => $pesan], 403);
            }
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }
        });

        $exceptions->render(function (ThrottleRequestsException $exception, Request $request) {
            if ($request->is('api/*')) {
                $detik = $exception->getHeaders()['Retry-After'] ?? 60;

                return response()->json(['message' => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik."], 429, $exception->getHeaders());
            }
        });
    })->create();
