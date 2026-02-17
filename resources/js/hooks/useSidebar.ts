import { usePage } from '@inertiajs/react'
import type { PageProps, SidebarItem } from '@/types/inertia'

export function useSidebar() {
    const { sidebar } = usePage<PageProps>().props
    return sidebar as SidebarItem[]
}