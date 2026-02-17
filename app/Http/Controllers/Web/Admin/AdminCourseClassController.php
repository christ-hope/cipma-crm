<?php

namespace App\Http\Controllers\Web\Admin;

use App\ClassStatus;
use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\User;
use App\Services\Academic\CourseClassService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCourseClassController extends Controller
{
    public function __construct(private CourseClassService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $classes = $this->service->list($request->only('formation_id', 'status', 'per_page'));

        return Inertia::render('Admin/Classes/Index', [
            'classes' => $classes,
            'filters' => $request->only('formation_id', 'status'),
            'formations' => Formation::active()->get(['id', 'name', 'code']),
            'statuses' => ClassStatus::options(),
        ]);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Admin/Classes/Create', [
            'formations' => Formation::active()->get(),
            'instructors' => User::role('formateur')->get(['id', 'first_name', 'last_name', 'email']),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'formation_id' => 'required|uuid|exists:formations,id',
            'code' => 'required|string',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'instructor_id' => 'nullable|uuid|exists:users,id',
            'max_students' => 'nullable|integer',
            'location' => 'nullable|string',
            'status' => 'required|in:planned,registration_open',
        ]);

        $this->service->create($validated);

        return redirect()->route('admin.classes.index')->with('success', 'Classe créée.');
    }

    public function show(string $uuid): \Inertia\Response
    {
        $class = $this->service->find($uuid);
        return Inertia::render('Admin/Classes/Show', compact('class'));
    }

    public function edit(string $uuid): \Inertia\Response
    {
        return Inertia::render('Admin/Classes/Edit', [
            'class' => $this->service->find($uuid),
            'instructors' => User::role('formateur')->get(['id', 'first_name', 'last_name']),
            'statuses' => ClassStatus::options(),
        ]);
    }

    public function update(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'instructor_id' => 'nullable|uuid',
            'status' => 'sometimes|string',
            'location' => 'nullable|string',
        ]);

        $this->service->update($uuid, $validated);

        return redirect()->route('admin.classes.index')->with('success', 'Classe mise à jour.');
    }

    public function destroy(string $uuid): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->delete($uuid);
            return redirect()->route('admin.classes.index')->with('success', 'Classe supprimée.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
