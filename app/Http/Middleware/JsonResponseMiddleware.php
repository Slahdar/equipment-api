<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Set Accept header to application/json for API routes
        $request->headers->set('Accept', 'application/json');

        // Continue with the request
        $response = $next($request);

        // If the response is not a JsonResponse, convert it
        if (!$response instanceof JsonResponse && $response instanceof Response) {
            // Handle error responses
            if ($response->getStatusCode() >= 400) {
                return response()->json([
                    'success' => false,
                    'message' => $response->getStatusCode() === 404 
                        ? 'Resource not found' 
                        : 'An error occurred',
                    'status' => $response->getStatusCode()
                ], $response->getStatusCode());
            }
        }

        return $response;
    }
}