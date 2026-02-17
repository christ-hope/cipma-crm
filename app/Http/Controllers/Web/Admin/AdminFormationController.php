<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormationType;
use App\Services\Academic\FormationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminFormationController extends Controller
{
    public function __construct(private FormationService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $formations = $this->service->list($request->only('type_slug', 'search', 'per_page'));

        return Inertia::render('Admin/Formations/Index', [
            'formations'     => $formations,
            'filters'        => $request->only('type_slug', 'search'),
            'formation_types'=> FormationType::active()->get(['id', 'name', 'slug']),
        ]);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Admin/Formations/Create', [
            'formation_types' => FormationType::active()->get(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'formation_type_id' => 'required|uuid|exists:formation_types,id',
            'code'              => 'required|string|unique:formations,code',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'mode'              => 'required|in:online,presentiel,hybrid',
            'duration_hours'    => 'nullable|integer',
            'price'             => 'required|numeric|min:0',
            'currency'          => 'nullable|string|size:3',
            'max_students'      => 'nullable|integer',
        ]);

        $this->service->create($validated);

        return redirect()->route('admin.formations.index')->with('success', 'Formation créée.');
    }

    public function show(string $uuid): \Inertia\Response
    {
        $formation = $this->service->find($uuid);
        return Inertia::render('Admin/Formations/Show', compact('formation'));
    }

    public function edit(string $uuid): \Inertia\Response
    {
        return Inertia::render('Admin/Formations/Edit', [
            'formation'       => $this->service->find($uuid),
            'formation_types' => FormationType::active()->get(),
        ]);
    }

    public function update(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'mode'        => 'sometimes|in:online,presentiel,hybrid',
            'price'       => 'sometimes|numeric|min:0',
            'is_active'   => 'sometimes|boolean',
        ]);

        $this->service->update($uuid, $validated);

        return redirect()->route('admin.formations.index')->with('success', 'Formation mise à jour.');
    }

    public function destroy(string $uuid): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->delete($uuid);
            return redirect()->route('admin.formations.index')->with('success', 'Formation supprimée.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
