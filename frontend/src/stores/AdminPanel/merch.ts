import { defineStore } from 'pinia';
import { computed, ref } from "vue";
import { useToast } from "vue-toastification";

import api from "@/lib/api";

import type { ArtistMerch } from "@/types/ArtistMerch";
import type { PaginatedResponse } from "@/types/PaginatedResponse";

export const useMerchStore = defineStore('merch', () => {
    const isLoading = ref(false);
    const merch = ref<PaginatedResponse<ArtistMerch> | null>(null)
    const selectedView = ref<
        'all' | 'moderating' | 'rejected'
    >('all')
    const viewingMerch = ref<ArtistMerch | null>(null)

    const toast = useToast()

    const fetchMerchData = async (status?: string, page: number = 1) => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                merch: PaginatedResponse<ArtistMerch>
            }>('/api/admin/merch', {
                params: { status: status, page: page }
            })

            merch.value = response.data.merch
        } catch (e) {
            console.log(e)
        } finally {
            isLoading.value = false;
        }
    }

    const updateMerchStatus = async (status: string) => {
        try {
            isLoading.value = true;

            await api.patch<{ message: string }>(
                `/api/admin/merch/${viewingMerch.value?.id}/status/update`,
                { 'status': status })

            await fetchMerchData()

            toast.success('Merch status was updated.')
        } catch (e) {
            console.log(e)

            toast.error('Something went wrong.')
        } finally {
            isLoading.value = false;
        }
    }

    const selectView = (view: 'all' | 'moderating' | 'rejected') => {
        selectedView.value = view
    }

    const setViewingMerch = (merch: ArtistMerch) => {
        viewingMerch.value = merch
    }

    const moderatingMerch = computed(() =>
        (merch.value?.data ?? []).filter(item => item.status === 'moderating')
    )

    const rejectedMerch = computed(() =>
        (merch.value?.data ?? []).filter(item => item.status === 'rejected')
    )

    return {
        isLoading, fetchMerchData, merch, moderatingMerch, rejectedMerch, selectedView, selectView,
        setViewingMerch, viewingMerch, updateMerchStatus
    }
})
