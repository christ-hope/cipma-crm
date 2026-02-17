import { usePage } from '@inertiajs/react'
import type { PageProps, AuthUser } from '@/types/inertia'

export function useAuth() {
    const { auth } = usePage<PageProps>().props
    const user = auth.user as AuthUser | null

    const hasRole = (role: string | string[]): boolean => {
        if (!user) return false
        const roles = Array.isArray(role) ? role : [role]
        return roles.some(r => user.roles.includes(r))
    }

    const can = (permission: string): boolean => {
        if (!user) return false
        return user.permissions.includes(permission) || user.roles.includes('super_admin')
    }

    const canAny = (permissions: string[]): boolean =>
        permissions.some(p => can(p))

    return {
        user,
        hasRole,
        can,
        canAny,
        isStudent: () => hasRole('student'),
        isAdmin: () => hasRole('super_admin'),
        isStaff: () => hasRole(['super_admin', 'responsable_academique', 'comptabilite', 'formateur', 'it']),
    }
}
