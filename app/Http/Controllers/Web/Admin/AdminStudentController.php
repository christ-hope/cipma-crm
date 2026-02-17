<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\Academic\StudentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminStudentController extends Controller
{
    public function __construct(private StudentService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $students = $this->service->list($request->only('status', 'search', 'per_page'));

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'filters'  => $request->only('status', 'search'),
        ]);
    }

    public function show(string $uuid): \Inertia\Response
    {
        $student = $this->service->find($uuid);

        return Inertia::render('Admin/Students/Show', compact('student'));
    }

    public function dashboard(string $uuid, Request $request): \Inertia\Response
    {
        $student = Student::findOrFail($uuid);
        $data    = $this->service->getDashboard($student);

        return Inertia::render('Admin/Students/Dashboard', $data);
    }

    public function update(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:active,suspended,graduated,withdrawn',
            'phone'  => 'nullable|string',
        ]);

        $this->service->update($uuid, $validated);

        return back()->with('success', 'Étudiant mis à jour.');
    }
}