import { defineStore } from "pinia";
import { ref } from "vue";
import api from "@/lib/api";

export const useOverviewStore = defineStore("overview", () => {
    const totalPlays = ref<number | null>(null);
    const isLoading = ref(false);
    const newUsers = ref<number | null>(null);

    const fetchTotalPlays = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{ totalPlays: number }>('/api/admin/totalPlays')

            totalPlays.value = response.data.totalPlays;
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
                newUsers: number
            }>('/api/admin/newUsers')

            newUsers.value = response.data.newUsers
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    };

    return {
        totalPlays,
        isLoading,
        fetchTotalPlays,
        fetchNewUsers,
        newUsers,
    }
})
