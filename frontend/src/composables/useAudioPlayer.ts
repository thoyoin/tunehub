import { computed, type Ref, ref, watch } from "vue";

import api from '@/lib/api'

import type { Track } from "@/types/Track"
import type { AudioPlayerSingleton } from "@/types/AudioPlayerSingleton";
import type {Release} from "@/types/Release";
import type {Playlist} from "@/types/Playlist";

let singleton: AudioPlayerSingleton | null = null;

export const useAudioPlayer = (audioRef: Ref<HTMLAudioElement>) => {
    if (singleton) return singleton;

    const currentTrack = ref<Track | null>(null)
    const queue = ref<Track[]>([])
    const currentIndex = ref<number | null>(null)
    const isPlaying = ref<boolean>(false)
    const progress = ref<number>(0)
    const currentTime = ref<number>(0)
    const duration = ref<number>(0)
    const volume = ref<number>(0.1)
    const isMuted = ref<boolean>(false)
    const hasTrack = computed(() => !!currentTrack.value)
    const currentContext = ref<Release | Playlist | null>(null)
    const hasBeenListened = ref(false)
    const listenedTimeout = ref<number | null>(null)

    watch(currentTrack, async (track) => {
        hasBeenListened.value = false

        if (listenedTimeout.value) {
            clearTimeout(listenedTimeout.value)
            listenedTimeout.value = null
        }

        if (!track) return
        if (!currentContext.value) return

        // listenedTimeout.value = window.setTimeout(async () => {
            if (hasBeenListened.value) return
            hasBeenListened.value = true

            const payload = {
                ...currentContext.value,
                track_id: track.id,
                track_artist_id: track.user_id,
                release_id: track.release_id ?? null
            }

            await api.post('/api/recentlyPlayed', payload)
        // }, 20000)
    }, {immediate: true})

    const onTimeUpdate = () => {
        if (!audioRef.value?.duration) return

        progress.value = (audioRef.value.currentTime / audioRef.value.duration) * 100
        currentTime.value = audioRef.value.currentTime
    }

    const onLoadedMetadata = () => {
        duration.value = audioRef.value.duration
    }

    const onEnded = () => {
        if (hasNext.value) next()
        else isPlaying.value = false
    }

    watch(audioRef, (newEl, oldEl) => {
        if (oldEl) {
            oldEl.removeEventListener('timeupdate', onTimeUpdate)
            oldEl.removeEventListener('loadedmetadata', onLoadedMetadata)
            oldEl.removeEventListener('ended', onEnded)
        }

        if (newEl) {
            newEl.volume = volume.value

            const lastPlayed = localStorage.getItem('lastPlayed')
            if (lastPlayed) {
                const parsed: Track = JSON.parse(lastPlayed)
                currentTrack.value = parsed

                if (parsed.audio_url) {
                    newEl.src = parsed.audio_url
                }
            }

            newEl.addEventListener('timeupdate', onTimeUpdate)
            newEl.addEventListener('loadedmetadata', onLoadedMetadata)
            newEl.addEventListener('ended', onEnded)
        }
    }, { immediate: true })

    function playTrack(track: Track, newQueue: Track[], item: Release | Playlist) {
        if (!audioRef.value) return

        if (newQueue.length) queue.value = newQueue
        currentIndex.value = queue.value.findIndex(t => t.id === track.id)

        currentContext.value = item

        audioRef.value.src = track.audio_url
        audioRef.value.play()
            .then(() => isPlaying.value = true)
            .catch(e => console.error("Ошибка воспроизведения:", e))

        currentTrack.value = track

        localStorage.setItem('lastPlayed', JSON.stringify(track))
    }

    function toggle() {
        if (!audioRef.value) return

        if (audioRef.value.paused) {
            audioRef.value.play().then(() => isPlaying.value = true)
        } else {
            audioRef.value.pause()
            isPlaying.value = false
        }
    }

    function toggleVolume() {
        if (!audioRef.value) return

        isMuted.value = !isMuted.value
        audioRef.value.volume = isMuted.value ? 0 : volume.value
    }

    function toggleTrack(track: Track, newQueue: Track[], item: Release | Playlist) {
        if (currentTrack.value?.id === track.id) {
            toggle()
        } else {
            playTrack(track, newQueue, item)
        }
    }

    function next() {
        if (currentIndex.value === null) return
        const nextIndex = currentIndex.value + 1
        if (nextIndex >= queue.value.length) return

        const nextTrack = queue.value[nextIndex]
        if (!nextTrack) return

        currentIndex.value = nextIndex
        if (currentContext.value) {
            playTrack(nextTrack, queue.value, currentContext.value)
        }
    }

    function prev() {
        if (currentIndex.value === null) return
        const prevIndex = currentIndex.value - 1
        if (prevIndex < 0) return

        const prevTrack = queue.value[prevIndex]
        if (!prevTrack) return

        currentIndex.value = prevIndex
        if (currentContext.value) {
            playTrack(prevTrack, queue.value, currentContext.value)
        }
    }

    function seek(e: MouseEvent) {
        if (!audioRef.value || !duration.value) return

        const target = e.currentTarget as HTMLElement | null
        if (!target) return
        const width = target.clientWidth

        const clickX = e.offsetX
        audioRef.value.currentTime = (clickX / width) * duration.value
    }

    function setVolume(value: number) {
        volume.value = value

        if (audioRef.value) {
            audioRef.value.volume = value
            isMuted.value = value === 0
        }
    }

    const hasNext = computed(() =>
        currentIndex.value !== null && currentIndex.value + 1 < queue.value.length)

    const hasPrev = computed(() =>
        currentIndex.value !== null && currentIndex.value > 0)

    const formatTime = (seconds: number) => {
        const minutes = Math.floor(seconds / 60)
        const secs = Math.floor(seconds % 60)

        return `${minutes}:${secs.toString().padStart(2, '0')}`
    }

    singleton = {
        currentTrack, queue, currentIndex, isPlaying, progress,
        currentTime, duration, volume, isMuted, hasNext, hasPrev,
        playTrack, toggle, toggleVolume, toggleTrack, currentContext,
        next, prev, seek, formatTime, setVolume, hasTrack
    }

    return singleton;
}
