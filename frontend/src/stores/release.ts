import { defineStore } from 'pinia';
import { useLibraryStore } from "@/stores/library.js";
import { ref } from 'vue';
import api from '@/lib/api'
import type { Release } from "@/types/Release"
import type { Track } from "@/types/Track"

export const useReleaseStore = defineStore('release', () => {
    const releases = ref<Release[] | null>(null);
    const pickedRelease = ref<Release | null>(null);
    const releaseTracks = ref<Track[] | null>(null);
    const isLoading = ref<boolean>(false);

    const libraryStore = useLibraryStore();

    const fetchLatestReleases = async (): Promise<void> => {
        try {
            const response = await api.get<Release[]>(`/api/releases/latest`)

            releases.value = response.data
        } catch (error) {
            console.log(error)
        }
    }

    const getRelease = async (id: number): Promise<void> => {
        try {
            isLoading.value = true;

            const response = await api.get<[Release, Track[]]>(`/api/release/${id}`)

            pickedRelease.value = response.data.release;
            releaseTracks.value = response.data.tracks;
        } catch (error) {
            console.log(error)
        } finally {
            isLoading.value = false;
        }
    }

    const addReleaseToLikes = async (id: number): Promise<void> => {
        await api.post(`/api/releases/${id}/add`)

        await libraryStore.fetchItems()
        await getRelease(id)
    }

    const addTrackToLikes = async (id: number): Promise<void> => {
        await api.post(`/api/liked/track/${id}`)

        if (pickedRelease.value) {
            await getRelease(pickedRelease.value.id)
        } else {
            await libraryStore.getPlaylist(libraryStore.libraryItem.id)
        }

        await libraryStore.fetchItems()
    }

    const addTrackToPlaylist = async (trackId: number, playlistId: number): Promise<void> => {
        try {
            await api.post(`/api/playlist/${playlistId}/track/${trackId}`)

            if (pickedRelease.value) {
                await getRelease(pickedRelease.value.id)
            } else {
                await libraryStore.getPlaylist(libraryStore.libraryItem.id)
            }

            await libraryStore.fetchItems()
        } catch (e) {
            console.log(e)
        }

    }

    const clearPickedRelease = (): void => {
        pickedRelease.value = null
    }

    return {
        releases,
        pickedRelease,
        releaseTracks,
        fetchLatestReleases,
        addReleaseToLikes,
        addTrackToLikes,
        clearPickedRelease,
        getRelease,
        isLoading,
        addTrackToPlaylist
    }
})
