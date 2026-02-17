<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Services\Academic\CourseClassService;
use App\Services\Academic\EnrollmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortalFormationController extends Controller
{
    public function __construct(
        private CourseClassService $classService,
        private EnrollmentService  $enrollmentService,
    ) {}

    public function index(Request $request): \Inertia\Response
    {
        $student     = $request->user()->student;
        $enrollments = $this->enrollmentService->list(['student_id' => $student->id]);
        $available   = $this->classService->list(['status' => 'registration_open']);

        return Inertia::render('Portal/Formations/Index', compact('enrollments', 'available'));
    }

    public function show(Request $request, string $classId): \Inertia\Response
    {
        $class   = $this->classService->find($classId);
        $student = $request->user()->student;

        $enrolled = $student->enrollments()->where('class_id', $classId)->first();

        return Inertia::render('Portal/Formations/Show', compact('class', 'enrolled'));
    }

    public function enroll(Request $request, string $classId): \Illuminate\Http\RedirectResponse
    {
        $student = $request->user()->student;

        try {
            $this->enrollmentService->enroll($student->id, $classId);
            return back()->with('success', 'Inscription effectuée avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
