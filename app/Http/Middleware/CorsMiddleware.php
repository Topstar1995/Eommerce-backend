<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('origin');
        $allowedOrigins = ['*', 'https://ecommerce-frontend-tau-swart.vercel.app', 'https://ecommerce-frontend-tau-swart.vercel.app/*'];

        $response = $next($request);

        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

            if ($request->getMethod() == "OPTIONS") {
                $response->headers->set('Access-Control-Max-Age', '3600');
                $response->setStatusCode(204);
            }
        }

        return $response;
    }
}
