import { defineStore } from 'pinia';
import { ref } from 'vue'

import api from '@/lib/api'

import type { Item } from '@/types/Item'

export const useRecentlyPlayedStore = defineStore('recentlyPlayedStore', () => {
    const items = ref<Item[] | null>(null)
    const isLoading = ref<boolean>(false)
    const isDataLoaded = ref<boolean>(false)

    const fetchRecentlyPlayed = async (): Promise<void> => {
        if (!isDataLoaded.value) {
            try {
                isLoading.value = true;

                const response = await api.get<{
                    playedHistory: Item[]
                }>('/api/recentlyPlayed')

                items.value = response.data.playedHistory
            } catch (e) {
                console.error(e)
            } finally {
                isLoading.value = false
                isDataLoaded.value = true
            }
        }
    }

    return {
        isLoading,
        fetchRecentlyPlayed,
        items,
    }
})
