<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return api_response([], 'Unauthenticated', 401);
        }

        $roleId = $user->user_role_id ?? 0;

        // Check if user is super admin (role_id = -1)
        if ($roleId != -1) {
            return api_response([
                'role_id' => $roleId,
            ], 'Unauthorized - Super admin access required', 403);
        }

        return $next($request);
    }
}
