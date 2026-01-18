<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginAlertMail extends Mailable
{
  use  SerializesModels;

    public function __construct(
        public string $name,
        public string $ip,
        public ?string $userAgent
    ) {}

    public function build()
    {
        return $this->subject('New login to your account')
            ->view('emails.login-alert');
    }
}
