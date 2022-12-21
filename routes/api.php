<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Dashboard\ProductController as DashboardProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

Route::get('/test', function () {
    // $from = Carbon::now()->subDays(8);
    // $to = Carbon::now();
    // return Order::groupBy('date')->select('id', DB::raw('DATE(created_at) AS date'), DB::raw('sum(billing_total) as sum'))
    //     ->whereDate('created_at', '<=', $to)->get();
    // $from = Carbon::now()->subDays(8);
    // $to = Carbon::now();
    // return Order::select(DB::raw('DATE(created_at) AS date'), DB::raw('count(*) as total'))->whereDate('created_at', '<=', $to)->groupBy('date')->get();
    $order = Order::find(1);
    return strtotime($order->created_at);
});

Route::prefix('v1')->group(function () {
    Route::post("/register", [ApiAuthController::class, 'register'])->name('api.register');
    Route::post("/login", [ApiAuthController::class, 'login'])->name('api.login');

    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    Route::apiResource('brands', BrandController::class)->only(['index', 'show']);
    Route::apiResource('products', ProductController::class)->only(['index', 'show'])->parameter('product', 'product:slug');

    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout.checkout');
    Route::post('/guest-payment', [CheckoutController::class, 'guestPayment'])->name('checkout.guestPayment');

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('orders', OrderController::class)->only(['index', 'show']);

        Route::post('/payment', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::put('profile/{user}', [ProfileController::class, 'update'])->name('api.profile.update');
        Route::put('profile/change-password/{user}', [ProfileController::class, 'updatePassword'])->name('api.profile.change-password');
        Route::put('profile/update-billing-info/{user}', [ProfileController::class, 'updateBillingInfo'])->name('api.profile.update-billing-info');
        Route::post("/logout", [ApiAuthController::class, 'logout'])->name('api.logout');
    });

    Route::prefix('dashboard')->group(function () {
        Route::post("/register", [ApiAuthController::class, 'register'])->name('api.register');
        Route::post("/login", [ApiAuthController::class, 'login'])->name('api.login');
    });

    Route::middleware('auth:sanctum')->prefix('dashboard')->group(function () {
        Route::post("/logout", [ApiAuthController::class, 'logout'])->name('api.logout');
        Route::post("/logout-all", [ApiAuthController::class, 'logoutAll'])->name('api.logout-all');
        Route::get("/tokens", [ApiAuthController::class, 'tokens'])->name('api.tokens');

        // Dashboard Routes
        Route::get('/users-count', [DashboardController::class, 'userCount']);
        Route::get('/products-count', [DashboardController::class, 'activeProducts']);
        Route::get('/orders-count', [DashboardController::class, 'unshippedOrders']);
        Route::get('/income-amount', [DashboardController::class, 'totalIncome']);
        Route::get('/latest-users', [DashboardController::class, 'latestUsers']);
        Route::get('/latest-orders', [DashboardController::class, 'latestOrders']);
        Route::get('/sale-values', [DashboardController::class, 'saleValues']);
        Route::get('/order-count', [DashboardController::class, 'orderCount']);

        Route::apiResource('users', UserController::class);
        Route::apiResource('brands', BrandController::class);
        Route::apiResource('products', DashboardProductController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('orders', DashboardOrderController::class)->only(['index', 'show', 'update']);
        Route::apiResource('photos', PhotoController::class)->only(['index', 'store', 'destroy']);
    });
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });