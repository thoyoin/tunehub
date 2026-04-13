<script setup lang="ts">
import { useArtistMerchStore } from "@/stores/artistMerch";

const merchStore = useArtistMerchStore();

function incrementQuantity(index: number): void {
    const item = merchStore.cart[index];

    if (!item) {
        return;
    }

    if (item.quantity >= item.stock) {
        return;
    }

    item.quantity += 1;
}

function decrementQuantity(index: number): void {
    const item = merchStore.cart[index];

    if (!item) {
        return;
    }

    if (item.quantity <= 1) {
        return;
    }

    item.quantity -= 1;
}

function removeFromCart(index: number): void {
    merchStore.cart.splice(index, 1);
}
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
                            <template
                                v-for="(product, index) in merchStore.cart"
                                :key="`${product.product_id}-${product.variant_id}`"
                            >
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
                                    class="d-flex flex-column gap-2 flex-grow-1"
                                >
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div class="d-flex flex-column gap-1">
                                            <span class="fw-bold">{{ product.title }}</span>
                                            <span class="opacity-50">{{ product.variant_name }}</span>
                                        </div>
                                        <button
                                            @click="removeFromCart(index)"
                                            type="button"
                                            class="btn btn-add"
                                        >
                                            <img src="@/assets/svg/delete.svg" alt="delete">
                                        </button>
                                    </div>
                                    <span class="fs-4">${{ product.price }}</span>
                                    <div class="d-flex flex-column gap-1 mt-1">
                                        <span class="fw-bold">Quantity</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <button
                                                @click="decrementQuantity(index)"
                                                type="button"
                                                class="btn quantity-btn"
                                                :disabled="product.quantity <= 1"
                                            >
                                                -
                                            </button>
                                            <span class="quantity-value">{{ product.quantity }}</span>
                                            <button
                                                @click="incrementQuantity(index)"
                                                type="button"
                                                class="btn quantity-btn"
                                                :disabled="product.quantity >= product.stock"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </div>
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
.quantity-btn {
    width: 25px;
    height: 25px;
    padding: 0;
    border-radius: 50%;
    border: 1px solid rgba(228,228,228,0.15);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: white;
}

.quantity-btn:disabled {
    opacity: 0.2;
    cursor: not-allowed;
}

.quantity-value {
    min-width: 24px;
    text-align: center;
    font-weight: 700;
}
</style>
