import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api'
import { useToast } from "vue-toastification";
import type { Release } from "@/types/Release"
import type { Track } from "@/types/Track"

interface ArtistEarnings {
    date: string;
    earnings: string;
    total_earnings: number;
}

interface ArtistStreamsDaily {
    date: string;
    plays: string;
}

interface ArtistTopTracks {
    track: Track;
    streams: string;
}

export const useArtistStore = defineStore('artistStudio', () => {
    const tracks = ref<Track[]>([])
    const releases = ref<Release[]>([])
    const selectedView = ref<string>('tracks')
    const editingItem = ref<Release | Track | null>(null)
    const isLoading = ref<boolean>(false)
    const artistStreamsTotal = ref<number | null>(null)
    const artistEarnings = ref<ArtistEarnings | null>(null)
    const artistBalance = ref<number | null>(null)
    const artistStreamsDaily = ref<ArtistStreamsDaily[] | null>(null)
    const artistTopTracks = ref<ArtistTopTracks[] | null>(null)
    const artistTopReleases = ref<Release[] | null>(null)

    const toast = useToast()

    const fetchArtistTopReleases = async () => {
        try {
            isLoading.value = true

            const response = await api.get<Release[]>('/api/artists/top-releases')

            artistTopReleases.value = response.data
        } catch (error) {
            console.log(error)
        } finally {
            isLoading.value = false
        }
    }

    const fetchArtistTopTracks = async () => {
        try {
            isLoading.value = true

            const response = await api.get<ArtistTopTracks[]>('/api/artists/top-tracks')

            artistTopTracks.value = response.data
        } catch (error) {
            console.error(error)
        } finally {
            isLoading.value = false
        }
    }

    const fetchArtistStreamsDaily = async () => {
        try {
            isLoading.value = true

            const response = await api.get<{
                streamsDaily: ArtistStreamsDaily[]
            }>('/api/artists/streams/daily')

            artistStreamsDaily.value = response.data.streamsDaily
        } catch (error) {
            console.log(error)
        } finally {
            isLoading.value = false
        }
    }

    const fetchArtistStreamsTotal = async () => {
        try {
            isLoading.value = true

            const response = await api.get<{
                artistStreams: number
            }>('/api/artists/streams')

            artistStreamsTotal.value = response.data.artistStreams
        } catch (error) {
            console.error(error)
        } finally {
            isLoading.value = false
        }
    }

    const fetchArtistEarnings = async () => {
        try {
            isLoading.value = true

            const response = await api.get<{
                earnings: ArtistEarnings
            }>('/api/artists/earnings')

            artistEarnings.value = response.data.earnings

            artistBalance.value = response.data.earnings.total_earnings
        } catch (error) {
            console.error(error)
        } finally {
            isLoading.value = false
        }
    }

    const fetchTracks = async (): Promise<void> => {
        try {
            const response = await api.get<{
                tracks: Track[]
            }>('/api/artists/tracks')

            tracks.value = response.data.tracks
        } catch (e) {
            console.error(e)
        }
    }

    const fetchReleases = async (): Promise<void> => {
        try {
            const response = await api.get<{
                releases: Release[]
            }>('/api/artists/releases')

            releases.value = response.data.releases
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

    const viewEarnings = (): void => {
        selectedView.value = 'earnings'
    }

    const viewLibrary = (): void => {
        selectedView.value = 'library'
    }

    const viewAnalytics = (): void => {
        selectedView.value = 'analytics'
    }

    const viewMerch = (): void => {
        selectedView.value = 'merch'
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
        viewEarnings,
        viewAnalytics,
        viewLibrary,
        viewMerch,
        deleteTrack,
        deleteRelease,
        pullEditingItem,
        editingItem,
        publishRelease,
        isLoading,
        fetchArtistStreamsTotal,
        fetchArtistStreamsDaily,
        fetchArtistEarnings,
        fetchArtistTopTracks,
        fetchArtistTopReleases,
        artistStreamsTotal,
        artistEarnings,
        artistBalance,
        artistStreamsDaily,
        artistTopTracks,
        artistTopReleases,
     };
})
