<?php

namespace App\Listeners;

use App\Mail\CustomReceiveResetPassword;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetEmail
{
    /**
     * Handle the event.
     *
     * @param  PasswordReset  $event
     *
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        $user = $event->user;

        Mail::to($user)
            ->send(new CustomReceiveResetPassword($user));
    }
}
