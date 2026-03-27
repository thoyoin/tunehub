import {defineStore} from "pinia";
import { useAuthStore } from "@/stores/auth";
import {ref, watch} from "vue";
import api from "@/lib/api";
import type { Item } from "@/types/Item"
import type { LibraryItem } from "@/types/LibraryItem"
import type { Playlist } from "@/types/Playlist"
import type { Track } from "@/types/Track"
import type { Release } from "@/types/Release";

export const useLibraryStore = defineStore('library',() => {
    const items = ref<LibraryItem[]>([]);
    const isLibraryLoading = ref<boolean>(false);
    const isPlaylistLoading = ref<boolean>(false);
    const selectedLibraryItem = ref<LibraryItem | null>(null);
    const libraryItem = ref<LibraryItem | null>(null);
    const itemTracks = ref<Track[]>([]);
    const isRelease = ref<boolean>(false);
    const userPlaylists = ref<Playlist[]>([]);
    const isLoading = ref<boolean>(false);
    const playlistVisibility = ref<string>(
        libraryItem.value?.visibility === 'public' ? 'public' : 'private'
    );
    const isLibraryDataLoaded = ref<boolean>(false);

    const auth = useAuthStore();

    const fetchLibraryData = async (force: boolean = false) => {
        if (isLibraryDataLoaded.value && !force) return;

        await Promise.all([
            fetchItems(),
        ])

        isLibraryDataLoaded.value = true
    }

    async function fetchItems(): Promise<void> {
        if (auth.user) {
            isLibraryLoading.value = true;

            try {
                const response = await api.get<{
                    libraryItems: LibraryItem[]
                }>(`/api/libraryItems`)

                items.value = response.data.libraryItems;
            } catch (e) {
                console.error(e);
            } finally {
                isLibraryLoading.value = false;
            }
        }
    }

    async function createPlaylist() {
        try {
            const { data } = await api.post<Playlist>('/api/playlist',)

            items.value.push(data.libraryItem)
        } catch (error) {
            console.log(error)
        }
    }

    async function updateVisibility() {
        try {
            isLoading.value = true;

            if (!libraryItem.value) return

            const response = await api.patch<{ message: string; visibility: string}>(
                `/api/playlist/${libraryItem.value.item.id}`, {
                    visibility: playlistVisibility.value,
                }
            )
            if (libraryItem.value.item.visibility) {
                libraryItem.value.item.visibility = response.data.visibility;
            }
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    function setVisibility(visibility: string) {
        playlistVisibility.value = visibility;
    }

    function clearAllSelectedItems(): void {
        libraryItem.value = null;
        selectedLibraryItem.value = null;
    }

    function clearSelectedItem(): void {
        libraryItem.value = null;
        selectedLibraryItem.value = null;
    }

    function selectLibraryItem(item: LibraryItem): void {
        selectedLibraryItem.value = item;
    }

    watch(selectedLibraryItem, async (id) => {
        if (!id) return

        try {
            const response = await api.get<{
                libraryItem: LibraryItem;
                isRelease: boolean;
            }>(`/api/libraryItems/${id}`)

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
        libraryItem,
        itemTracks,
        isRelease,
        userPlaylists,
        isLoading,
        updateVisibility,
        playlistVisibility,
        setVisibility,
        fetchLibraryData,
    };
})
