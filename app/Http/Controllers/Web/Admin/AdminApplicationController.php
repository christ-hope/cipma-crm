<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\Application\ApplicationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminApplicationController extends Controller
{
    public function __construct(private ApplicationService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $applications = $this->service->list($request->only('status', 'search', 'per_page'));

        return Inertia::render('Admin/Applications/Index', [
            'applications' => $applications,
            'filters' => $request->only('status', 'search'),
        ]);
    }

    public function show(string $uuid): \Inertia\Response
    {
        $application = Application::with(['documents', 'reviewer', 'student'])->findOrFail($uuid);

        return Inertia::render('Admin/Applications/Show', compact('application'));
    }

    public function approve(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->approve($uuid, $request->user()->id);
            return redirect()->route('admin.applications.index')
                ->with('success', 'Candidature approuvée et compte étudiant créé.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['reason' => 'required|string']);

        $this->service->reject($uuid, $request->user()->id, $request->reason);

        return redirect()->route('admin.applications.index')->with('success', 'Candidature rejetée.');
    }

    public function requestInfo(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['notes' => 'required|string']);

        $this->service->requestInfo($uuid, $request->user()->id, $request->notes);

        return back()->with('success', 'Demande d\'informations envoyée.');
    }
}