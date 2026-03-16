import { defineStore } from 'pinia'
import {ref} from "vue";
import api from "@/lib/api";
import { loadStripe } from "@stripe/stripe-js";

const stripePromise = loadStripe('pk_test_XBv0lI7M4t3I7l6rAlfKE7dn00tJ2fRSzh');

const cardStyle = {
    style: {
        base: {
            border: "1px solid rgba(228, 228, 228, 0.15)",
            borderRadius: "15px",
            color: 'rgb(228,228,228)',
            fontFamily: 'Arial, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: 'rgb(228,228,228)',
            },
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a',
        },
    },
};

export const useSubscriptionStore = defineStore('subscription', () => {
    const isLoading = ref<boolean>(false);
    let cardElement: any = null;

    const subscribeUser = async () => {
        try {
            isLoading.value = true;

            const stripe = await stripePromise

            const elements = stripe?.elements();

            if (!cardElement && elements) {
                cardElement = elements.create('card', cardStyle);
                setTimeout(() => {
                    cardElement.mount('#card-element');
                });
                return;
            }

            const { paymentMethod, error } = await stripe?.createPaymentMethod({
                type: 'card',
                card: cardElement,
            })

            if (error) {
                console.error(error)
                isLoading.value = false
                return
            }

            const response = await api.post('/api/user/subscribe', {
                payment_method: paymentMethod.id,
                plan: 'premium'
            })

            console.log(response.data)
        } catch (e) {
            console.error(e)
        } finally {
            isLoading.value = false;
        }
    }

    return { isLoading, subscribeUser };
})
