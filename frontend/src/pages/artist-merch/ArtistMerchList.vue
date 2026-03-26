<script setup lang="ts">
import { useArtistMerchStore } from "@/stores/artistMerch";
import { useRoute, useRouter } from "vue-router";
import { watch } from "vue";

const merchStore = useArtistMerchStore();
const route = useRoute();
const router = useRouter();

watch(
    () => route.params.artistId,
    async (id) => {
        if (id) {
            await merchStore.fetchArtist(id);
        }
    },
    { immediate: true },
);

</script>

<template>
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
                style="width: 90px; height: 90px"
                :src="merchStore.artist?.profile_picture"
                alt="cover"
            />
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
                        @click="router.push({
                            name: 'artist.merch.show',
                            params: {
                                slug: product.slug
                            }
                        })"
                        class="product-card w-100"
                    >
                        <img
                            class="rounded-3 mb-3"
                            style="width: 220px; height: 220px"
                            :src="product.cover_url"
                            alt="cover"
                        />
                        <div class="d-flex flex-column">
                            <span class="fw-bold">{{ product.title }}</span>
                            <span class="mt-1 fw-light"
                                >${{ product.product_variants[0]?.price }}</span
                            >
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.product-card {
    max-width: 250px;
    padding: 15px;
    height: 320px;
}
</style>
