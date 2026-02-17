<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Services\Academic\EvaluationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortalEvaluationController extends Controller
{
    public function __construct(private EvaluationService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $student     = $request->user()->student;
        $evaluations = $this->service->getForStudent($student->id);

        return Inertia::render('Portal/Evaluations/Index', compact('evaluations'));
    }
}