<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Support\Facades\Mail;

class SendLoginAlertEmail
{
    public function handle(UserLoggedIn $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new \App\Mail\LoginAlertMail(
            $user->name,
            $event->ip,
            $event->userAgent
        ));
    }
}
