import {defineStore} from "pinia";
import { useReleaseStore } from "@/stores/release.js";
import { useAuthStore } from "@/stores/auth.js";
import {ref, watch} from "vue";
import api from "@/lib/api.js";

export const useLibraryStore = defineStore('library',() => {
    const items = ref([]);
    const isLoading = ref(false);
    const selectedLibraryItem = ref(null);
    const libraryItem = ref(null);
    const itemTracks = ref([]);
    const isRelease = ref(false);

    const releaseStore = useReleaseStore();
    const auth = useAuthStore();

    async function fetchItems() {
        if (auth.user) {
            isLoading.value = true;

            try {
                const { data } = await api.get(`/api/libraryItems`)

                items.value = data.libraryItems;
            } catch (e) {
                console.error(e);
            } finally {
                isLoading.value = false;
            }
        }
    }

    async function getPlaylist(playlist) {
        const response = await api.get(`/api/playlist/${playlist}`)

        libraryItem.value = response.data.playlist;
        itemTracks.value = response.data.tracks;
    }

    async function createPlaylist() {
        try {
            const { data } = await api.post('/api/playlist',)

            items.value.push(data.libraryItem)
        } catch (error) {
            console.log(error)
        }
    }

    function clearAllSelectedItems() {
        libraryItem.value = null;
        selectedLibraryItem.value = null;
        releaseStore.clearPickedRelease();
    }

    function clearSelectedItem() {
        libraryItem.value = null;
        selectedLibraryItem.value = null;
    }

    function selectLibraryItem(id) {
        selectedLibraryItem.value = id;
    }

    watch(selectedLibraryItem, async (id) => {
        if (!id) return

        try {
            const response = await api.get(`/api/libraryItems/${id}`)

            libraryItem.value = response.data.libraryItem
            isRelease.value = response.data.isRelease;

            if (isRelease.value) {
                await releaseStore.getRelease(libraryItem.value.item.id)

            } else {

                await getPlaylist(libraryItem.value.item.id)
                releaseStore.clearPickedRelease();
            }
        } catch (error) {
            console.error(error)
        }
    })

    return {
        items,
        isLoading,
        fetchItems,
        createPlaylist,
        selectLibraryItem,
        selectedLibraryItem,
        clearAllSelectedItems,
        clearSelectedItem,
        getPlaylist,
        libraryItem,
        itemTracks,
        isRelease,
    };
})
