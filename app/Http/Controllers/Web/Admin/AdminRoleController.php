<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\RoleManagementService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminRoleController extends Controller
{
    public function __construct(private RoleManagementService $service) {}

    public function index(): \Inertia\Response
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles'       => $this->service->listRoles(),
            'permissions' => $this->service->listPermissions(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
        $this->service->createRole($request->name);
        return back()->with('success', 'Rôle créé.');
    }

    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['name' => 'required|string|unique:roles,name,' . $id]);
        $this->service->updateRole($id, $request->name);
        return back()->with('success', 'Rôle mis à jour.');
    }

    public function syncPermissions(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['permissions' => 'present|array', 'permissions.*' => 'string|exists:permissions,name']);
        $this->service->assignPermissionsToRole($id, $request->permissions);
        return back()->with('success', 'Permissions synchronisées.');
    }

    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->deleteRole($id);
            return back()->with('success', 'Rôle supprimé.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function storePermission(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['name' => 'required|string|unique:permissions,name']);
        $this->service->createPermission($request->name);
        return back()->with('success', 'Permission créée.');
    }

    public function destroyPermission(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->deletePermission($id);
            return back()->with('success', 'Permission supprimée.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}