<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService
{
    public function __construct()
    {
        // Set the Stripe secret key from config
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a PaymentIntent
     *
     * @param int $amount Amount in cents
     * @param string $currency Currency code, e.g., 'usd'
     * @return PaymentIntent
     */
    public function createPaymentIntent(int $amount, string $currency = 'usd')
    {
        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method_types' => ['card'],
        ]);
    }

    /**
     * Verify webhook signature and return Stripe event
     */
    public function constructEvent(string $payload, string $sigHeader)
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        return \Stripe\Event::constructFrom(
            \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret)
        );
    }
}
