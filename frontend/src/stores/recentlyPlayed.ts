import { defineStore } from 'pinia';
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth.ts'
import api from '@/lib/api.js'
import type { Item } from '../types/Item.js'
import type { Track } from '../types/Track.js'

export const useRecentlyPlayedStore = defineStore('recentlyPlayedStore', () => {
    const items = ref<Item>(null)
    const isLoading = ref<boolean>(false)

    const auth = useAuthStore()

    const fetchRecentlyPlayed = async (): Promise<void> => {
        if (auth.user) {
            try {
                isLoading.value = true;

                const response = await api.get<Item[]>('/api/recentlyPlayed')

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
