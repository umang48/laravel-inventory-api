<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;


Route::get('/login', function () {
    return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
})->name('login');

// 2. The actual endpoint to get your token
Route::post('/login', [AuthController::class, 'login']);



// Public routes (if any)
Route::get('/products', [ProductController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

Route::put('/categories/{category}', [CategoryController::class, 'update']);
Route::put('/products/{product}', [ProductController::class, 'update']);

// Add these inside Route::middleware('auth:sanctum')->group(...)
Route::get('/profile', [AuthController::class, 'profile']);
Route::post('/logout', [AuthController::class, 'logout']);
    
    // Anyone authenticated can view details
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);

    // Only admins can create/update/delete
    Route::middleware('role:admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    
        // Add other admin-only endpoints here

        // Stock adjustment endpoint
        Route::post('/products/{product}/stock', [InventoryController::class, 'updateStock']);
    });

    Route::get('/products/{product}/transactions', [\App\Http\Controllers\InventoryController::class, 'history']);
    
});