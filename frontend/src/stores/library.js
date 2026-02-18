import {defineStore} from "pinia";
import { useAuthStore } from "@/stores/auth.js";
import {ref, watch} from "vue";
import api from "@/lib/api.js";

export const useLibraryStore = defineStore('library',() => {
    const items = ref([]);
    const isLibraryLoading = ref(false);
    const isPlaylistLoading = ref(false);
    const selectedLibraryItem = ref(null);
    const libraryItem = ref(null);
    const itemTracks = ref([]);
    const isRelease = ref(false);
    const isReady = ref(false);
    const userPlaylists = ref([]);

    const auth = useAuthStore();

    async function fetchItems() {
        if (auth.user) {
            isLibraryLoading.value = true;

            try {
                const { data } = await api.get(`/api/libraryItems`)

                items.value = data.libraryItems;
                console.log(items.value);
            } catch (e) {
                console.error(e);
            } finally {
                isLibraryLoading.value = false;
                isReady.value = true;
            }
        }
    }

    async function fetchUserPlaylists() {
        if (auth.user) {
            try {
                const { data } = await api.get(`/api/playlists`)

                userPlaylists.value = data.playlists
            } catch (e) {
                console.error(e);
            }
        }
    }

    async function getPlaylist(playlist) {
        try {
            isPlaylistLoading.value = true;

            const response = await api.get(`/api/playlist/${playlist}`)

            libraryItem.value = response.data.playlist;
            itemTracks.value = response.data.tracks;
            console.log(itemTracks.value);
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
        } catch (e) {
            console.error(e)

            throw e
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
        userPlaylists,
        fetchUserPlaylists
    };
})
