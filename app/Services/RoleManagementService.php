<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleManagementService
{
    /**
     * Créer un nouveau rôle
     */
    public function createRole(string $name, ?string $guardName = null): Role
    {
        return Role::create([
            'name' => $name,
            'guard_name' => $guardName ?? 'web',
        ]);
    }

    /**
     * Mettre à jour un rôle
     */
    public function updateRole(int $roleId, string $name): Role
    {
        $role = Role::findById($roleId);
        $role->update(['name' => $name]);

        return $role;
    }

    /**
     * Supprimer un rôle
     */
    public function deleteRole(int $roleId): bool
    {
        $role = Role::findById($roleId);

        // Ne pas permettre suppression des rôles système
        $systemRoles = ['super_admin', 'student'];
        if (in_array($role->name, $systemRoles)) {
            throw new \Exception('Impossible de supprimer un rôle système');
        }

        // Vérifier si des utilisateurs ont ce rôle
        if ($role->users()->count() > 0) {
            throw new \Exception('Impossible de supprimer un rôle assigné à des utilisateurs');
        }

        return $role->delete();
    }

    /**
     * Assigner des permissions à un rôle
     */
    public function assignPermissionsToRole(int $roleId, array $permissions): Role
    {
        $role = Role::findById($roleId);

        // Synchroniser les permissions (remplace les anciennes)
        $role->syncPermissions($permissions);

        return $role->fresh(['permissions']);
    }

    /**
     * Créer une nouvelle permission
     */
    public function createPermission(string $name, ?string $guardName = null): Permission
    {
        return Permission::create([
            'name' => $name,
            'guard_name' => $guardName ?? 'web',
        ]);
    }

    /**
     * Mettre à jour une permission
     */
    public function updatePermission(int $permissionId, string $name): Permission
    {
        $permission = Permission::findById($permissionId);
        $permission->update(['name' => $name]);

        return $permission;
    }

    /**
     * Supprimer une permission
     */
    public function deletePermission(int $permissionId): bool
    {
        $permission = Permission::findById($permissionId);

        // Vérifier si la permission est utilisée
        if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
            throw new \Exception('Impossible de supprimer une permission utilisée');
        }

        return $permission->delete();
    }

    /**
     * Lister tous les rôles avec leurs permissions
     */
    public function listRoles()
    {
        return Role::with('permissions')->get();
    }

    /**
     * Lister toutes les permissions
     */
    public function listPermissions()
    {
        return Permission::all();
    }

    /**
     * Obtenir les permissions d'un rôle
     */
    public function getRolePermissions(int $roleId): array
    {
        $role = Role::findById($roleId);

        return $role->permissions->pluck('name')->toArray();
    }

    /**
     * Obtenir les utilisateurs ayant un rôle
     */
    public function getRoleUsers(int $roleId)
    {
        $role = Role::findById($roleId);

        return $role->users()->with('permissions')->paginate(20);
    }
}