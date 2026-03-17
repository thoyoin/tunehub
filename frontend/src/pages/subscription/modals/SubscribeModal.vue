<script setup lang="ts">
import { useSubscriptionStore } from "@/stores/subscription";
import { useToast } from "vue-toastification";

const subscriptionStore = useSubscriptionStore();
const toast = useToast();

</script>

<template>
    <div
        class="modal fade"
        id="subscribeModal"
        tabindex="-1"
        aria-labelledby="subscribeModalLabel"
        aria-hidden="true"
    >
        <div
            class="modal-dialog modal-dialog-scrollable modal-fullscreen p-5"
        >
            <div
                class="modal-content rounded-4"
            >
                <div class="modal-header">
                    <span class="modal-title fw-bold fs-1">Get started with premium</span>
                </div>
                <div class="modal-body d-flex justify-content-center">
                    <div
                        class="rounded-5 px-3"
                        style="border:1px solid rgba(228, 228, 228, 0.15);max-height:60px"
                    >
                        <div
                            class="mt-3"
                            id="card-element"
                            style="min-height: 70px; min-width: 300px"
                        ></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        @click="subscriptionStore.goToCheckout()"
                        class="btn btn-primary"
                        :disabled="subscriptionStore.isLoading"
                    >
                        Subscribe
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
