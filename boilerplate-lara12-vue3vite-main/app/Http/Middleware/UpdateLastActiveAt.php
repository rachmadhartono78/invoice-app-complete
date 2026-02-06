<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateLastActiveAt
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->bearerToken()) {
            $token = $request->user()->currentAccessToken();
            if ($token) {
                $token->forceFill([
                    'last_used_at' => now(),
                ])->save();
            }
        }

        return $next($request);
    }
}
