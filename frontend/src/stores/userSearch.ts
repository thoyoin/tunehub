import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import api from '@/lib/api.ts'
import { useDebounceFn } from '@vueuse/core'
import type { User } from '../types/User.js'
import type { PaginatedResponse } from '../types/PaginatedResponse.js'

export const useUserSearch = defineStore('userSearchStore', () => {
    const search = ref<string | null>(null)
    const result = ref<User[]>([])
    const hasResult = ref<boolean>(true)
    const isLoading = ref<boolean>(false)

    const fetchSearch = async (): Promise<void> => {
        if (!search.value) {
            hasResult.value = true
            isLoading.value = false

            return result.value = []
        }

        try {
            const response = await api.get<PaginatedResponse<User[]>>('/api/search/users', {
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
