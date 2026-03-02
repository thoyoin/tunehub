import { defineStore } from "pinia";
import { ref } from "vue";
import type { PaginatedResponse } from "@/types/PaginatedResponse";
import type { Release } from "@/types/Release";
import api from "@/lib/api";

export const useModerationStore = defineStore("moderation", () => {
    const releases = ref<PaginatedResponse<Release[]> | null>(null)
    const isLoading = ref<boolean>(false);
    const viewRelease = ref<Release | null>(null)
    const selectedView = ref<string>('pending')
    const pendingReleasesNumber = ref<number | null>(null)
    const releasesNumber = ref<number | null>(null)

    const fetchByStatus = async (status: string = 'pending', page = 1) => {
        try {
            isLoading.value = true;

            const response = await api.get<PaginatedResponse<Release[]>>(
                `/api/admin/releases`, {
                    params: { status: status, page: page }
                },
            )

            releases.value = response.data

            if (status === 'pending') {
                pendingReleasesNumber.value = response.data.data.length
            } else {
                releasesNumber.value = response.data.data.length
            }

        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false;
        }
    }

    const updateReleaseStatus = async (status: string, releaseId: number) => {
        try {
            isLoading.value = true;

            await api.patch(`/api/admin/releases/${releaseId}/status`, {
                'status': status,
            })

            await fetchByStatus()
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false;
        }
    }

    const setViewRelease = (release: Release): void => {
        viewRelease.value = release
    }

    const selectView = (view: string): void => {
        selectedView.value = view
    }

    return {
        releases, isLoading, setViewRelease, viewRelease, selectView,
        selectedView, fetchByStatus, updateReleaseStatus, pendingReleasesNumber, releasesNumber
    }
})
