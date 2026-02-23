import { defineStore } from 'pinia';
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import api from '@/lib/api.js'

export const useRecentlyPlayedStore = defineStore('recentlyPlayedStore', () => {
    const items = ref(null)
    const isLoading = ref(false)

    const auth = useAuthStore()

    const fetchRecentlyPlayed = async () => {
        if (auth.user) {
            try {
                isLoading.value = true;
                const response = await api.get('/api/recentlyPlayed')
                items.value = response.data.playedHistory
            } catch (e) {
                console.error(e)
            } finally {
                isLoading.value = false
            }
        }
    }

    return {
        isLoading,
        fetchRecentlyPlayed,
        items,
    }
})
