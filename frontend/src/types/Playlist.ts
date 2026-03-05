import type { LibraryItem } from "@/types/LibraryItem";
import type { User } from "@/types/User";

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
}
