<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class SendWelcomeEmail implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        // Kirim email selamat datang
        Mail::to($event->user->email)->send(new WelcomeMail($event->user));

        // Buat token untuk verifikasi email
        $event->user->sendEmailVerificationNotification();
    }
}
