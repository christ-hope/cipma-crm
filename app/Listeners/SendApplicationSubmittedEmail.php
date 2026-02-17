<?php

namespace App\Listeners;

use App\Events\ApplicationSubmitted;
use App\Mail\ApplicationSubmittedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendApplicationSubmittedEmail implements ShouldQueue
{
    public function handle(ApplicationSubmitted $event): void
    {
        // Mail::to($event->application->email)->send(new ApplicationSubmittedMail($event->application));
    }
}