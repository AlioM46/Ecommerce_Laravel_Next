<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;



Route::prefix('auth')->group(function () {
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/refresh/{userId}', [AuthController::class,'refresh']);
Route::post('/logout', [AuthController::class,'logout']);

});




Route::prefix("/categories")->group(function (){
    Route::get("", [CategoryController::class, "index"]);
Route::post("", [CategoryController::class, "store"]);
Route::get("/top", [CategoryController::class, "getTop10Categories"]);
Route::get("/tree/{id}", [CategoryController::class, "getCategoryTreeByCategoryFirstLevel"]);
Route::get("/{id}", [CategoryController::class, "show"]);
Route::get("/byname/{categoryName}", [CategoryController::class, "getByName"]);
Route::delete("/{id}", [CategoryController::class, "destroy"]);
Route::put("/{id}", [CategoryController::class, "update"]);
});



Route::prefix("/product")->group(function() {
Route::get('', [ProductController::class, 'index']);
Route::get('/order', [ProductController::class, 'getOrdered']);
Route::post('', [ProductController::class, 'store']);
Route::put('/isActive', [ProductController::class, 'changeIsActive']);
Route::get('/{productId}', [ProductController::class, 'show']);
Route::put('/{productId}', [ProductController::class, 'update']);
Route::delete('/{productId}', [ProductController::class, 'destroy']);

});

Route::prefix("/address")->middleware("jwt.auth")->group(function() {
    Route::get('', [AddressController::class, 'index']);
Route::post('', [AddressController::class, 'store']);
Route::get('/{addressId}', [AddressController::class, 'show']);
Route::put('/{addressId}', [AddressController::class, 'update']);
Route::delete('/{addressId}', [AddressController::class, 'destroy']);

});


Route::prefix("/order")->middleware('jwt.auth')->group(function () {
    Route::get('', [OrderController::class, 'index']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::post('', [OrderController::class, 'store']);
    Route::put('/{id}/cancel', [OrderController::class, 'cancel']);
    Route::put('/{id}/pay', [OrderController::class, 'markAsPaid']);
});



Route::middleware("jwt.auth")->group(function() {
    Route::post('/payments/intent/{orderId}', [PaymentController::class, 'createIntent']);
    Route::post("/checkout", [CheckoutController::class, "checkOut"]);
});
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle']);