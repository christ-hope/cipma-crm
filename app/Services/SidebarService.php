<?php

namespace App\Services;

use App\Models\User;

class SidebarService
{
    /**
     * Construit les items de navigation en fonction des rôles/permissions de l'utilisateur.
     * Retourné dans le InertiaMiddleware pour être disponible dans tous les layouts React.
     */
    public function build(User $user): array
    {
        // ── Portail Étudiant ──────────────────────────────────────────────
        if ($user->hasRole('student')) {
            return [
                $this->item('portal.dashboard', 'Tableau de bord', 'LayoutDashboard', 'portal.dashboard'),
                $this->item('portal.formations', 'Mes formations', 'BookOpen', 'portal.formations.index'),
                $this->item('portal.evaluations', 'Mes résultats', 'ClipboardList', 'portal.evaluations.index'),
                $this->item('portal.payments', 'Mes paiements', 'CreditCard', 'portal.payments.index'),
                $this->item('portal.certificates', 'Mes certificats', 'Award', 'portal.certificates.index'),
                $this->item('portal.profile', 'Mon profil', 'User', 'portal.profile.show'),
            ];
        }

        // ── Admin / Staff ─────────────────────────────────────────────────
        $items = [];

        $items[] = $this->item('admin.dashboard', 'Tableau de bord', 'LayoutDashboard', 'admin.dashboard');

        if ($user->canAny(['applications.view', 'applications.approve'])) {
            $items[] = $this->item('admin.applications', 'Candidatures', 'FileText', 'admin.applications.index');
        }

        if ($user->can('students.view')) {
            $items[] = $this->item('admin.students', 'Étudiants', 'Users', 'admin.students.index');
        }

        // Section Académique (groupée)
        $academicChildren = [];
        if ($user->can('formations.view')) {
            $academicChildren[] = $this->item('admin.formations', 'Formations', 'BookOpen', 'admin.formations.index');
            $academicChildren[] = $this->item('admin.classes', 'Classes', 'Calendar', 'admin.classes.index');
        }
        if ($user->can('evaluations.create')) {
            $academicChildren[] = $this->item('admin.evaluations', 'Évaluations', 'ClipboardList', 'admin.evaluations.index');
        }
        if (!empty($academicChildren)) {
            $items[] = $this->group('academic', 'Académique', 'GraduationCap', $academicChildren);
        }

        if ($user->canAny(['payments.view', 'payments.validate'])) {
            $items[] = $this->item('admin.payments', 'Paiements', 'CreditCard', 'admin.payments.index');
        }

        if ($user->canAny(['certificates.issue', 'certificates.view'])) {
            $items[] = $this->item('admin.certificates', 'Certificats', 'Award', 'admin.certificates.index');
        }

        // Section Paramètres (super_admin + it uniquement)
        if ($user->hasAnyRole(['super_admin', 'it'])) {
            $settingsChildren = [
                $this->item('admin.users', 'Utilisateurs', 'UserCog', 'admin.users.index'),
            ];

            if ($user->hasRole('super_admin')) {
                $settingsChildren[] = $this->item('admin.roles', 'Rôles & permissions', 'Shield', 'admin.roles.index');
                $settingsChildren[] = $this->item('admin.formation-types', 'Types de formations', 'Tag', 'admin.formation-types.index');
            }

            $items[] = $this->group('settings', 'Paramètres', 'Settings', $settingsChildren);
        }

        return $items;
    }

    private function item(string $key, string $label, string $icon, string $route): array
    {
        return compact('key', 'label', 'icon', 'route');
    }

    private function group(string $key, string $label, string $icon, array $children): array
    {
        return compact('key', 'label', 'icon', 'children');
    }
}