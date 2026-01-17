<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Payment;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function checkout(Request $request, OrderService $orderService)
    {
        return DB::transaction(function () use ($request, $orderService) {

            // 1️⃣ Create order
            $order = $orderService->createOrder(
                $request->user()->id,
                $request->address_id,
                $request->items
            );

            // 2️⃣ Prepare Stripe
            Stripe::setApiKey(config('services.stripe.secret'));

            // 3️⃣ Build line items from pivot table
            $lineItems = DB::table('order_product')
                ->where('order_id', $order->id)
                ->get()
                ->map(function ($item) {
                    $product = Product::find($item->product_id);

                    return [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $product->name,
                            ],
                            'unit_amount' => $item->price * 100,
                        ],
                        'quantity' => $item->quantity,
                    ];
                })->toArray();

            // 4️⃣ Create Stripe checkout session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'metadata' => ['order_id' => $order->id],
                'success_url' => env('FRONT_END_URL') . '/order-success?orderId=' . $order->id,
                'cancel_url' => env('FRONT_END_URL') . '/cart',
            ]);

            // 5️⃣ Save payment record
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'stripe_checkout',
                'amount' => $order->total_price,
                'status' => 'pending',
                'transaction_id' => $session->id,
            ]);




            // 6️⃣ Return session URL
            return response()->json([
                'isSuccess' => true,
                'sessionUrl' => $session->url,
            ]);
        });
    }
}
