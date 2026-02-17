<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\Academic\CertificateService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCertificateController extends Controller
{
    public function __construct(private CertificateService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $certificates = Certificate::with(['student', 'class.formation', 'issuedBy'])
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->when($request->search, fn($q) => $q->whereHas(
                'student',
                fn($q2) =>
                $q2->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('student_number', 'like', "%{$request->search}%")
            ))
            ->latest('emis_le')
            ->paginate(20);

        return Inertia::render('Admin/Certificates/Index', [
            'certificates' => $certificates,
            'filters' => $request->only('statut', 'search'),
        ]);
    }

    public function issue(Request $request, string $enrollmentId): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->request($enrollmentId, $request->user()->id);
            return back()->with('success', 'Certificat émis avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function revoke(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['reason' => 'required|string']);

        try {
            $this->service->revoke($uuid, $request->user()->id, $request->reason);
            return back()->with('success', 'Certificat révoqué.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function download(string $uuid): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $cert = Certificate::findOrFail($uuid);
        return \Illuminate\Support\Facades\Storage::disk('private')->download(
            $cert->pdf_path,
            "certificat-{$cert->numero_unique}.pdf"
        );
    }
}