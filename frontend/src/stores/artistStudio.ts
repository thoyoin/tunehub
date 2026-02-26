import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api'
import { useToast } from "vue-toastification";
import type { Release } from "@/types/Release"
import type { Track } from "@/types/Track"

export const useArtistStore = defineStore('artistStudio', () => {
    const tracks = ref<Track[]>([])
    const releases = ref<Release[]>([])
    const selectedView = ref<string>('tracks')
    const editingItem = ref<Release | Track | null>(null)
    const isLoading = ref<boolean>(false)

    const toast = useToast()

    const fetchTracks = async (): Promise<void> => {
        try {
            const response = await api.get<Track[]>('/api/artists/tracks')

            tracks.value = response.data
        } catch (e) {
            console.error(e)
        }
    }

    const fetchReleases = async (): Promise<void> => {
        try {
            const response = await api.get<Release[]>('/api/artists/releases')

            releases.value = response.data
        } catch (e) {
            console.error(e)
        }
    }

    const deleteTrack = async (id: number): Promise<void> => {
        try {
            await api.delete(`/api/track/${id}`)

            await fetchTracks()
            await fetchReleases()

            toast.success('Track successfully deleted!')
        } catch (e) {
            console.error(e)

            toast.error('Something went wrong.')
        }
    }

    const deleteRelease = async (id: number): Promise<void> => {
        try {
            await api.delete(`/api/release/${id}`)

            await fetchReleases()
            await fetchTracks()

            toast.success('release successfully deleted!')
        } catch (e) {
            console.error(e)

            toast.error('Something went wrong.')
        }
    }

    const publishRelease = async (releaseId: number): Promise<void> => {
        try {
            isLoading.value = true

            await api.patch(`/api/release/${releaseId}/publish`)

            await fetchReleases()
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false
        }
    }

    const viewTracks = (): void => {
        selectedView.value = 'tracks'
    }

    const viewReleases = (): void => {
        selectedView.value = 'releases'
    }

    const pullEditingItem = (item: Track | Release): void => {
        editingItem.value = item
    }

     return {
        tracks,
        releases,
        selectedView,
        fetchReleases,
        fetchTracks,
        viewTracks,
        viewReleases,
        deleteTrack,
        deleteRelease,
        pullEditingItem,
        editingItem,
         publishRelease,
         isLoading,
     };
})
