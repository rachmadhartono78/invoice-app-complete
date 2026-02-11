<?php

namespace App\Http;

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Support\Facades\RateLimiter;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            VerifyCsrfToken::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LogApiRequests::class,
            \App\Http\Middleware\UpdateLastActiveAt::class,
            \App\Http\Middleware\CustomSanctumMiddleware::class,
        ],
    ];

    protected $commands = [
        // Register custom commands here
    ];

    public function __construct()
    {
        parent::__construct();

        RateLimiter::for('api', function () {
            return Limit::perMinute(60)->by(request()->user()?->id ?: request()->ip());
        });
    }

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('notify:weekly-whatsapp')
        //     ->mondays()
        //     ->at('08:00'); // 8:00 AM
    }
}
