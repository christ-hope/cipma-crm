<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortalProfileController extends Controller
{
    public function show(Request $request): \Inertia\Response
    {
        return Inertia::render('Portal/Profile/Show', [
            'student' => $request->user()->student,
        ]);
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $student = $request->user()->student;

        $validated = $request->validate([
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        $student->update($validated);

        return back()->with('success', 'Profil mis à jour.');
    }
}