import { defineStore } from 'pinia'
import { ref } from "vue";

import api from "@/lib/api";

import type { Subscription } from "@/types/Subscription";

export const useSubscriptionStore = defineStore('subscription', () => {
    const isLoading = ref<boolean>(false);
    const subscriptionDetails = ref<Subscription | null>(null);
    const isDetailsDataLoaded = ref<boolean>(false);

    const goToCheckout = async () => {
        isLoading.value = true;

        try {
            const response = await api.post('/api/subscription/checkout', {
                price_id: 'price_1TBa6jLLiCzKtruSYomynytQ',
            })

            window.location.href = response.data.url
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false;
        }
    }

    const getSubscriptionDetails = async (force: boolean = false) => {
        if (isDetailsDataLoaded.value && !force) return

        try {
            isLoading.value = true;

            const response = await api.get<{
                details: Subscription
            }>('/api/subscription/details')

            subscriptionDetails.value = response.data.details
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false;
            isDetailsDataLoaded.value = true;
        }
    }

    const goToBillingPortal = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                url: string;
            }>("/api/user/billing-portal");

            window.location.href = response.data.url
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false;
        }
    }

    return { isLoading, goToCheckout, getSubscriptionDetails, subscriptionDetails, goToBillingPortal };
})
