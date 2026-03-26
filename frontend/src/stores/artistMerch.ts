import { defineStore } from 'pinia'
import {ref} from "vue";
import type {User} from "@/types/User";
import api from "@/lib/api";
import type { ArtistMerch } from "@/types/ArtistMerch";

export const useArtistMerchStore = defineStore('artistMerch', () => {
    const isLoading = ref<boolean>(false);
    const artist = ref<User | null>(null);
    const artistMerch = ref<ArtistMerch | null>(null);

    const fetchArtist = async (id: string) => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                artist: User
            }>(`/api/artist/${id}`);

            artist.value = response.data.artist;
            console.log(response.data.artist);
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchMerch = async (slug: string) => {
        try {
            isLoading.value = true;

            console.log(slug)
            const response = await api.get<{
                merch: ArtistMerch
            }>(`/api/artist/merch/${slug}/get`)

            artistMerch.value = response.data.merch;
            console.log(response.data.merch);
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    return {
        isLoading, fetchArtist, artist, fetchMerch, artistMerch
    }
})
