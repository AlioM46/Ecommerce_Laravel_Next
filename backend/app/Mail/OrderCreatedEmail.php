<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCreatedEmail extends Mailable
{
    use  SerializesModels;

    public function __construct(public Order $order) {}

    public function build()
    {
        return $this->subject("Order #{$this->order->id} confirmed")
            ->view('emails.order-created')
            ->with(['order' => $this->order]);
    }
}
