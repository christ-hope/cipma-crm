export interface AuthUser {
    id: string
    first_name: string
    last_name: string
    full_name: string
    email: string
    must_change_password: boolean
    is_active: boolean
    roles: string[]
    permissions: string[]
    student_id?: string
    student_number?: string
}

export interface SidebarItem {
    key: string
    label: string
    icon: string
    route?: string
    children?: SidebarItem[]
}

export interface Flash {
    success?: string
    error?: string
    info?: string
}

export interface AppConfig {
    name: string
    locale: string
    currency: string
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: { user: AuthUser | null }
    sidebar: SidebarItem[]
    flash: Flash
    app: AppConfig
}
