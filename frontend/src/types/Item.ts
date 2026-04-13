import type { Track } from './Track'

export interface Item {
    created_at: string;
    updated_at: string;
    user_id: number;
    id: number;
    title: string;
    item_type: string;
    description: string;
    cover_url: string;
    slug: string;
    tracks: Track[];
    visibility: string;
    track_id?: number | null;
}
