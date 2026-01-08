<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        return DB::transaction(function () use ($request) {

            // 1️⃣ Create Order
            $orderResponse = app(OrderService::class)->createOrder(
                1,
                // $request->user()->id,
                $request->address_id,
                $request->items
            );

            $order = $orderResponse->original['order'];

            // 2️⃣ Create PaymentIntent
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount' => (int) ($order->total_price * 100),
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);

            // 3️⃣ Save payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'card',
                'amount' => $order->total_price,
                'status' => 'pending',
                'transaction_id' => $intent->id,
            ]);

            // 4️⃣ Return ONLY what frontend needs
            return response()->json([
                'isSuccess' => true,
                'client_secret' => $intent->client_secret,
            ]);
        });
    }
}
