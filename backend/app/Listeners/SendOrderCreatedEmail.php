<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Mail\OrderCreatedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderCreatedEmail 
{
    public function handle(OrderCreated $event): void
    {

        Log::info('ORDER CREATED LISTENER HIT', [
        'order_id' => $event->order->id,
    ]);
        try {
            $order = $event->order->load([
                'user',
                'address',
                'products',
                'payment',
            ]);

            Mail::to($order->user->email)->send(new \App\Mail\OrderCreatedEmail($order));
        } catch (\Throwable $e) {
            Log::error('Order created email failed', [
                'order_id' => $event->order->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
