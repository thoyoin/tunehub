import {defineStore} from "pinia";
import { useAuthStore } from "@/stores/auth.js";
import {ref, watch} from "vue";
import api from "@/lib/api.js";
import { useRouter } from "vue-router";

export const useLibraryStore = defineStore('library',() => {
    const items = ref([]);
    const isLibraryLoading = ref(false);
    const isPlaylistLoading = ref(false);
    const selectedLibraryItem = ref(null);
    const libraryItem = ref(null);
    const itemTracks = ref([]);
    const isRelease = ref(false);
    const isReady = ref(false);

    const auth = useAuthStore();
    const router = useRouter();

    async function fetchItems() {
        if (auth.user) {
            isLibraryLoading.value = true;

            try {
                const { data } = await api.get(`/api/libraryItems`)

                items.value = data.libraryItems;
            } catch (e) {
                console.error(e);
            } finally {
                isLibraryLoading.value = false;
                isReady.value = true;
            }
        }
    }

    async function getPlaylist(playlist) {
        try {
            isPlaylistLoading.value = true;

            const response = await api.get(`/api/playlist/${playlist}`)

            libraryItem.value = response.data.playlist;
            itemTracks.value = response.data.tracks;
        } catch (e) {
            console.error(e);
        } finally {
            isPlaylistLoading.value = false;
        }
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
                await router.push({
                    name: "release",
                    params: { releaseId: libraryItem.value.item.id }
                })

            } else {
                await router.push({
                    name: "playlist",
                    params: { playlistId: libraryItem.value.item.id }
                })
            }
        } catch (error) {
            console.error(error)
        }
    })

    return {
        items,
        isLibraryLoading,
        isPlaylistLoading,
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
        isReady,
    };
})
