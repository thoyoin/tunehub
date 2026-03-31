import { defineStore } from 'pinia'
import { computed, ref} from "vue";
import type {User} from "@/types/User";
import api from "@/lib/api";
import type { ArtistMerch } from "@/types/ArtistMerch";

interface ProductVariant {
    id: number,
    created_at: string,
    price: number,
    product_id: number,
    stock: number,
    updated_at: string,
    variant_name: string,
}

interface CartItem {
    product_id: number
    variant_id: number
    quantity: number
    title: string
    cover_url: string
    artist_name: string
    artist_cover: string
    variant_name: string
    price: number
    currency: string
    stock: number
}

interface AddToCartPayload {
    product: ArtistMerch;
    variant: ProductVariant;
    quantity: number;
}

export const useArtistMerchStore = defineStore('artistMerch', () => {
    const isLoading = ref<boolean>(false);
    const artist = ref<User | null>(null);
    const artistMerch = ref<ArtistMerch | null>(null);
    const cart = ref<CartItem[]>([])
    const cartSubtotalPrice = computed(() =>
        cart.value.reduce(
            (sum, item) => sum + item.price * item.quantity,
            0
        )
    );

    const fetchArtist = async (id: string) => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                artist: User
            }>(`/api/artist/${id}`);

            artist.value = response.data.artist;
            console.log(response.data.artist);
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchMerch = async (slug: string) => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                merch: ArtistMerch
            }>(`/api/artist/merch/${slug}/get`)

            artistMerch.value = response.data.merch;
            console.log(response.data.merch);
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const addToCart = (payload: AddToCartPayload) => {
        const existingItem = cart.value.find(
            item =>
                item.product_id === payload.product.id &&
                item.variant_id === payload.variant.id
        )

        const stock = payload.variant.stock
        const quantityToAdd = payload.quantity

        if (existingItem) {
            existingItem.quantity = Number(existingItem.quantity)
                + Number(payload.quantity);

            return
        }

        cart.value.push({
            product_id: payload.product.id,
            variant_id: payload.variant.id,
            quantity: payload.quantity,
            title: payload.product.title,
            cover_url: payload.product.cover_url,
            artist_name: payload.product.user?.username ?? '',
            artist_cover: payload.product.user?.profile_picture ?? '',
            variant_name: payload.variant.variant_name,
            price: payload.variant.price,
            currency: payload.product.currency,
            stock: payload.variant.stock,
        })
    }

    const checkoutMerch = async () => {
        try {
            isLoading.value = true;

            const response = await api.post('/api/artist/merch/checkout', {
                cart: cart.value.map((item) => ({
                    product_id: item.product_id,
                    variant_id: item.variant_id,
                    quantity: item.quantity,
                })),
            })

            window.location.href = response.data.url
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    return {
        isLoading, fetchArtist, artist, fetchMerch, artistMerch, addToCart, cart, cartSubtotalPrice,
        checkoutMerch,
    }
})
