<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
            }
            return redirect()->route('login');
        }

        $user = Auth::guard('web')->user();

        // Check if user has admin role
        if (!$user->role || $user->role->name !== 'admin') {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthorized: Admin access only'], Response::HTTP_FORBIDDEN);
            }
            abort(403, 'Unauthorized: Admin access only');
        }

        return $next($request);
    }
}
