<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            // Applications
            'applications.view',
            'applications.approve',
            'applications.reject',
            
            // Students
            'students.view',
            'students.manage',
            
            // Evaluations
            'evaluations.create',
            'evaluations.update',
            'evaluations.validate',
            
            // Payments
            'payments.view',
            'payments.validate',
            'payment-plans.manage',
            
            // Certificates
            'certificates.issue',
            'certificates.revoke',
            
            // Admin
            'users.manage',
            'roles.manage',
            'permissions.manage',
            'formation-types.manage',
            'system.configure',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Roles
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $responsableAcademique = Role::create(['name' => 'responsable_academique']);
        $responsableAcademique->givePermissionTo([
            'applications.view',
            'applications.approve',
            'applications.reject',
            'students.view',
            'certificates.issue',
            'certificates.revoke',
        ]);

        $comptabilite = Role::create(['name' => 'comptabilite']);
        $comptabilite->givePermissionTo([
            'payments.view',
            'payments.validate',
            'payment-plans.manage',
        ]);

        $formateur = Role::create(['name' => 'formateur']);
        $formateur->givePermissionTo([
            'evaluations.create',
            'evaluations.update',
            'students.view',
        ]);

        $it = Role::create(['name' => 'it']);
        $it->givePermissionTo([
            'users.manage',
            'system.configure',
        ]);

        Role::create(['name' => 'student']);
    }
}