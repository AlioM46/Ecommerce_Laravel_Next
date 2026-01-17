<?php

namespace App\Http\Controllers;

use App\Enums\enOrderStatus;
use App\Events\OrderCreated;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
public function handle(Request $request)
{
    $payload   = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );
    } catch (\Exception $e) {
        Log::error('Webhook signature failed: '.$e->getMessage());
        return response('Invalid payload', 400);
    }

    Log::info('Event type: '.$event->type);

    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;

        $orderId = $session->metadata->order_id ?? null;
        if (!$orderId) {
            Log::warning('Order ID missing in metadata');
            return response()->json(['status' => 'missing_order']);
        }

        $order = Order::find($orderId);
        if (!$order) {
            Log::warning("Order not found: {$orderId}");
            return response()->json(['status' => 'order_not_found']);
        }

        // Update Payment record
        $payment = Payment::where('transaction_id', $session->id)->first();
        if ($payment) {
            $payment->update(['status' => 'paid']);
        } else {
            Log::warning("Payment not found for session {$session->id}");
        }

        $order->update(['status' => \App\Enums\enOrderStatus::Paid]);

        Log::info("Order {$orderId} marked as PAID");

        # Fire The Event (Email message)
            event(new OrderCreated($order));


        return response()->json(['status' => 'ok']);
    }

    // You can still handle other event types if needed
    return response()->json(['status' => 'ignored']);
}
}
