<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // API routes for frontend (separate frontend folder consumes this)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies (required when behind Nginx/Cloudflare so X-Forwarded-Proto is honoured for HSTS/CORS)
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO | \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB);
        $middleware->append(\App\Http\Middleware\SecureHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['success'=>false,'message'=>'Resource not found'], 404);
            }
        });
        // My20i 403 fix: log admin 403 with user/ability for debugging, never cache it
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('admin*')) {
                \Illuminate\Support\Facades\Log::warning('Admin 403', [
                    'user' => auth()->id(),
                    'email' => auth()->user()?->email,
                    'path' => $request->path(),
                    'ability' => $e->getMessage(),
                    'ip' => $request->ip(),
                ]);
                // In production, for active users, Gate::before already allows, but if still 403, show friendly page
                if (app()->isProduction() && auth()->check() && auth()->user()->is_active) {
                    // Don't hide, but ensure it's not cached by CDN
                    // Let the 403 response pass with no-cache headers (handled in SecureHeaders)
                }
            }
        });
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') && app()->isProduction()) {
                \Illuminate\Support\Facades\Log::error('API exception', ['message'=>$e->getMessage(), 'trace'=>$e->getTraceAsString()]);
                return response()->json(['success'=>false,'message'=>'Internal server error'], 500);
            }
        });
    })->create();
