<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Master\UserController;
use App\Http\Controllers\API\Master\UserIdentifierController;
use App\Http\Controllers\API\Notification\NotificationController;
use App\Http\Controllers\API\Settings\ActionController;
use App\Http\Controllers\API\Settings\ApplicationController;
use App\Http\Controllers\API\Settings\AuthorityController;
use App\Http\Controllers\API\Settings\MenuController;
use App\Http\Controllers\API\Settings\NotificationTestController;
use App\Http\Controllers\API\Settings\OrganizationController;
use App\Http\Middleware\LogApiRequests;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Clear cache endpoint (development only)
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');

    return 'Cleared!';
});

// CSRF token endpoint
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Google OAuth callback
Route::post('google/callback', [AuthController::class, 'googleCallback']);

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/request-otp', [AuthController::class, 'requestOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/send-verification-email', [AuthController::class, 'sendEmailVerification']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
});

// Notification routes (public access)
Route::prefix('notification')->group(function () {
    Route::apiResource('', NotificationController::class);
    Route::post('{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('read-all', [NotificationController::class, 'readAll']);
});

// Protected routes (require authentication)
Route::middleware(['auth:sanctum', LogApiRequests::class])->group(function () {

    // Broadcasting authentication for WebSocket private channels
    Route::post('/broadcasting/auth', function (Illuminate\Http\Request $request) {
        return Illuminate\Support\Facades\Broadcast::auth($request);
    });

    // Check authentication
    Route::get('/me', [AuthController::class, 'checkToken']);
    Route::get('/load-app-data', [AuthController::class, 'loadAppData']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'userInfo']);

    // API v1 routes
    Route::prefix('v1')->group(function () {

        // Master data management
        Route::prefix('master')->group(function () {
            Route::apiResource('user', UserController::class);
            Route::put('user/{id}/password', [UserController::class, 'changePassword']);
            Route::put('user/{id}/phone', [UserController::class, 'changePhoneNumber']);
            Route::delete('user/{id}/revoke/{token_id}', [UserController::class, 'revokeToken']);

            // User identifier routes
            Route::post('user/{user_id}/identifiers', [UserIdentifierController::class, 'store']);
            Route::put('user/{user_id}/identifiers/{identifier_id}', [UserIdentifierController::class, 'update']);
            Route::delete('user/{user_id}/identifiers/{identifier_id}', [UserIdentifierController::class, 'destroy']);
            Route::patch('user/{user_id}/identifiers/{identifier_id}/verify', [UserIdentifierController::class, 'verify']);
            Route::patch('user/{user_id}/identifiers/{identifier_id}/restore', [UserIdentifierController::class, 'restore']);
        });

        // Settings/Admin routes (RBAC configuration)
        Route::prefix('settings')->group(function () {
            Route::apiResource('actions', ActionController::class);
            Route::apiResource('applications', ApplicationController::class);
            Route::apiResource('menus', MenuController::class);
            Route::apiResource('authorities', AuthorityController::class);
            Route::apiResource('organizations', OrganizationController::class);

            // Notification testing
            Route::post('notifications/test', [NotificationTestController::class, 'sendTest']);
        });
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/report/export', [DashboardController::class, 'exportReport']);
    });

    
        Route::prefix('invoices')->group(function(){
            Route::get('/',[InvoiceController::class,'index']);
            Route::get('/next-number',[InvoiceController::class,'nextNumber']);
            Route::post('/',[InvoiceController::class,'store']);
            Route::get('/{invoice}',[InvoiceController::class,'show']);
            Route::put('/{invoice}',[InvoiceController::class,'update']);
            Route::delete('/{invoice}',[InvoiceController::class,'destroy']);
            Route::get('/{invoice}/pdf',[InvoiceController::class,'pdf']);
            
            // Status action routes
            Route::post('/{invoice}/mark-as-quoted',[InvoiceController::class,'markAsQuoted']);
            Route::post('/{invoice}/mark-as-invoiced',[InvoiceController::class,'markAsInvoiced']);
            Route::post('/{invoice}/mark-as-paid',[InvoiceController::class,'markAsPaid']);
            Route::post('/{invoice}/mark-as-partial-paid',[InvoiceController::class,'markAsPartialPaid']);
            Route::post('/{invoice}/mark-as-void',[InvoiceController::class,'markAsVoid']);
        });

        Route::prefix('items')->group(function(){
            Route::post('/import', [\App\Http\Controllers\API\ImportController::class, 'items']);
            Route::get('/',[ItemController::class,'index']);
            Route::get('/categories',[ItemController::class,'categories']);
            Route::post('/',[ItemController::class,'store']);
            Route::get('/{item}',[ItemController::class,'show']);
            Route::put('/{item}',[ItemController::class,'update']);
            Route::delete('/{item}',[ItemController::class,'destroy']);
        });

        Route::prefix('customers')->group(function(){
            Route::get('/',[CustomerController::class,'index']);
            Route::post('/',[CustomerController::class,'store']);
            Route::get('/{customer}',[CustomerController::class,'show']);
            Route::put('/{customer}',[CustomerController::class,'update']);
            Route::delete('/{customer}',[CustomerController::class,'destroy']);
        });

        Route::prefix('payments')->group(function(){
            Route::get('/',[PaymentController::class,'index']);
            Route::get('/next-number',[PaymentController::class,'nextPaymentNumber']);
            Route::post('/',[PaymentController::class,'store']);
            Route::get('/{payment}',[PaymentController::class,'show']);
            Route::put('/{payment}',[PaymentController::class,'update']);
            Route::delete('/{payment}',[PaymentController::class,'destroy']);
        });
});
