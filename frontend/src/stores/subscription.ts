import { defineStore } from 'pinia'
import {ref} from "vue";
import api from "@/lib/api";

export const useSubscriptionStore = defineStore('subscription', () => {
    const isLoading = ref<boolean>(false);

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

    return { isLoading, goToCheckout };
})
