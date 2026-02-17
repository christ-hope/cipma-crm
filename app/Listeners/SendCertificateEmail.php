<?php

namespace App\Listeners;

use App\Events\CertificateRequested;
use App\Mail\CertificateIssuedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendCertificateEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
public function handle(CertificateRequested $event): void
    {
        $certificate = $event->certificate;
        $studentEmail = $certificate->student->email;

        Mail::to($studentEmail)->send(new CertificateIssuedMail($certificate));
    }
}
