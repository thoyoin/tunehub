import { computed, ref, watch } from 'vue';
import api from '@/lib/api.js'
import type { Track } from '../types/Track.js'
import type { Item } from '../types/Item.js'

let singleton = null;

export const useAudioPlayer = (audioRef) => {
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
    const currentContext = ref<Item>(null)

    watch(currentContext, async (newContext, oldContext) => {
        if (!newContext) return

        if (
            !oldContext
            || newContext.id !== oldContext.id
            || newContext.type !== oldContext.type
        ) {
            console.log('New context: ', newContext)
            await api.post('/api/recentlyPlayed', newContext)
        }
    })

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
                currentTrack.value = JSON.parse(lastPlayed)
                newEl.src = currentTrack.value.audio_url
            }

            newEl.addEventListener('timeupdate', onTimeUpdate)
            newEl.addEventListener('loadedmetadata', onLoadedMetadata)
            newEl.addEventListener('ended', onEnded)
        }
    }, { immediate: true })

    function playTrack(track, newQueue = [], item) {
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

    function toggleTrack(track, newQueue = [], item) {
        if (currentTrack.value?.id === track.id) {
            toggle()
        } else {
            playTrack(track, newQueue, item)
        }
    }

    function next() {
        if (!hasNext.value) return

        currentIndex.value++
        playTrack(queue.value[currentIndex.value])
    }

    function prev() {
        if (!hasPrev.value) return

        currentIndex.value--
        playTrack(queue.value[currentIndex.value])
    }

    function seek(e) {
        if (!audioRef.value || !duration.value) return

        const width = e.currentTarget.clientWidth
        const clickX = e.offsetX
        audioRef.value.currentTime = (clickX / width) * duration.value
    }

    function setVolume(value) {
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

    const formatTime = (seconds) => {
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
