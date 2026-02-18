import { defineStore } from 'pinia';
import { useLibraryStore } from "@/stores/library.js";
import { ref } from 'vue';
import api from '@/lib/api'

export const useReleaseStore = defineStore('release', () => {
    const releases = ref(null);
    const pickedRelease = ref(null);
    const releaseTracks = ref(null);
    const isLoading = ref(false);

    const libraryStore = useLibraryStore();

    const fetchLatestReleases = async () => {
        try {
            const response = await api.get(`/api/releases/latest`)

            releases.value = response.data
        } catch (error) {
            console.log(error)
        }
    }

    const getRelease = async (id) => {
        try {
            isLoading.value = true;

            const response = await api.get(`/api/release/${id}`)

            pickedRelease.value = response.data.release;
            releaseTracks.value = response.data.tracks;
        } catch (error) {
            console.log(error)
        } finally {
            isLoading.value = false;
        }
    }

    const addReleaseToLikes = async (id) => {
        await api.post(`/api/releases/${id}/add`)

        await libraryStore.fetchItems()
        await getRelease(id)
    }

    const addTrackToLikes = async (id) => {
        await api.post(`/api/liked/track/${id}`)

        if (pickedRelease.value) {
            await getRelease(pickedRelease.value.id)
        } else {
            await libraryStore.getPlaylist(libraryStore.libraryItem.id)
        }

        await libraryStore.fetchItems()
    }

    const addTrackToPlaylist = async (track, playlist) => {
        try {
            await api.post(`/api/playlist/${playlist}/track/${track}`)

            if (pickedRelease.value) {
                await getRelease(pickedRelease.value.id)
            } else {
                await libraryStore.getPlaylist(libraryStore.libraryItem.id)
            }
        } catch (e) {
            console.log(e)
        }

    }

    const clearPickedRelease = () => {
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
