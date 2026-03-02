export interface Track {
    added_ago: string | null;
    artist: string;
    cover_url: string;
    created_at: string;
    duration: number;
    formatted_duration: string;
    id: number;
    is_liked: boolean;
    position: number;
    release_date: string;
    release_id: number;
    released_in: string;
    title: string;
    updated_at: string;
    audio_url: string;
    user_id: number;
    playlist_ids: number[];
    playlists: {
        id: number;
        pivot: {
            playlist_id: number;
            track_id: number;
            created_at: string;
            updated_at: string;
        }
    }
}
