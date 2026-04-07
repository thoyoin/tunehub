import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'

import api from '@/lib/api'

import type { Release } from '@/types/Release'
import type { Playlist } from '@/types/Playlist'
import type { Track } from '@/types/Track'

type SearchResult = {
    releases?: Release[],
    playlists?: Playlist[],
    tracks?: Track[],
};

export const useSearchStore = defineStore('searchStore', () => {
    const search = ref<string | null>(null)
    const result = ref<SearchResult | null>(null)
    const hasResult = ref<boolean>(true)
    const isLoading = ref<boolean>(false)

    const fetchSearch = async () => {
        if (!search.value) {
            hasResult.value = true
            isLoading.value = false
            return result.value = null
        }

        try {
            const response = await api.get<SearchResult | null>('/api/search', {
                params: { query: search.value}
            })

            result.value = response.data

            hasResult.value = result.value?.releases?.length !== 0
                || result.value.playlists?.length !== 0
                || result.value.tracks?.length !== 0;
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false
        }

    }

    const debouncedSearch = useDebounceFn(fetchSearch, 400)

    watch(search, async (): Promise<void> => {
        isLoading.value = true
        await debouncedSearch()
    })

    return { search, fetchSearch, result, hasResult, isLoading }
})
