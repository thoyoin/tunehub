import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import api from '@/lib/api.js'
import { useDebounceFn } from '@vueuse/core'

export const useUserSearch = defineStore('userSearchStore', () => {
    const search = ref(null)
    const result = ref([])
    const hasResult = ref(true)
    const isLoading = ref(false)

    const fetchSearch = async () => {
        if (!search.value) {
            hasResult.value = true
            isLoading.value = false

            return result.value = []
        }

        try {
            const response = await api.get('/api/search/users', {
                params: { query: search.value}
            })

            result.value = response.data.users

            hasResult.value = result.value.length !== 0
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false
        }

    }

    const debouncedSearch = useDebounceFn(fetchSearch, 400)

    // watch(search, async () => {
    //     isLoading.value = true
    //
    //     await debouncedSearch()
    // })

    return { search, fetchSearch, result, isLoading, hasResult }
})
