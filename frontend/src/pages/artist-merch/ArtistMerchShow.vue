<script setup lang="ts">
import { useArtistMerchStore } from "@/stores/artistMerch";
import { useRoute, useRouter } from "vue-router";
import { watch} from "vue";
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const merchStore = useArtistMerchStore();
const route = useRoute();
const router = useRouter();

watch(
    [() => route.params.slug, () => route.params.artistId],
    async ([slug, artistId]) => {
        if (slug && typeof slug === "string") {
            await merchStore.fetchMerch(slug);
        }
        if (!merchStore.artist && typeof artistId === "string") {
            await merchStore.fetchArtist(artistId)
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
        <div class="d-flex flex-row browse-btn align-items-center">
            <img style="width: 20px" src="@/assets/svg/arrowLeft.svg" alt="">
            <span
                @click="router.push({
                    name: 'artist.merch.all',
                    params: { artistId: merchStore.artist?.id},
                })"
                class="fw-bold ms-2"
            >
                Browse all
            </span>
        </div>
        <div class="d-flex flex-row align-items-start justify-content-start w-100">
            <div>
                <Swiper
                    v-if="merchStore.artistMerch?.product_images?.length"
                    class="merch-swiper"
                    :space-between="10"
                    :slides-per-view="1"
                    :modules="[Navigation, Pagination]"
                    navigation
                    :pagination="{ clickable: true }"
                >
                    <SwiperSlide
                        v-for="image in merchStore.artistMerch?.product_images"
                        :key="image.id"
                        class="merch-swiper__slide"
                    >
                        <img
                            :src="image.image_url"
                            class="merch-swiper__image"
                            alt="image"
                        />
                    </SwiperSlide>
                </Swiper>
            </div>
            <div class="d-flex flex-column" style="margin-left: 100px;margin-top: 30px">
                <span class="fw-bold fs-5">{{ merchStore.artistMerch?.title }}</span>
                <div class="d-flex flex-row mt-3 align-items-center">
                    <img
                        class="rounded-circle"
                        style="width: 50px;height: 50px"
                        :src="merchStore.artist?.profile_picture"
                        alt="cover"
                    >
                    <span
                        @click="router.push({
                            name: 'artist',
                            params: {
                                artistId: route.params.artistId,
                            }
                        })"
                        class="fw-normal ms-2 browse-btn"
                    >
                        {{ merchStore.artist?.username }}
                    </span>
                </div>
                <span class="mt-4 opacity-50">{{ merchStore.artistMerch?.description }}</span>
                <div class="d-flex flex-row align-items-center mt-4">
                    <span class="opacity-50 fs-4">
                        {{ merchStore.artistMerch?.currency.toUpperCase() }}
                    </span>
                    <span class="ms-4 fs-4">
                        ${{ merchStore.artistMerch?.product_variants[0]?.price }}
                    </span>
                </div>
                <div class="d-flex flex-column mt-4">
                    <div class="mb-2">
                        <span class="badge opacity-50 mb-1">Size</span>
                        <select class="form-select" aria-label="Default select example">
                            <template v-for="(product, index) in merchStore.artistMerch?.product_variants">
                                <option :value="index + 1">{{ product.variant_name }}</option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <span class="badge opacity-50 mb-1">Quantity</span>
                        <select class="form-select" aria-label="Default select example">
                            <option value="1">1</option>
                            <option value="1">2</option>
                            <option value="1">3</option>
                            <option value="1">4</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex flex-row align-items-center gap-5">
                    <button class="btn btn-primary fs-5 fw-light">
                        Add to card
                    </button>
                    <button class="btn btn-primary fs-5 fw-light">
                        But it now
                    </button>
                </div>
            </div>
        </div>
        <div class="d-flex mt-5 flex-column">
            <span class="fw-bold fs-5">More items from {{ merchStore.artist?.username }}</span>
            <div class="d-flex flex-row mt-4 gap-4">
                <template v-for="product in merchStore.artist?.products">
                    <template v-if="product.slug !== route.params.slug">
                        <div
                            @click="router.push({
                                name: 'artist.merch.show',
                                params: {
                                    slug: product.slug
                                }
                            })"
                            class="merch-card"
                        >
                            <img
                                class="rounded-3 mb-2"
                                style="width: 180px;height: 180px"
                                :src="product.cover_url"
                                alt="cover"
                            >
                            <div class="fw-bold">{{ product.title }}</div>
                            <span class="opacity-50">${{ product.product_variants[0]?.price }}</span>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.browse-btn {
    &:hover {
        text-decoration: underline;
    }
}

.swiper {
    width: 100%;
}

.merch-swiper {
    width: 100%;
    max-width: 500px;
    min-height: 500px;
    margin: 40px auto 0;
    border-radius: 28px;
    overflow: hidden;
}

.merch-swiper__slide {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 500px;
}

.merch-swiper__image {
    display: block;
    max-width: 100%;
    max-height: 620px;
    object-fit: contain;
    user-select: none;
    border-radius: 15px;
}

:deep(.swiper-button-prev),
:deep(.swiper-button-next) {
    width: 15px;
    top: 100%;
    transform: translateY(-50%);
    color: #f3f3f3;
    transition: background-color 0.2s ease, opacity 0.2s ease;
    z-index: 100;
}

:deep(.swiper-button-prev::after),
:deep(.swiper-button-next::after) {
    font-size: 28px;
    font-weight: 500;
}

:deep(.swiper-pagination) {
    bottom: 0 !important;
}

:deep(.swiper-pagination-bullet) {
    width: 5px;
    height: 5px;
    background: rgba(255, 255, 255, 0.2);
    opacity: 1;
}

:deep(.swiper-pagination-bullet-active) {
    background: rgba(255, 255, 255, 0.8);
}

.form-select {
    transition: .2s !important;
    background-color: rgb(32,32,32);
    border: 1px solid rgba(228,228,228, 0.15);
    color: #fff;

    &:focus {
        box-shadow: none !important;
        border-color: rgb(158, 23, 63) !important;
    }
}

.merch-card {
    display: flex;
    align-items: start;
    flex-direction: column;
    transition: .2s;

    &:hover {
        div {
            transition: .1s;
            color: rgb(158, 23, 63);
        }
    }
}
</style>
