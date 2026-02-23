import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import api from '@/lib/api.js'
import { useDebounceFn } from '@vueuse/core'

export const useSearchStore = defineStore('searchStore', () => {
    const search = ref(null)
    const result = ref([])
    const hasResult = ref(true)
    const isLoading = ref(false)

    const fetchSearch = async () => {
        if (!search.value) {
            hasResult.value = true
            return result.value = []
        }

        try {

            const response = await api.get('/api/search', {
                params: { query: search.value}
            })

            result.value = response.data

            hasResult.value = result.value.releases?.length !== 0
                || result.value.playlists?.length !== 0
                || result.value.tracks?.length !== 0;
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false
        }

    }

        const debouncedSearch = useDebounceFn(fetchSearch, 400)

        watch(search, async () => {
            isLoading.value = true

            await debouncedSearch()
        })

    return { search, fetchSearch, result, hasResult, isLoading }
})
