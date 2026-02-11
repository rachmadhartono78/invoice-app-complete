<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : 'guest';

        Log::channel('api')->info('API Request', [
            'user_id' => $userId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'body' => $request->except(['password', 'token']),
        ]);

        return $next($request);
    }
}
