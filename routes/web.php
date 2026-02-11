<?php

use App\Http\Controllers\API\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/yahoo/redirect', [AuthController::class, 'redirectToYahoo']);
Route::get('/yahoo/callback', [AuthController::class, 'handleYahooCallback']);

// SPA catch-all route - must exclude /api routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
