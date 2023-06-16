<?php

namespace App\Notifications;

use App\Messages\CustomMailLayout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CustomReceiveResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The user email.
     *
     * @var string
     */
    public $email;

    /**
     * Create a notification instance.
     *
     * @param  string  $email
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): CustomMailLayout
    {
        return (new CustomMailLayout)
            ->subject("Your password has been changed")
            ->headingMail("Your password has been changed")
            ->greeting("Hi ".$this->email)
            ->line('You are receiving this email because we received a password reset request for your account.');
    }
}
