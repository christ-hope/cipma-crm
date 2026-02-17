<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class UserManagementService
{
    /**
     * Créer un utilisateur staff (par admin)
     */
    public function createStaffUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Générer mot de passe temporaire
            $tempPassword = Str::random(12);

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($tempPassword),
                'is_active' => true,
                'must_change_password' => true,
            ]);

            // Assigner rôle(s)
            if (isset($data['roles'])) {
                $user->assignRole($data['roles']);
            }

            // Assigner permissions directes (optionnel)
            if (isset($data['permissions'])) {
                $user->givePermissionTo($data['permissions']);
            }

            // Envoyer email avec credentials
            $this->sendStaffCredentialsEmail($user, $tempPassword);

            return $user->load('roles', 'permissions');
        });
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(string $userId, array $data): User
    {
        $user = User::findOrFail($userId);

        $updateData = [
            'first_name' => $data['first_name'] ?? $user->first_name,
            'last_name' => $data['last_name'] ?? $user->last_name,
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? $user->phone,
            'is_active' => $data['is_active'] ?? $user->is_active,
        ];

        $user->update($updateData);

        return $user->fresh(['roles', 'permissions']);
    }

    /**
     * Assigner des rôles à un utilisateur
     */
    public function assignRoles(string $userId, array $roles): User
    {
        $user = User::findOrFail($userId);

        // Supprimer anciens rôles
        $user->roles()->detach();

        // Assigner nouveaux rôles
        $user->assignRole($roles);

        return $user->fresh(['roles', 'permissions']);
    }

    /**
     * Assigner des permissions directes à un utilisateur
     */
    public function assignPermissions(string $userId, array $permissions): User
    {
        $user = User::findOrFail($userId);

        // Supprimer anciennes permissions directes
        $user->permissions()->detach();

        // Assigner nouvelles permissions
        $user->givePermissionTo($permissions);

        return $user->fresh(['roles', 'permissions']);
    }

    /**
     * Désactiver un utilisateur
     */
    public function deactivateUser(string $userId): User
    {
        $user = User::findOrFail($userId);

        $user->update(['is_active' => false]);

        // Révoquer tous les tokens
        $user->tokens()->delete();

        return $user;
    }

    /**
     * Activer un utilisateur
     */
    public function activateUser(string $userId): User
    {
        $user = User::findOrFail($userId);

        $user->update(['is_active' => true]);

        return $user;
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     */
    public function resetPassword(string $userId): array
    {
        $user = User::findOrFail($userId);

        $tempPassword = Str::random(12);

        $user->update([
            'password' => Hash::make($tempPassword),
            'must_change_password' => true,
        ]);

        // Révoquer tous les tokens
        $user->tokens()->delete();

        // Envoyer email
        $this->sendPasswordResetEmail($user, $tempPassword);

        return [
            'user' => $user,
            'temp_password' => $tempPassword,
        ];
    }

    /**
     * Supprimer un utilisateur (soft delete)
     */
    public function deleteUser(string $userId): bool
    {
        $user = User::findOrFail($userId);

        // Ne pas permettre suppression du dernier super_admin
        if ($user->hasRole('super_admin')) {
            $adminCount = User::role('super_admin')->count();
            if ($adminCount <= 1) {
                throw new \Exception('Impossible de supprimer le dernier super administrateur');
            }
        }

        // Révoquer tokens
        $user->tokens()->delete();

        // Soft delete
        return $user->delete();
    }

    /**
     * Lister les utilisateurs avec filtres
     */
    public function listUsers(array $filters = [])
    {
        $query = User::with(['roles', 'permissions']);

        // Filtre par rôle
        if (isset($filters['role'])) {
            $query->role($filters['role']);
        }

        // Filtre par statut
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Recherche par nom/email
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    // Email methods
    private function sendStaffCredentialsEmail(User $user, string $tempPassword): void
    {
        // Mail::to($user->email)->send(new StaffCredentialsEmail($user, $tempPassword));
    }

    private function sendPasswordResetEmail(User $user, string $tempPassword): void
    {
        // Mail::to($user->email)->send(new PasswordResetEmail($user, $tempPassword));
    }
}