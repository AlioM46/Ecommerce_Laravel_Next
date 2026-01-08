<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\checkout\Session;

class CheckoutController extends Controller
{


public function checkout(Request $request)
{
    return DB::transaction(function () use ($request) {
        // 1️⃣ Create Order
        $orderResponse = app(OrderService::class)->createOrder(
            1, // user id or $request->user()->id
            $request->address_id,
            $request->items
        );

        $order = $orderResponse->original['order'];

        // return response()->json([
        //     'isSuccess' => true,
        //     'order' => $order->products[0]->price,
        // ]);
        Stripe::setApiKey(config('services.stripe.secret'));
//         return response()->json([
//             'isSuccess' => true,
//             'sessionId' => $order->products,
// ]);

        // 2️⃣ Create Checkout Session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $order->products->map(function($item) {
                $product = Product::find($item->id);
                Log::error('Product in Checkout:', ['product' => $product]);
                Log::error('item in Checkout:', ['item' =>$item]);
                return [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $product->name,
                        ],
                        'unit_amount' => $product->discount_price > 0 ? $product->discount_price * 100 : $item->price * 100,
                    ],
                    'quantity' => $item->pivot->quantity,
                ];
            })->toArray(),
            'mode' => 'payment',
            'metadata' => [
                'order_id' => $order->id,
            ],
    'success_url' => env('APP_URL') . '/order-success?orderId=' . $order->id,
    'cancel_url'  => env('APP_URL') . '/cart',
        ]);

        // 3️⃣ Save payment record in your DB
        Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'stripe_checkout',
            'amount' => $order->total_price,
            'status' => 'pending',
            'transaction_id' => $session->id,
        ]);

        // 4️⃣ Return session ID to frontend
        return response()->json([
            'isSuccess' => true,
            'sessionUrl' => $session->url,
        ]);
    });
}
}
