import { defineStore } from "pinia";
import { ref } from "vue";
import api from "@/lib/api";
import type {User} from "@/types/User";

interface PlaysPerDay {
    date: string;
    plays: string;
}

interface UserGrowth {
    date: string;
    users: string;
    joined_at: string;
}

export const useOverviewStore = defineStore("overview", () => {
    const isLoading = ref(false);
    const totalPlays = ref<number | null>(null);
    const totalPlaysGrowth = ref<number | null>(null);
    const newUsers = ref<number | null>(null);
    const newUsersGrowth = ref<number | null>(null);
    const newTracks = ref<number | null>(null);
    const newTracksGrowth = ref<number | null>(null);
    const newReleases = ref<number | null>(null);
    const newReleasesGrowth = ref<number | null>(null);
    const newPlaylists = ref<number | null>(null);
    const newPlaylistsGrowth = ref<number | null>(null);
    const topArtists = ref<any>(null);

    const playsPerMonth = ref<PlaysPerDay[] | null>(null);
    const userGrowth = ref<UserGrowth[] | null>(null);

    const fetchTotalPlays = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                totalPlays: number;
                growth: number;
            }>('/api/admin/totalPlays')

            totalPlays.value = response.data.totalPlays;
            totalPlaysGrowth.value = response.data.growth;
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    };

    const fetchNewUsers = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                newUsers: number;
                growth: number;
            }>('/api/admin/newUsers')

            newUsers.value = response.data.newUsers
            newUsersGrowth.value = response.data.growth
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    };

    const fetchNewTracks = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                newTracks: number;
                growth: number;
            }>('/api/admin/newTracks')

            newTracks.value = response.data.newTracks
            newTracksGrowth.value = response.data.growth
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchNewReleases = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                newReleases: number;
                growth: number;
            }>('/api/admin/newReleases')

            newReleases.value = response.data.newReleases
            newReleasesGrowth.value = response.data.growth
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchNewPlaylists = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                newPlaylists: number;
                growth: number;
            }>('/api/admin/newPlaylists')

            newPlaylists.value = response.data.newPlaylists
            newPlaylistsGrowth.value = response.data.growth
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchPlaysPerMonth = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                monthPlays: PlaysPerDay[]
            }>('/api/admin/plays/month')

            playsPerMonth.value = response.data.monthPlays
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchUserGrowth = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                userGrowth: UserGrowth[];
            }>('/api/admin/userGrowth')

            userGrowth.value = response.data.userGrowth
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchTopArtists = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                topArtists: [
                    artist: User,
                    streams: string
                ]
            }>("/api/admin/topArtists");

            topArtists.value = response.data.topArtists
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchAllAnalytics = async () => {
        await fetchTotalPlays();
        await fetchNewTracks();
        await fetchNewReleases();
        await fetchNewUsers();
        await fetchNewPlaylists();
        await fetchPlaysPerMonth();
        await fetchUserGrowth();
        await fetchTopArtists();
    }

    return {
        isLoading,
        fetchTotalPlays,
        fetchNewUsers,
        fetchNewTracks,
        fetchNewReleases,
        fetchAllAnalytics,
        newPlaylists,
        newPlaylistsGrowth,
        newReleases,
        newReleasesGrowth,
        newTracks,
        newTracksGrowth,
        newUsers,
        newUsersGrowth,
        totalPlays,
        totalPlaysGrowth,
        playsPerMonth,
        userGrowth,
        topArtists
    }
})
