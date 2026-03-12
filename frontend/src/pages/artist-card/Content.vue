<script setup lang="ts">
import { useArtistCardStore } from "@/stores/artistCard";
import { useVibrantPalette } from "@/composables/useVibrantPalette";
import { useRouter } from "vue-router";
import { watch } from "vue";

const artistCardStore = useArtistCardStore();
const router = useRouter();
const { palette, getCoverPalette } = useVibrantPalette()

watch(
    () => artistCardStore.artist?.profile_picture,
    async (url) => {
        if (url) {
            await getCoverPalette(url)
        }
    },
    { immediate: true },
)

const handleGetRelease = async () => {
    await router.push({
        name: "release",
        params: { 'releaseId': artistCardStore.artistLatestRelease.id }
    })
}

</script>

<template>
    <div style="color: rgb(228, 228, 228)" class="flex-grow-1 playlist-content position-relative">
        <transition name="fade">
            <div
                v-if="artistCardStore.isLoading"
                class="loading-overlay d-flex align-items-center justify-content-center"
            >
                <span class="fw-bold fs-5 opacity-50"> Loading... </span>
            </div>
        </transition>
        <div :style="{
                background: `
                       linear-gradient(
                         180deg,
                         rgba(${palette?.LightVibrant?.rgb}, 0.3),
                         rgb(32,32,32)
                       )
                     `,
                backdropFilter: 'blur(40px)',
            }"
             style="height: 360px;"
        >
            <div style="padding:0 250px">
                <div
                    style="margin-top: 150px;padding-bottom: 20px;
                    border-bottom:1px solid rgba(228, 228, 228, 0.15);"
                    class="d-flex flex-row align-items-center"
                >
                    <img
                        style="width: 180px;height: 180px;border-bottom:1px solid rgba(228, 228, 228, 0.15);"
                        class="rounded-circle"
                        :src="artistCardStore.artist?.profile_picture"
                        alt="cover"
                    >
                    <div class="ms-4">
                        <span class="fw-bold fs-1">
                            {{artistCardStore.artist?.username}}
                        </span>
                    </div>
                </div>
                <div class="mt-4 row">
                    <div class="col-6">
                        <span class="fw-bold fs-5">Latest Release</span>
                        <div
                            style="cursor: pointer; max-width: 350px"
                            @click="handleGetRelease()"
                            class="mt-4 d-flex flex-row align-items-center"
                        >
                            <img
                                class="rounded-3"
                                style="width: 180px;height: 180px;
                                border:1px solid rgba(228, 228, 228, 0.10);"
                                :src="artistCardStore.artistLatestRelease?.cover_url"
                                alt="cover"
                            >
                            <div class="ms-3 d-flex flex-column">
                                <span class="opacity-50">
                                    {{ artistCardStore.artistLatestRelease?.release_date }}
                                </span>
                                <span class="fs-5">
                                    {{ artistCardStore.artistLatestRelease?.title }}
                                </span>
                                <span class="opacity-50">
                                    {{ artistCardStore.artistLatestRelease?.tracks.length }} songs
                                </span>
                            </div>
                        </div>
                    </div>
                    <div style="height: 225px" class="col-6 d-flex flex-column">
                        <span class="fw-bold fs-5">Top Songs</span>
                        <div class="overflow-y-auto flex-1">
                            <template v-for="track in artistCardStore.artistTopSongs">
                                <div
                                    style="border-top:1px solid rgba(228, 228, 228, 0.15)"
                                    class="my-2 pt-2 w-100 d-flex flex-row align-items-center"
                                >
                                    <img
                                        class="rounded-1"
                                        style="width: 45px;height: 45px;"
                                        :src="track.cover_url"
                                        alt="cover"
                                    >
                                    <div class="d-flex flex-column ms-2 w-100">
                                        <span style="line-height: 17px">
                                            {{ track.title }}
                                        </span>
                                        <div class="d-flex flex-row">
                                            <div>
                                                <span class="opacity-50" style="line-height: 17px">
                                                    {{ track.release.title }}
                                                </span>
                                                <img
                                                    style="height:18px;opacity:0.5;"
                                                    src="@/assets/svg/dot.svg"
                                                    alt="dot"
                                                >
                                                <span
                                                    class="opacity-50"
                                                    style="line-height: 17px; font-size: 15px"
                                                >
                                                    {{ track.release.release_date }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="opacity-50 w-25">
                                        {{ track.plays }} plays
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.playlist-content {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 0 0 90px 0 !important;
    min-height: 0 !important;
}
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(32, 32, 32, 0.35);
    backdrop-filter: blur(4px);
    z-index: 1;
    pointer-events: none;
}
</style>
