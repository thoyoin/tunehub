import {defineStore} from "pinia";
import { useAuthStore } from "@/stores/auth.ts";
import {ref, watch} from "vue";
import api from "@/lib/api.ts";
import type { Item } from '../types/Item.js'
import type { LibraryItem } from '../types/LibraryItem.js'
import type { Playlist } from '../types/Playlist.js'
import type { Track } from '../types/Track.js'

export const useLibraryStore = defineStore('library',() => {
    const items = ref<Item[]>([]);
    const isLibraryLoading = ref<boolean>(false);
    const isPlaylistLoading = ref<boolean>(false);
    const selectedLibraryItem = ref<LibraryItem | null>(null);
    const libraryItem = ref<LibraryItem | null>(null);
    const itemTracks = ref<Track[]>([]);
    const isRelease = ref<boolean>(false);
    const isReady = ref<boolean>(false);
    const userPlaylists = ref<Playlist[]>([]);

    const auth = useAuthStore();

    async function fetchItems(): Promise<void> {
        if (auth.user) {
            isLibraryLoading.value = true;

            try {
                const { data } = await api.get<LibraryItem[]>(`/api/libraryItems`)

                items.value = data.libraryItems;
            } catch (e) {
                console.error(e);
            } finally {
                isLibraryLoading.value = false;
                isReady.value = true;
            }
        }
    }

    async function fetchUserPlaylists(): Promise<void> {
        if (auth.user) {
            try {
                const { data } = await api.get<Playlist[]>(`/api/playlists`)

                userPlaylists.value = data.playlists
            } catch (e) {
                console.error(e);
            }
        }
    }

    async function getPlaylist(playlistId: number): Promise<[Playlist, Track[]]> {
        try {
            isPlaylistLoading.value = true;

            const response = await api.get<[Playlist, Track[]]>(`/api/playlist/${playlistId}`)

            libraryItem.value = response.data.playlist;
            itemTracks.value = response.data.tracks;
        } catch (e) {
            console.error(e);
        } finally {
            isPlaylistLoading.value = false;
        }
    }

    async function createPlaylist(): Promise<Playlist> {
        try {
            const { data } = await api.post<Playlist>('/api/playlist',)

            items.value.push(data.libraryItem)
        } catch (error) {
            console.log(error)
        }
    }

    function clearAllSelectedItems(): void {
        libraryItem.value = null;
        selectedLibraryItem.value = null;
    }

    function clearSelectedItem(): void {
        libraryItem.value = null;
        selectedLibraryItem.value = null;
    }

    function selectLibraryItem(id: number): void {
        selectedLibraryItem.value = id;
    }

    watch(selectedLibraryItem, async (id: number): Promise<void> => {
        if (!id) return

        try {
            const response = await api.get<LibraryItem[]>(`/api/libraryItems/${id}`)

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
