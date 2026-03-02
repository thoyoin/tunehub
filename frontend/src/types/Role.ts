export interface Role {
    id: number,
    name: string,
    slug: string,
    created_at: string | null,
    updated_at: string | null,
    pivot: {
        user_id: number,
        role_id: number,
        started_at: string,
        ends_at: string,
        created_at: string,
        updated_at: string
    }
}
