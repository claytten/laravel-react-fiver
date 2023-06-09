<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomReceiveResetPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @var $user */
    private $user;

    /**
    * Create a new message instance.
    *
    * @param $user
    */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
    * Build the message.
    *
    * @return $this
    */
    public function build(): self
    {
        return $this
            ->subject("Your password has been changed")
            ->view('mail.email-reset-password', [
                'greeting' => "Hi ".$this->user->email,
                'introLines' => ['You are receiving this email because we received a password reset request for your account.'],
                'socialMedia' => [
                    'facebook' => 'https://www.facebook.com/',
                ],
            ]);
    }
}
