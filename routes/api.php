<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::post("/register", [ApiAuthController::class, 'register'])->name('api.register');
    Route::post("/login", [ApiAuthController::class, 'login'])->name('api.login');

    Route::apiResource('categories', CategoryController::class)->only(['index','show']);
    Route::apiResource('brands', BrandController::class)->only(['index','show']);
    Route::apiResource('products', ProductController::class)->only(['index','show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post("/logout", [ApiAuthController::class, 'logout'])->name('api.logout');
        Route::post("/logout-all", [ApiAuthController::class, 'logoutAll'])->name('api.logout-all');
        Route::post("/tokens", [ApiAuthController::class, 'tokens'])->name('api.tokens');

        Route::apiResource('brands', BrandController::class)->only(['store','update','destroy']);
        Route::apiResource('products', ProductController::class)->only(['store','update','destroy']);
        Route::apiResource('categories', CategoryController::class)->only(['store','update','destroy']);
        Route::apiResource('photos', PhotoController::class)->only(['index', 'store', 'destroy']);
    });
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });