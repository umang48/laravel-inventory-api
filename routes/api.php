<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;


// Public routes (if any)
Route::get('/products', [ProductController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Anyone authenticated can view details
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);

    // Only admins can create/update/delete
    Route::middleware('role:admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        
        Route::post('/categories', [CategoryController::class, 'store']);
        // Add other admin-only endpoints here

        // Stock adjustment endpoint
        Route::post('/products/{product}/stock', [InventoryController::class, 'updateStock']);
    });
});