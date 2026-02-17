<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Services\Academic\CertificateService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VerificationController extends Controller
{
    public function __construct(private CertificateService $service) {}

    public function certificate(string $uuid): \Inertia\Response
    {
        try {
            $data = $this->service->verify($uuid);
        } catch (\Throwable) {
            $data = ['valid' => false, 'error' => 'Certificat introuvable'];
        }

        return Inertia::render('Public/Verify/Certificate', compact('data'));
    }
}