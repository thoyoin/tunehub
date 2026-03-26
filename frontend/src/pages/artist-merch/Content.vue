<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
import { useArtistMerchStore } from "@/stores/artistMerch";
import { onMounted, watch } from "vue";
import { useRoute } from "vue-router";

const merchStore = useArtistMerchStore();
const auth = useAuthStore();
const route = useRoute();

onMounted(() => {
    if (!auth.isReady) {
        auth.fetchUser()
    }
})

</script>

<template>
    <div style="color: rgb(228, 228, 228)" class="flex-grow-1 content position-relative">
        <transition name="fade">
            <div
                v-if="merchStore.isLoading"
                class="loading-overlay d-flex flex-column align-items-center justify-content-center"
            >
                <div class="search-spinner mb-2"></div>
            </div>
        </transition>
        <div class="d-flex flex-column" style="padding: 150px 200px 50px">
            <div class="d-flex flex-row align-items-center">
                <img
                    class="rounded-circle"
                    style="width: 90px;height:90px"
                    :src="merchStore.artist?.profile_picture"
                    alt="cover"
                >
                <div class="d-flex flex-column ms-3">
                    <span class="fs-5 fw-light opacity-50">Merch by</span>
                    <span style="line-height: 25px" class="fs-2 fw-bold">
                        {{ merchStore.artist?.username }}
                    </span>
                </div>
            </div>
            <div style="margin-top: 100px">
                <div class="d-flex flex-row flex-wrap w-100 gap-4">
                    <template v-for="product in merchStore.artist?.products">
                        <div
                            @click=""
                            class="product-card w-100"
                        >
                            <img
                                class="rounded-3 mb-3"
                                style="width:220px;height:220px"
                                :src="product.cover_url"
                                alt="cover"
                            >
                            <div class="d-flex flex-column">
                                <span class="fw-bold">{{ product.title }}</span>
                                <span class="mt-1 fw-light">${{ product.product_variants[0]?.price }}</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.content {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 0 0 90px 0 !important;
    min-height: 0 !important;
}
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(32, 32, 32, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1000;
    pointer-events: auto;
    user-select: none;
}
.search-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(228, 228, 228, 0.2);
    border-top: 2px solid rgb(158, 23, 63);
    border-radius: 50%;
    animation: spin 0.4s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.product-card {
    max-width: 250px;
    padding: 15px;
    height: 320px;
}
</style>
