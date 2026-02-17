<?php

namespace App\Http\Middleware;

use App\Services\SidebarService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    public function __construct(private SidebarService $sidebarService)
    {
    }


    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [

            // ── Authentification ──────────────────────────────────────────
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'must_change_password' => $user->must_change_password,
                    'is_active' => $user->is_active,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                    // Données étudiant si applicable
                    'student_id' => $user->student?->id,
                    'student_number' => $user->student?->student_number,
                ] : null,
            ],

            // ── Sidebar dynamique basée sur rôles/permissions ─────────────
            'sidebar' => $user
                ? $this->sidebarService->build($user)
                : [],

            // ── Flash messages ────────────────────────────────────────────
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'info' => fn() => $request->session()->get('info'),
            ],

            // ── Config app ───────────────────────────────────────────────
            'app' => [
                'name' => config('app.name'),
                'locale' => config('app.locale'),
                'currency' => 'XOF',
            ],
        ]);
    }
}
