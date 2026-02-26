import type { Playlist } from './Playlist.js'
import type { Track } from './Track.js'

export interface User {
    id: number
    email: string
    username: string
    slug: string
    roles: string
    profile_picture: string
    updated_at: string
    joined_at: string
    created_at: string
    playlists: Playlist[]
    tracks: Track[]
}
