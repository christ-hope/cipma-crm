<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Academic\EnrollmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminEnrollmentController extends Controller
{
    public function __construct(private EnrollmentService $service)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        $enrollments = $this->service->list($request->only('class_id', 'student_id', 'status', 'per_page'));

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $enrollments,
            'filters' => $request->only('class_id', 'student_id', 'status'),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|uuid|exists:students,id',
            'class_id' => 'required|uuid|exists:classes,id',
        ]);

        try {
            $this->service->enroll($request->student_id, $request->class_id);
            return back()->with('success', 'Inscription créée.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateAttendance(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'sessions_attended' => 'required|integer|min:0',
            'sessions_total' => 'required|integer|min:1',
        ]);

        $this->service->updateAttendance($uuid, $request->sessions_attended, $request->sessions_total);

        return back()->with('success', 'Présence mise à jour.');
    }

    public function withdraw(string $uuid): \Illuminate\Http\RedirectResponse
    {
        $this->service->withdraw($uuid);
        return back()->with('success', 'Inscription retirée.');
    }
}