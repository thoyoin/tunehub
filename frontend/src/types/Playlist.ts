import type { LibraryItem } from "@/types/LibraryItem";
import type { User } from "@/types/User";
import type { Track } from "@/types/Track";

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
    libraryItem: LibraryItem;
    user: User;
    visibility: string;
    playlist_duration: string;
    tracks: Track[];
    creation_date: string;
    is_hidden: boolean;
}
