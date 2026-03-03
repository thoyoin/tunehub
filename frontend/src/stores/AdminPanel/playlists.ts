import { defineStore } from "pinia";
import { ref, watch } from "vue";
import type { PaginatedResponse } from "@/types/PaginatedResponse";
import type { Playlist } from "@/types/Playlist";
import api from "@/lib/api";

export const usePlaylistsStore = defineStore('playlists', () => {
    const playlists = ref<PaginatedResponse<Playlist[]> | null>(null)
    const hiddenPlaylists = ref<PaginatedResponse<Playlist[]> | null>(null)
    const isLoading = ref<boolean>(false)
    const selectedView = ref<string>('all')
    const viewingPlaylist = ref<Playlist | null>(null)

    const fetchPlaylists = async (page: number = 1) => {
        try {
            isLoading.value = true

            const response = await api.get<{
                allPlaylists: PaginatedResponse<Playlist[]>;
                hiddenPlaylists: PaginatedResponse<Playlist[]>;
            }>(
                '/api/admin/playlists', {
                    params: { page: page }
                },
            );

            playlists.value = response.data.allPlaylists
            hiddenPlaylists.value = response.data.hiddenPlaylists
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false
        }
    }

    const updateVisibility = async (visibility: string) => {
        try {
            isLoading.value = true;

            const response = await api.patch<{ message: string; visibility: string}>(
                `/api/playlist/${viewingPlaylist.value?.id}`, {
                    visibility: visibility,
                }
            )

            await fetchPlaylists()

            viewingPlaylist.value.visibility = response.data.visibility;
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
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
        isLoading, hiddenPlaylists, updateVisibility,
    }
})
