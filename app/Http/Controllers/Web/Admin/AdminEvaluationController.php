<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Services\Academic\EvaluationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminEvaluationController extends Controller
{
    public function __construct(private EvaluationService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $classId = $request->class_id;
        $evals   = $classId ? $this->service->getForClass($classId) : collect();
        $classes = CourseClass::with('formation')->inProgress()->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Evaluations/Index', [
            'evaluations'    => $evals,
            'classes'        => $classes,
            'selected_class' => $classId,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'student_id'    => 'required|uuid|exists:students,id',
            'class_id'      => 'required|uuid|exists:classes,id',
            'evaluation_id' => 'nullable|uuid|exists:evaluations,id',
            'source'        => 'required|in:crm,moodle,manual',
            'score'         => 'nullable|numeric|min:0|max:20',
            'note_finale'   => 'required|numeric|min:0|max:20',
            'presence'      => 'nullable|boolean',
            'commentaire'   => 'nullable|string',
            'metadata'      => 'nullable|array',
        ]);

        $this->service->manualEntry($validated, $request->user()->id);

        return back()->with('success', 'Évaluation enregistrée.');
    }

    public function validate(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $this->service->validate($uuid, $request->user()->id);
        return back()->with('success', 'Évaluation validée.');
    }

    public function invalidate(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $this->service->invalidate($uuid, $request->user()->id);
        return back()->with('success', 'Évaluation invalidée.');
    }
}