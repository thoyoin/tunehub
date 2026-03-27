import { defineStore } from 'pinia';
import {reactive, ref } from "vue";
import type { User } from "@/types/User";
import api from "@/lib/api";
import type { Release } from "@/types/Release";
import type { Track } from "@/types/Track";

export const useArtistCardStore = defineStore('artistCard', () => {
    const isLoading = reactive({
        artist: false,
        release: false,
        albums: false,
        songs: false,
    });
    const artist = ref<User | null>(null);
    const artistLatestRelease = ref<Release | null>(null);
    const artistTopSongs = ref<Track[] | null>(null);
    const artistAlbums = ref<Release[] | null>(null);

    const fetchArtist = async (id: string | number) => {
        try {
            isLoading.artist = true;

            const response = await api.get<{
                artist: User
            }>(`/api/artist/${id}`);

            artist.value = response.data.artist;
        } catch (e) {
            console.log(e);
        } finally {
            isLoading.artist = false;
        }
    }

    const fetchLatestRelease = async () => {
        try {
            isLoading.release = true;

            const response = await api.get<{
                latestRelease: Release
            }>(`/api/artist/${artist.value?.id}/releases/latest`);

            artistLatestRelease.value = response.data.latestRelease
        } catch (e) {
            console.log(e);
        } finally {
            isLoading.release = false;
        }
    }

    const fetchTopSongs = async () => {
        try {
            isLoading.songs = true;

            const response = await api.get<{
                topTracks: Track[]
            }>(`/api/artist/${artist.value?.id}/tracks/top`)

            artistTopSongs.value = response.data.topTracks
        } catch (e) {
            console.log(e);
        } finally {
            isLoading.songs = false;
        }
    }

    const fetchAlbums = async () => {
        try {
            isLoading.albums = true;

            const response = await api.get<{
                albums: Release[]
            }>(`/api/artist/${artist.value?.id}/albums`)

            artistAlbums.value = response.data.albums
        } catch (e) {
            console.log(e);
        } finally {
            isLoading.albums = false;
        }
    }

    const ensureDataIsLoaded = async (id: string) => {
        if (artist.value?.id === Number(id)) return

        await Promise.all([
            await fetchArtist(id),
            fetchLatestRelease(),
            fetchTopSongs(),
            fetchAlbums(),
        ]);
    }

    return {
        isLoading, fetchArtist, artist, artistLatestRelease, fetchLatestRelease, fetchTopSongs,
        artistTopSongs, fetchAlbums, artistAlbums, ensureDataIsLoaded
    }
})
