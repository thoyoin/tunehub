export interface Release {
    id: number;
    artist: string;
    cover_url: string;
    created_at: string;
    isReleaseLiked: boolean;
    item_type: string;
    release_date: string;
    release_type: string;
    released_in: string;
    status: 'pending' | 'rejected' | 'approved' | 'published';
    title: string;
    updated_at: string;
    user_id: number;
}
