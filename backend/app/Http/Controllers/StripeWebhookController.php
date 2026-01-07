<?php

namespace App\Http\Controllers;

use App\Enums\enOrderStatus;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
public function handle(Request $request)
{
    Log::info('Webhook received');

    $payload   = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');

    try {
        $event = Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );
    } catch (\Exception $e) {
        Log::error('Webhook signature failed: '.$e->getMessage());
        return response('Invalid payload', 400);
    }

    Log::info('Event type: '.$event->type);

    // We ONLY care about successful payments
    if ($event->type !== 'payment_intent.succeeded') {
        return response()->json(['status' => 'ignored']);
    }

    $intent  = $event->data->object;
    $orderId = $intent->metadata->order_id ?? null;

    if (!$orderId) {
        Log::warning('Order ID missing in payment metadata');
        return response()->json(['status' => 'missing_order']);
    }

    $order = Order::find($orderId);

    if (!$order) {
        Log::warning("Order not found: {$orderId}");
        return response()->json(['status' => 'order_not_found']);
    }

    $payment = Payment::where('transaction_id', $intent->id)->first();

    if ($payment) {
        $payment->update(['status' => 'paid']);
    } else {
        Log::warning("Payment not found for intent {$intent->id}");
    }

    $order->update(['status' => enOrderStatus::Paid]);

    Log::info("Order {$orderId} marked as PAID");

    return response()->json(['status' => 'ok']);
}


}
