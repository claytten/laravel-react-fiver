<?php

namespace App\Notifications;

use App\Messages\CustomMailLayout;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Get the verify email notification mail message for the given URL.
     *
     * @param  string  $url
     * @return App\Messages\CustomMailVerifyMessage
     */
    protected function buildMailMessage($url)
    {
        return (new CustomMailLayout)
            ->subject("Thank you for your application to ".config('app.name'))
            ->headingMail("Please Verify Your Email Address")
            ->greeting("Thanks for joining ".config('app.name'))
            ->line("To finish signing up, please confirm your email address. This ensures we have the right email in case we need to contact you.")
            ->action("Confirm Email Address", $url);
    }
}
