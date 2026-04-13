import type { ComputedRef, Ref } from "vue";

import type { Track } from "@/types/Track";
import type { Release } from "@/types/Release";
import type { Playlist } from "@/types/Playlist";

export interface AudioPlayerSingleton {
    currentTrack: Ref<Track | null>
    queue: Ref<Track[]>
    currentIndex: Ref<number | null>
    isPlaying: Ref<boolean>
    progress: Ref<number>
    currentTime: Ref<number>
    duration: Ref<number>
    volume: Ref<number>
    isMuted: Ref<boolean>
    hasNext: ComputedRef<boolean>
    hasPrev: ComputedRef<boolean>
    hasTrack: ComputedRef<boolean>
    currentContext: Ref<Release | Playlist | null>

    playTrack: (track: Track, newQueue: Track[], item: Release | Playlist) => void
    toggle: () => void
    toggleVolume: () => void
    toggleTrack: (track: Track, newQueue: Track[], item: Release | Playlist) => void
    next: () => void
    prev: () => void
    seek: (e: MouseEvent) => void
    formatTime: (seconds: number) => string
    setVolume: (value: number) => void
}
