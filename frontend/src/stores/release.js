import { defineStore } from 'pinia';
import { useLibraryStore } from "@/stores/library.js";
import { ref } from 'vue';
import api from '@/lib/api'

export const useReleaseStore = defineStore('release', () => {
    const releases = ref(null);
    const pickedRelease = ref(null);
    const releaseTracks = ref(null);
    const isReleaseLiked = ref(false);
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
            isReleaseLiked.value = response.data.isReleaseLiked
        } catch (error) {
            console.log(error)
        } finally {
            isLoading.value = false;
        }
    }

    const addReleaseToLikes = async (id) => {
        const response = await api.post(`/api/releases/${id}/add`)

        isReleaseLiked.value = response.data.liked

        await libraryStore.fetchItems()
    }

    const addTrackToLikes = async (id) => {
        await api.post(`/api/tracks/${id}/add`)

        if (pickedRelease.value) {
            await getRelease(pickedRelease.value.id)
        } else {
            await libraryStore.getPlaylist(libraryStore.libraryItem.id)
        }

        await libraryStore.fetchItems()
    }

    const clearPickedRelease = () => {
        pickedRelease.value = null
    }

    return {
        releases,
        pickedRelease,
        releaseTracks,
        fetchLatestReleases,
        isReleaseLiked,
        addReleaseToLikes,
        addTrackToLikes,
        clearPickedRelease,
        getRelease,
        isLoading,
    }
})
