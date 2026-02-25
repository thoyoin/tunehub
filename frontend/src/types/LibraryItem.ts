import type { Item } from './Item.js'
import type { User } from './User.js'

export interface LibraryItem {
    id: number;
    created_at: string;
    item_id: number;
    item_type: string;
    updated_at: string;
    user_id: number;
    item: Item;
    user: User
}
