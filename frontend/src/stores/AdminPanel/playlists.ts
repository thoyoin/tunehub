import { defineStore } from "pinia";
import { ref } from "vue";
import type { PaginatedResponse } from "@/types/PaginatedResponse";
import type { Playlist } from "@/types/Playlist";
import api from "@/lib/api";

export const usePlaylistsStore = defineStore('playlists', () => {
    const playlists = ref<PaginatedResponse<Playlist[]> | null>(null)
    const isLoading = ref<boolean>(false)
    const selectedView = ref<string>('all')
    const viewingPlaylist = ref<Playlist | null>(null)

    const fetchPlaylists = async (page: number = 1) => {
        try {
            isLoading.value = true

            const response = await api.get<PaginatedResponse<Playlist[]>>(
                '/api/admin/playlists', {
                    params: { page: page }
                },
            );

            playlists.value = response.data
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false
        }
    }

    const selectView = (view: string) => {
        selectedView.value = view
    }

    const setViewingPlaylist = (playlist: Playlist) => {
        viewingPlaylist.value = playlist
    }

    return {
        playlists, fetchPlaylists, selectedView, selectView, viewingPlaylist, setViewingPlaylist,
        isLoading,
    }
})
