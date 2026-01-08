<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{

    public function __construct()
    {

    }

    public function createIntent($orderId)
    {
  $order = Order::findOrFail($orderId);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => (int)($order->total_price * 100), // Stripe uses cents
            'currency' => 'usd',
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        

        // Store payment as pending
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'card',
            'amount' => $order->total_price,
            'status' => 'pending',
            'transaction_id' => $intent->id,
        ]);


        return response()->json([
            'client_secret' => $intent->client_secret,
            "isSuccess" => true,
        ]);    }

    /**
     * Stripe webhook endpoint
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle event types
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // PaymentIntent object
                // TODO: update your order/payment record in DB
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                // TODO: handle failure
                break;

            // add other event types if needed
        }

        return response()->json(['status' => 'success']);
    }
}
