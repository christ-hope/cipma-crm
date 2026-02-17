<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Academic\ValidationEngine;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminValidationController extends Controller
{
    public function __construct(private ValidationEngine $engine) {}

    public function check(string $enrollmentId): \Inertia\Response
    {
        $result = $this->engine->check($enrollmentId);
        return Inertia::render('Admin/Validation/Check', compact('result', 'enrollmentId'));
    }

    public function validate(Request $request, string $enrollmentId): \Illuminate\Http\RedirectResponse
    {
        $this->engine->manualValidate($enrollmentId, $request->user()->id);
        return back()->with('success', 'Formation validée manuellement.');
    }
}