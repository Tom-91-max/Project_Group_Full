<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if (isset($user->status) && $user->status === 'blocked') {
            abort(403, 'Your account has been blocked by admin.');
        }

        return $next($request);
    }
}
