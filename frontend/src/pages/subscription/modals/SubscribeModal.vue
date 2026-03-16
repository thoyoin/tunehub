<script setup lang="ts">
import { useSubscriptionStore } from "@/stores/subscription";
import { onMounted } from "vue";
import { useToast } from "vue-toastification";

const subscriptionStore = useSubscriptionStore();
const toast = useToast();

onMounted(() => {
    subscriptionStore.subscribeUser();
});

const handleSubscribe = async () => {
    try {
        await subscriptionStore.subscribeUser();

        toast.success("Successfully subscribed!");
    } catch (error) {
        console.error(error);

        toast.error("Something went wrong.");
    }
}

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
                    <div id="card-element" style="min-height: 200px; min-width: 300px">

                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        @click="handleSubscribe"
                        class="btn btn-primary"

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
