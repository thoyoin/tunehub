<script setup lang="ts">
import { useArtistMerchStore } from "@/stores/artistMerch";

const merchStore = useArtistMerchStore();
</script>

<template>
    <div
        class="modal fade"
        id="cartModal"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" style="width: 500px">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title d-flex flex-row justify-content-start w-100">
                        <span class="fw-bold fs-5 ms-2">Your Cart</span>
                    </div>
                </div>
                <div class="modal-body">
                    <template v-if="merchStore.cart.length">
                        <div class="cart-item">
                            <div
                                style="border-bottom: 1px solid rgba(228,228,228,0.15)"
                                class="d-flex flex-row align-items-center p-2"
                            >
                                <img
                                    class="rounded-circle"
                                    style="width: 50px;height: 50px"
                                    :src="merchStore.cart[0]?.artist_cover"
                                    alt=""
                                >
                                <span class="fw-bold ms-2">
                                    {{ merchStore.cart[0]?.artist_name }}
                                </span>
                            </div>
                        <template v-for="product in merchStore.cart">
                            <div
                                style="border-bottom: 1px solid rgba(228,228,228,0.15)"
                                class="d-flex flex-row p-2"
                            >
                                <div class="me-3">
                                    <img
                                        class="rounded-4"
                                        style="width:170px;height:170px"
                                        :src="product.cover_url"
                                        alt="cover"
                                    >
                                </div>
                                <div
                                    style="font-size: 15px"
                                    class="d-flex flex-column gap-1"
                                >
                                    <span class="fw-bold">{{ product.title }}</span>
                                    <span class="opacity-50">{{ product.variant_name }}</span>
                                    <span class=" fs-4">${{ product.price }}</span>
                                    <span class="fw-bold">Quantity</span>
                                    <span class="">{{ product.quantity }}</span>
                                </div>
                            </div>
                        </template>
                        <div class="d-flex flex-column p-2 fw-bold fs-5">
                        <div class="d-flex flex-row justify-content-between">
                            <span>Subtotal</span>
                            <span>${{ merchStore.cartSubtotalPrice }}</span>
                        </div>
                        <div class="w-100 d-flex mt-3 justify-content-center">
                            <button
                                @click="merchStore.checkoutMerch()"
                                class="btn btn-artists mb-2 w-100 d-flex justify-content-center py-3"
                            >
                                Go to checkout
                            </button>
                        </div>
                    </div>
                    </div>
                    </template>
                    <template v-else>
                        <div class="w-100 d-flex justify-content-center">
                            <span class="opacity-50">Your cart is empty</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.cart-item {
    border: 1px solid rgba(228,228,228,0.15);
    border-radius: 25px;
}

</style>
