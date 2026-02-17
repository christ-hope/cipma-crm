<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Services\Academic\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PortalCertificateController extends Controller
{
    public function __construct(private CertificateService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $student = $request->user()->student;
        $certificates = $this->service->getForStudent($student->id);

        return Inertia::render('Portal/Certificates/Index', compact('certificates'));
    }

    public function request(Request $request, string $enrollmentId): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->request($enrollmentId, $request->user()->id);
            return back()->with('success', 'Demande de certificat soumise avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function download(string $id): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $certificate = \App\Models\Certificate::findOrFail($id);
        $this->authorize('view', $certificate);

        return Storage::disk('private')->download(
            $certificate->pdf_path,
            "certificat-{$certificate->numero_unique}.pdf",
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }
}