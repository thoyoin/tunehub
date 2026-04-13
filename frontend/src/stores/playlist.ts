import { defineStore } from "pinia";
import { ref } from "vue";

import api from "@/lib/api";
import { useAuthStore } from "@/stores/auth";
import { useLibraryStore } from "@/stores/library";

import type { LibraryItem } from "@/types/LibraryItem";
import type { Track } from "@/types/Track";
import type { Playlist } from "@/types/Playlist";

export const usePlaylistStore = defineStore("playlist", () => {
    const playlist = ref<LibraryItem | null>(null)
    const playlistTracks = ref<Track[]>([])
    const userPlaylists = ref<Playlist[]>([])
    const isLoading = ref(false)
    const auth = useAuthStore()

    const libraryStore = useLibraryStore()

    const getPlaylist = async (playlistId: number | string) => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                playlistItem: LibraryItem,
                tracks: Track[]
            }>(`/api/playlist/${playlistId}`)

            playlist.value = response.data.playlistItem;
            playlistTracks.value = response.data.tracks;
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const addTrackToLikes = async (id: number) => {
        await api.post(`/api/liked/track/${id}`)

        if (playlist.value) {
            await getPlaylist(playlist.value.item_id)
        }

        await libraryStore.fetchItems();
    }

    const addTrackToPlaylist = async (id: number, playlistId: number) => {
        try {
            await api.post(`/api/playlist/${playlistId}/track/${id}`)

            if (playlist.value) {
                await getPlaylist(playlist.value.item_id)
            }

            await libraryStore.fetchItems();
        } catch (e) {
            console.error(e)
        }
    }

    const fetchUserPlaylists = async () => {
        if (auth.user) {
            try {
                const { data } = await api.get<{
                    playlists: Playlist[]
                }>(`/api/playlists`)

                userPlaylists.value = data.playlists
            } catch (e) {
                console.error(e);
            }
        }
    }

    return {
        playlist, playlistTracks, isLoading, getPlaylist, fetchUserPlaylists, userPlaylists,
        addTrackToLikes, addTrackToPlaylist
    }
})
