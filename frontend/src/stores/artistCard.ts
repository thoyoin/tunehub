import { defineStore } from 'pinia';
import { ref } from "vue";
import type { User } from "@/types/User";
import api from "@/lib/api";
import type { Release } from "@/types/Release";
import type { Track } from "@/types/Track";

export const useArtistCardStore = defineStore('artistCard', () => {
    const isLoading = ref<boolean>(false);
    const artist = ref<User | null>(null);
    const artistLatestRelease = ref<Release | null>(null);
    const artistTopSongs = ref<Track[] | null>(null);

    const fetchArtist = async (id: number) => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                artist: User
            }>(`/api/artist/${id}`);

            artist.value = response.data.artist;
        } catch (e) {
            console.log(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchLatestRelease = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                latestRelease: Release
            }>(`/api/artist/${artist.value.id}/releases/latest`);

            artistLatestRelease.value = response.data.latestRelease
        } catch (e) {
            console.log(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchTopSongs = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                topTracks: Track[]
            }>(`/api/artist/${artist.value.id}/tracks/top`)

            artistTopSongs.value = response.data.topTracks
        } catch (e) {
            console.log(e);
        } finally {
            isLoading.value = false;
        }
    }

    return {
        isLoading, fetchArtist, artist, artistLatestRelease, fetchLatestRelease, fetchTopSongs,
        artistTopSongs,
    }
})
