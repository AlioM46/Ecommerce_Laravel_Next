<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail
{
protected function verificationUrl($notifiable)
{
    $frontendUrl = rtrim(config('app.FRONT_END_URL'), '/') . '/verify-email';

    $signedUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $notifiable->id, 'hash' => sha1($notifiable->email)]
    );

    // signedUrl is like:
    // http://localhost:8000/api/email/verify/1/hash?expires=...&signature=...

    $path = parse_url($signedUrl, PHP_URL_PATH); // /api/email/verify/1/hash
    $query = parse_url($signedUrl, PHP_URL_QUERY); // expires=...&signature=...

    // extract id & hash from the path
    $parts = explode('/', trim($path, '/'));
    // parts: ["api","email","verify","1","hash"]
    $id = $parts[3] ?? null;
    $hash = $parts[4] ?? null;

    return $frontendUrl . '?' . $query . '&id=' . $id . '&hash=' . $hash;
}

public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verify your email address')
            ->line('Click the button below to verify your email address.')
            ->action('Verify Email', $this->verificationUrl($notifiable))
            ->line('If you did not create an account, no further action is required.');
    }
}
