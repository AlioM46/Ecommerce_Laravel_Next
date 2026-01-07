<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;

Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

Route::prefix('auth')->group(function () {
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/refresh/{userId}', [AuthController::class,'refresh']);
Route::post('/logout', [AuthController::class,'logout']);

});




Route::get("/categories", [CategoryController::class, "index"]);
Route::post("/categories", [CategoryController::class, "store"]);
Route::get("/categories/top", [CategoryController::class, "getTop10Categories"]);
Route::get("/categories/tree/{id}", [CategoryController::class, "getCategoryTreeByCategoryFirstLevel"]);
Route::get("/categories/{id}", [CategoryController::class, "show"]);
Route::get("/categories/byname/{categoryName}", [CategoryController::class, "getByName"]);
Route::delete("/categories/{id}", [CategoryController::class, "destroy"]);
Route::put("/categories/{id}", [CategoryController::class, "update"]);



Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/order', [ProductController::class, 'getOrdered']);
Route::post('/product', [ProductController::class, 'store']);
Route::put('/product/isActive', [ProductController::class, 'changeIsActive']);
Route::get('/product/{productId}', [ProductController::class, 'show']);
Route::put('/product/{productId}', [ProductController::class, 'update']);
Route::delete('/product/{productId}', [ProductController::class, 'destroy']);


Route::get('/address', [AddressController::class, 'index']);
Route::post('/address', [AddressController::class, 'store']);
Route::get('/address/{addressId}', [AddressController::class, 'show']);
Route::put('/address/{addressId}', [AddressController::class, 'update']);
Route::delete('/address/{addressId}', [AddressController::class, 'destroy']);



// Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('jwt.auth')->get('/order', [OrderController::class, 'index']);
    Route::middleware('jwt.auth')->get('/order/{id}', [OrderController::class, 'show']);
    Route::middleware('jwt.auth')->post('/order', [OrderController::class, 'store']);
    Route::middleware('jwt.auth')->put('/order/{id}/cancel', [OrderController::class, 'cancel']);
    Route::middleware('jwt.auth')->put('/order/{id}/pay', [OrderController::class, 'markAsPaid']);
// });



Route::post('/payments/intent/{orderId}', [PaymentController::class, 'createIntent']);


Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle']);
