import Subscription from "@/pages/subscription/Subscription.vue";

import type { Playlist } from './Playlist'
import type { Track } from './Track'
import type { Role } from './Role'
import type { ArtistMerch } from "@/types/ArtistMerch";

interface Subscription {
    created_at: string
    ends_at: string | null
    id: number
    quantity: number
    stripe_id: string
    stripe_price: string
    stripe_status: string
    trial_ends_at: string | null
    type: string
    updated_at: string
    user_id: number
    item?: {
        created_at: string
        updated_at: string
        id: number
        meter_event_name: string | null
        meter_id: number | null
        quantity: number
        stripe_id: string
        stripe_price: string
        stripe_product: string
        subscription_id: number
    }
}

export interface User {
    id: number
    email: string
    username: string
    slug: string
    role: string
    profile_picture: string
    updated_at: string
    joined_at: string
    created_at: string
    playlists: Playlist[]
    tracks: Track[]
    roles: Role[]
    is_subscribed: boolean
    subscriptions: Subscription[]
    products?: ArtistMerch[]
}

export interface UserDetailed {
    id: number
    email: string
    username: string
    slug: string
    role: string
    profile_picture: string
    updated_at: string
    joined_at: string
    created_at: string
    playlists_count: number
    tracks_count: number
    roles: Role[]
    is_subscribed: boolean
    subscriptions: Subscription[]
    products?: ArtistMerch[]
}
