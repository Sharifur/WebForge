<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {

        // dd(Auth::guard('admin')->check());

        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please log in as admin.',
                    'redirect' => route('admin.login')
                ], 401);
            }
            return redirect()->route('admin.login');
        }

        if (!Auth::guard('admin')->user()->is_active) {
            Auth::guard('admin')->logout();
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated.',
                    'redirect' => route('admin.login')
                ], 403);
            }
            
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }

        return $next($request);
    }
}