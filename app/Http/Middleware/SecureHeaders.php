<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // HSTS: also trust X-Forwarded-Proto when behind reverse proxy (Nginx/Cloudflare)
        $isHttps = $request->isSecure() || $request->header('X-Forwarded-Proto') === 'https' || $request->header('X-Forwarded-Ssl') === 'on';
        if ($isHttps) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload', false);
        }
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()', false);
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none', false);

        // Basic CSP for Filament admin only (API routes return JSON, no CSP needed)
        if (! $request->is('api/*')) {
            $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https: blob:; connect-src 'self'", false);
        }

        return $response;
    }
}
