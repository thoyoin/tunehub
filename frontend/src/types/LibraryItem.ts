import type { Item } from './Item'
import type { User } from './User'

export interface LibraryItem {
    id: number;
    created_at: string;
    item_id: number;
    item_type: string;
    updated_at: string;
    visibility: string;
    user_id: number;
    item: Item;
    user: User
}
