<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RoleManagementService;
use App\Services\UserManagementService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function __construct(
        private UserManagementService $userService,
        private RoleManagementService $roleService,
    ) {
    }

    public function index(Request $request): \Inertia\Response
    {
        $users = $this->userService->listUsers($request->only('role', 'search', 'per_page'));

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only('role', 'search'),
            'roles' => $this->roleService->listRoles(),
        ]);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => $this->roleService->listRoles(),
            'permissions' => $this->roleService->listPermissions(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'roles' => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $this->userService->createStaffUser($validated);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé. Identifiants envoyés par email.');
    }

    public function show(string $uuid): \Inertia\Response
    {
        $user = User::with(['roles', 'permissions'])->findOrFail($uuid);
        return Inertia::render('Admin/Users/Show', compact('user'));
    }

    public function edit(string $uuid): \Inertia\Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => User::with(['roles', 'permissions'])->findOrFail($uuid),
            'roles' => $this->roleService->listRoles(),
            'permissions' => $this->roleService->listPermissions(),
        ]);
    }

    public function update(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => "sometimes|email|unique:users,email,{$uuid}",
            'is_active' => 'sometimes|boolean',
        ]);

        $this->userService->updateUser($uuid, $validated);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function assignRoles(Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $this->userService->assignRoles($uuid, $request->roles);

        return back()->with('success', 'Rôles mis à jour.');
    }

    public function resetPassword(string $uuid): \Illuminate\Http\RedirectResponse
    {
        $this->userService->resetPassword($uuid);
        return back()->with('success', 'Mot de passe réinitialisé. Nouveau mot de passe envoyé par email.');
    }

    public function destroy(string $uuid): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->userService->deleteUser($uuid);
            return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
