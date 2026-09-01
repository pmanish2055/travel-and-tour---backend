<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies for Cloudflare / Nginx
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO |
                     Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // Secure Headers Middleware
        $middleware->append(\App\Http\Middleware\SecureHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // API Validation Exception
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // API 404 Exception
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }
        });

        // Admin 403 Authorization Exception Logging
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->is('admin*')) {
                \Illuminate\Support\Facades\Log::warning('Admin 403 Forbidden Access Attempt', [
                    'user'    => auth()->id(),
                    'email'   => auth()->user()?->email,
                    'path'    => $request->path(),
                    'ability' => $e->getMessage(),
                    'ip'      => $request->ip(),
                ]);
            }
        });

        // Generic API Throwable Exception
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') && app()->isProduction()) {
                \Illuminate\Support\Facades\Log::error('API exception', [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Internal server error'
                ], 500);
            }
        });
    })->create();