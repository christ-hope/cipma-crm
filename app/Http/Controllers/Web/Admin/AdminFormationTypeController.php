<?php

namespace App\Http\Controllers\Web\Admin;

use App\EvaluationMode;
use App\Http\Controllers\Controller;
use App\Services\Academic\FormationTypeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminFormationTypeController extends Controller
{
    public function __construct(private FormationTypeService $service) {}

    public function index(): \Inertia\Response
    {
        return Inertia::render('Admin/FormationTypes/Index', [
            'types' => $this->service->list(),
            'evaluation_modes' => EvaluationMode::options(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:formation_types,name',
            'description' => 'nullable|string',
            'requires_certification' => 'boolean',
            'evaluation_mode' => 'required|in:crm,external,manual',
        ]);

        $this->service->create($validated, $request->user()->id);

        return back()->with('success', 'Type de formation créé.');
    }

    public function update(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => "sometimes|string|unique:formation_types,name,{$uuid}",
            'description' => 'nullable|string',
            'requires_certification' => 'sometimes|boolean',
            'evaluation_mode' => 'sometimes|in:crm,external,manual',
            'is_active' => 'sometimes|boolean',
        ]);

        $this->service->update($uuid, $validated);

        return back()->with('success', 'Type de formation mis à jour.');
    }

    public function destroy(string $uuid): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->delete($uuid);
            return back()->with('success', 'Type supprimé.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
