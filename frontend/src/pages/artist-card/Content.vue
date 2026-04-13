<script setup lang="ts">
import { useRouter } from "vue-router";
import { watch } from "vue";

import { useVibrantPalette } from "@/composables/useVibrantPalette";
import { useAudioPlayer } from "@/composables/useAudioPlayer";
import { useArtistCardStore } from "@/stores/artistCard";
import { useAuthStore } from "@/stores/auth";
import { useReleaseStore } from "@/stores/release";

const artistCardStore = useArtistCardStore();
const router = useRouter();
const auth = useAuthStore();
const releaseStore = useReleaseStore();
const { palette, getCoverPalette } = useVibrantPalette();
const { currentTrack, isPlaying, toggleTrack } = useAudioPlayer();

watch(
    () => artistCardStore.artist?.profile_picture,
    async (url) => {
        if (url) {
            await getCoverPalette(url);
        }
    },
    { immediate: true },
);

const handleGetRelease = async () => {
    await router.push({
        name: "release",
        params: { releaseId: artistCardStore.artistLatestRelease?.id },
    });
};
</script>

<template>
    <div style="color: rgb(228, 228, 228)" class="flex-grow-1 playlist-content position-relative">
        <transition name="fade">
            <div
                v-if="artistCardStore.isLoading.artist"
                class="loading-overlay d-flex flex-column align-items-center justify-content-center"
            >
                <div class="search-spinner mb-2"></div>
            </div>
        </transition>
        <div
            :style="{
                background: `
                       linear-gradient(
                         180deg,
                         rgba(${palette?.LightVibrant?.rgb}, 0.3),
                         rgb(32,32,32)
                       )
                     `,
                backdropFilter: 'blur(40px)',
            }"
            style="height: 360px"
        >
            <div style="padding: 0 50px 0 350px" class="d-flex flex-column">
                <div
                    style="
                        margin-top: 150px;
                        padding-bottom: 20px;
                        border-bottom: 1px solid rgba(228, 228, 228, 0.15);
                    "
                    class="d-flex flex-row align-items-center"
                >
                    <img
                        style="
                            width: 180px;
                            height: 180px;
                            border-bottom: 1px solid rgba(228, 228, 228, 0.15);
                        "
                        class="rounded-circle"
                        :src="artistCardStore.artist?.profile_picture"
                        alt="cover"
                    />
                    <div class="ms-4">
                        <span class="fw-bold fs-1">
                            {{ artistCardStore.artist?.username }}
                        </span>
                    </div>
                </div>
                <div class="mt-4 row">
                    <div class="col-6 position-relative">
                        <span class="fw-bold fs-5">Latest Release</span>
                            <transition name="fade">
                                <div
                                    v-if="artistCardStore.isLoading.release"
                                    class="loading-overlay"
                                >
                                    <div class="search-spinner mb-2"></div>
                                </div>
                            </transition>
                        <template v-if="!artistCardStore.artistLatestRelease">
                            <div class="mt-4 opacity-50">
                                You have not released anything yet...
                            </div>
                        </template>
                        <template v-else>
                            <div
                                style="cursor: pointer; max-width: 350px"
                                @click="handleGetRelease()"
                                class="mt-4 d-flex flex-row align-items-center position-relative"
                            >
                                <img
                                    class="rounded-3"
                                    style="
                                        width: 180px;
                                        height: 180px;
                                        border: .5px solid rgba(228, 228, 228, 0.1);
                                    "
                                    :src="artistCardStore.artistLatestRelease?.cover_url"
                                    alt="cover"
                                />
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
                        </template>
                    </div>
                    <div style="height: 225px" class="col-6 d-flex flex-column">
                        <span class="fw-bold fs-5">Top Songs</span>
                        <div class="overflow-y-auto flex-1 mt-2 position-relative">
                            <transition name="fade">
                                <div
                                    v-if="artistCardStore.isLoading.songs"
                                    class="loading-overlay d-flex flex-column align-items-center justify-content-center"
                                >
                                    <div class="search-spinner mb-2"></div>
                                </div>
                            </transition>
                            <template v-for="track in artistCardStore.artistTopSongs">
                                <div
                                    style="
                                        border-top: 1px solid rgba(228, 228, 228, 0.15);
                                        cursor: default;
                                    "
                                    class="my-2 track pt-2 w-100 d-flex flex-row align-items-center"
                                >
                                    <div class="position-relative">
                                        <img
                                            class="rounded-1 track-cover"
                                            style="width: 45px; height: 45px"
                                            :src="track.cover_url"
                                            alt="cover"
                                        />
                                        <template v-if="auth.user">
                                            <button
                                                @click="
                                                    toggleTrack(
                                                        track,
                                                        artistCardStore.artistTopSongs!,
                                                        track.release!,
                                                    )
                                                "
                                                class="cover-play-btn btn"
                                            >
                                                <template v-if="currentTrack?.id !== track.id">
                                                    <img src="@/assets/svg/play.svg" alt="play" />
                                                </template>
                                                <template
                                                    v-if="currentTrack?.id === track.id && !isPlaying"
                                                >
                                                    <img src="@/assets/svg/play.svg" alt="play" />
                                                </template>
                                                <template
                                                    v-if="currentTrack?.id === track.id && isPlaying"
                                                >
                                                    <img src="@/assets/svg/pause.svg" alt="pause" />
                                                </template>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#authenticateModal"
                                                class="btn z-3 cover-play-btn"
                                                @click="releaseStore.getRelease(track.release_id)"
                                            >
                                                <img src="@/assets/svg/play.svg" alt="play" />
                                            </button>
                                        </template>
                                    </div>
                                    <div class="d-flex flex-column ms-2 w-100">
                                        <span style="line-height: 17px">
                                            {{ track.title }}
                                        </span>
                                        <div class="d-flex flex-row">
                                            <div>
                                                <span class="opacity-50" style="line-height: 17px">
                                                    {{ track.release?.title }}
                                                </span>
                                                <img
                                                    style="height: 18px; opacity: 0.5"
                                                    src="@/assets/svg/dot.svg"
                                                    alt="dot"
                                                />
                                                <span
                                                    class="opacity-50"
                                                    style="line-height: 17px; font-size: 15px"
                                                >
                                                    {{ track.release?.release_date }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="opacity-50 w-25">{{ track.plays }} plays</div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-4"
                    :class="{
                        'padding-bottom': !artistCardStore.artist?.products?.length
                    }"
                >
                    <span class="fw-bold fs-5">Albums</span>
                    <div class="d-flex flex-row gap-4 mt-3 position-relative">
                        <transition name="fade">
                            <div
                                v-if="artistCardStore.isLoading.albums"
                                class="loading-overlay d-flex flex-column align-items-center justify-content-center"
                            >
                                <div class="search-spinner mb-2"></div>
                            </div>
                        </transition>
                        <template v-for="album in artistCardStore.artistAlbums">
                            <div class="d-flex flex-column">
                                <div
                                    @click="
                                        router.push({
                                            name: 'release',
                                            params: { releaseId: album.id },
                                        })
                                    "
                                    class="release-card"
                                >
                                    <img
                                        class="rounded-3 release-cover"
                                        style="width: 180px;height: 180px;border: .5px solid rgba(228, 228, 228, 0.1);"
                                        :src="album.cover_url"
                                        alt="cover"
                                    >
                                </div>
                                <div
                                    class="d-flex flex-column mt-1"
                                >
                                    <span>{{ album.title }}</span>
                                    <span class="opacity-50">{{ album.release_date }}</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <template v-if="artistCardStore.artist?.products?.length">
                    <div class="mt-4" style="padding-bottom: 200px">
                        <span class="fw-bold fs-5">Merch</span>
                        <div class="d-flex flex-column gap-1 mt-3">
                            <template v-for="product in artistCardStore.artist?.products">
                                <div
                                    @click="router.push({
                                        name: 'artist.merch.all',
                                        params: { artistId: artistCardStore.artist.id},
                                    })"
                                    class="d-flex merch-card flex-row align-items-center"
                                >
                                    <img
                                        class="me-3 ms-1 rounded-2"
                                        style="width: 55px;height: 55px"
                                        :src="product.cover_url"
                                        alt="cover"
                                    >
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">
                                            {{ product.title }}
                                        </span>
                                        <span
                                            style="font-size: 15px"
                                            class="opacity-50"
                                        >
                                            {{ product.description }}
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.playlist-content {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 0 0 90px 0 !important;
    min-height: 0 !important;
}

.cover-play-btn {
    z-index: 100;
    position: absolute;
    transition: 0.2s;
    opacity: 0;
    bottom: 0;
    right: 0;
    top: 5px;
    left: 4px;
    border-radius: 50%;
    max-width: 40px;
    border: none !important;
    padding: 0;
}

.track-cover {
    transition: 0.2s;
}

.track:hover {
    .track-cover {
        opacity: 0.4;
    }

    .cover-play-btn {
        opacity: 1;
    }
}

.release-cover {
    transition: .2s;
}

.release-card:hover {
    .release-cover {
        opacity: .7;
    }
}

.merch-card {
    transition: .2s;
    border-radius: 25px;
    max-width: 500px;
    padding: 10px;

    &:hover {
        background-color: rgba(228, 228, 228, 0.05);
    }
}

.padding-bottom {
    padding-bottom: 100px;
}
</style>
