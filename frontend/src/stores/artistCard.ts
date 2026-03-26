import { defineStore } from 'pinia';
import { ref } from "vue";
import type { User } from "@/types/User";
import api from "@/lib/api";
import type { Release } from "@/types/Release";
import type { Track } from "@/types/Track";

export const useArtistCardStore = defineStore('artistCard', () => {
    const isLoading = ref<boolean>(false);
    const isReleaseLoading = ref<boolean>(false);
    const areReleasesLoading = ref<boolean>(false);
    const areSongsLoading = ref<boolean>(false);
    const artist = ref<User | null>(null);
    const artistLatestRelease = ref<Release | null>(null);
    const artistTopSongs = ref<Track[] | null>(null);
    const artistAlbums = ref<Release[] | null>(null);

    const fetchArtist = async (id: string | number) => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                artist: User
            }>(`/api/artist/${id}`);

            artist.value = response.data.artist;
            console.log(response.data.artist);
        } catch (e) {
            console.log(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchLatestRelease = async () => {
        try {
            isReleaseLoading.value = true;

            const response = await api.get<{
                latestRelease: Release
            }>(`/api/artist/${artist.value?.id}/releases/latest`);

            artistLatestRelease.value = response.data.latestRelease
        } catch (e) {
            console.log(e);
        } finally {
            isReleaseLoading.value = false;
        }
    }

    const fetchTopSongs = async () => {
        try {
            areSongsLoading.value = true;

            const response = await api.get<{
                topTracks: Track[]
            }>(`/api/artist/${artist.value?.id}/tracks/top`)

            artistTopSongs.value = response.data.topTracks
        } catch (e) {
            console.log(e);
        } finally {
            areSongsLoading.value = false;
        }
    }

    const fetchAlbums = async () => {
        try {
            areReleasesLoading.value = true;

            const response = await api.get<{
                albums: Release[]
            }>(`/api/artist/${artist.value?.id}/albums`)

            artistAlbums.value = response.data.albums
        } catch (e) {
            console.log(e);
        } finally {
            isReleaseLoading.value = false;
        }
    }

    return {
        isLoading, fetchArtist, artist, artistLatestRelease, fetchLatestRelease, fetchTopSongs,
        artistTopSongs, areSongsLoading, isReleaseLoading, fetchAlbums, artistAlbums,
    }
})
