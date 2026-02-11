<?php
// Add this to routes/api.php

use App\Http\Controllers\API\InvoiceController;

Route::middleware('auth:sanctum')->prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'index']);
    Route::post('/', [InvoiceController::class, 'store']);
    Route::get('/{invoice}', [InvoiceController::class, 'show']);
    Route::put('/{invoice}', [InvoiceController::class, 'update']);
    Route::delete('/{invoice}', [InvoiceController::class, 'destroy']);
    Route::get('/{invoice}/pdf', [InvoiceController::class, 'pdf']);
});
