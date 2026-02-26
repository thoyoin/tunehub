export interface Playlist {
    id: number;
    item_type: string;
    title: string;
    slug: string;
    description: string | undefined;
    cover_url: string;
    created_at: string;
    updated_at: string;
    user_id: number;
}
