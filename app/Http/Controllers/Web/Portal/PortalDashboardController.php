<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Services\Academic\StudentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortalDashboardController extends Controller
{
    public function __construct(private StudentService $service) {}

    public function index(Request $request): \Inertia\Response
    {
        $student = $request->user()->student;
        $data    = $this->service->getDashboard($student);

        return Inertia::render('Portal/Dashboard', $data);
    }
}