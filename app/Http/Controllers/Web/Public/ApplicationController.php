<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Services\Application\ApplicationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    public function __construct(private ApplicationService $service) {}

    public function create(): \Inertia\Response
    {
        return Inertia::render('Public/Application/Create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'first_name'           => 'required|string|max:255',
            'last_name'            => 'required|string|max:255',
            'email'                => 'required|email|unique:applications,email',
            'phone'                => 'required|string',
            'birth_date'           => 'required|date|before:today',
            'birth_place'          => 'nullable|string',
            'nationality'          => 'required|string',
            'address'              => 'required|string',
            'city'                 => 'required|string',
            'postal_code'          => 'required|string',
            'country'              => 'required|string',
            'last_diploma'         => 'nullable|string',
            'institution'          => 'nullable|string',
            'graduation_year'      => 'nullable|integer|min:1950|max:' . date('Y'),
            'academic_background'  => 'nullable|string',
            'requested_formations' => 'required|array|min:1',
            'requested_formations.*'=> 'uuid|exists:formations,id',
            'legal_declaration'    => 'required|accepted',
        ]);

        $this->service->create($validated);

        return redirect()->route('application.success');
    }

    public function success(): \Inertia\Response
    {
        return Inertia::render('Public/Application/Success');
    }
}
