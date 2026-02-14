<script setup>
import { useLibraryStore } from '@/stores/library.js'
import { useAuthStore } from '@/stores/auth.js'
import { useReleaseStore } from '@/stores/release.js'
import { useAudioPlayer } from '@/composables/useAudioPlayer.js'
import { watch } from 'vue'
import addedIcon from '@/assets/svg/added.svg'
import addIcon from '@/assets/svg/add.svg'
import { useVibrantPalette } from '@/composables/useVibrantPalette.js'

const auth = useAuthStore()
const releaseStore = useReleaseStore()
const { palette, getCoverPalette } = useVibrantPalette()
const { currentTrack, isPlaying, toggleTrack } = useAudioPlayer()

watch(
    () => releaseStore.pickedRelease?.cover_url,
    async (url) => {
        if (url) {
            await getCoverPalette(url)
        }
    },
    { immediate: true },
)
</script>

<template>
    <template v-if="releaseStore.isLoading">
        <div class="fw-bold fs-5 mx-auto my-auto" style="color: rgba(228,228,228, 0.5)">
            Loading...
        </div>
    </template>
    <template v-else>
        <div style="color: rgb(228, 228, 228)" class="flex-grow-1 release-content">
            <div
                :style="{
                       background: `
                       linear-gradient(
                         180deg,
                         rgba(${palette?.LightMuted?.rgb}, 0.2),
                         rgb(32,32,32)
                       )
                     `,
                       backdropFilter: 'blur(40px)',
                   }"
            >
                <div style="padding: 95px 0 0 320px" class="d-flex flex-column">
                    <div class="d-flex flex-row mb-5">
                        <div>
                            <img
                                :src="releaseStore.pickedRelease?.cover_url"
                                alt="cover"
                                :style="{width: '210px',height: '210px'}"
                                class="rounded-2"
                            />
                        </div>
                        <div
                            style="max-width: 600px"
                            class="d-flex flex-column w-100 justify-content-end"
                        >
                            <h1
                                style="font-size: 55px"
                                class="ms-4 fw-bold"
                                v-text="releaseStore.pickedRelease?.title"
                            ></h1>
                            <h3
                                style="opacity: 10"
                                class="ms-4 mt-1 fs-5 fw-bold"
                                v-text="releaseStore.pickedRelease?.artist"
                            ></h3>
                        </div>
                    </div>
                    <template v-if="auth.user">
                        <div class="pb-3">
                            <button
                                class="btn btn-add-like mb-4"
                                @click="
                                       releaseStore.addReleaseToLikes(releaseStore.pickedRelease.id)
                                   "
                            >
                                <img
                                    style="width: 35px"
                                    :src="releaseStore.isReleaseLiked ? addedIcon : addIcon"
                                    alt="add"
                                />
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            <table class="table table-borderless align-middle" style="padding: 25px 0 0 295px">
                <thead style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                <tr>
                    <th
                        scope="col"
                        style="
                                   font-weight: lighter;
                                   opacity: 60%;
                                   padding-left: 320px;
                                   padding-right: 20px;
                               "
                    >
                        #
                    </th>
                    <th scope="col"></th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Name</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%"></th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">
                        <img src="@/assets/svg/clock.svg" alt="clock" />
                    </th>
                </tr>
                </thead>
                <tbody>
                <template v-for="track in releaseStore.pickedRelease?.tracks" :key="track.id">
                    <tr class="track-row rounded-5">
                        <td
                            class="position-relative"
                            style="width: 10px; padding-left: 320px; padding-right: 0"
                        >
                            <template v-if="currentTrack?.id !== track.id || !isPlaying">
                                <span
                                    class="fw-lighter opacity-50 position-number"
                                    v-text="track.position"
                                ></span>
                            </template>
                            <template v-if="auth.user">
                                <button
                                    type="button"
                                    style="left: 308px; top: 13px"
                                    class="btn z-3 btn-play-table position-absolute"
                                    @click="
                                               toggleTrack(track, releaseStore.pickedRelease?.tracks)
                                           "
                                >
                                    <template v-if="currentTrack?.id !== track.id">
                                        <img src="@/assets/svg/play.svg" alt="play" />
                                    </template>
                                    <template
                                        v-if="currentTrack?.id === track.id && !isPlaying"
                                    >
                                        <img src="@/assets/svg/play.svg" alt="play" />
                                    </template>
                                    <template v-if="currentTrack?.id === track.id && isPlaying">
                                        <img src="@/assets/svg/pause.svg" alt="pause" />
                                    </template>
                                </button>
                            </template>
                            <template v-else>
                                <button
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#authenticateModal"
                                    style="left: 308px; top: 11px"
                                    class="btn z-3 btn-play-table position-absolute"
                                >
                                    <img src="@/assets/svg/play.svg" alt="play" />
                                </button>
                            </template>
                            <div
                                style="left: 320px; top: 25px"
                                class="playing-wave position-absolute"
                                v-if="currentTrack?.id === track.id && isPlaying"
                            >
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </td>
                        <td style="width: 10px"></td>
                        <td>
                            <div class="d-flex flex-column">
                                <span
                                    v-text="track.title"
                                    style="font-size: 15px; font-weight: normal"
                                >
                                </span>
                                <span
                                    v-text="track.artist"
                                    style="opacity: 60%; font-size: 15px; font-weight: lighter"
                                >
                                </span>
                            </div>
                        </td>
                        <td>
                            <template v-if="auth.user">
                                <button
                                    class="btn btn-add-like"
                                    @click="releaseStore.addTrackToLikes(track.id)"
                                >
                                    <img
                                        style="width: 25px"
                                        :class="track.is_added ? '' : 'add-like'"
                                        :src="track.is_added ? addedIcon : addIcon"
                                        alt="add"
                                    />
                                </button>
                            </template>
                        </td>
                        <td>
                            <span
                                class="fw-lighter opacity-50"
                                v-text="track.formatted_duration"
                            >
                            </span>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
            <div class="mt-4" style="padding: 25px 0 0 320px">
                <span
                    style="opacity: 60%; font-size: 15px; font-weight: lighter"
                    v-text="releaseStore.pickedRelease?.released_in"
                ></span>
            </div>
        </div>
    </template>
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

.btn-get-release {
    color: rgb(228, 228, 228) !important;
    border: none !important;

    &:hover {
        color: rgba(228, 228, 228, 0.4) !important;
    }

    &:active {
        border: none !important;
    }
}

.card {
    margin-top: 15px !important;
    backdrop-filter: blur(5px) !important;
    background: none !important;
    border: none !important;

    .card-cover:hover {
        opacity: 0.7 !important;
        transition: 0.4s !important;
    }

    .card-title {
        color: rgb(228, 228, 228);
        font-size: 15px;
        margin: 0 0 5px 0;
    }

    .card-text {
        color: rgba(228, 228, 228, 40%);
        font-size: 13px;
        margin: 0 0 5px 0;
    }
}

.playing-wave {
    display: flex;
    align-items: flex-end;
    height: 12px;
    gap: 2px;
}

.playing-wave span {
    width: 2px;
    height: 4px;
    background: #ff2667;
    animation: wave 1s infinite ease-in-out;
}

.playing-wave span:nth-child(2) {
    animation-delay: 0.2s !important;
}

.playing-wave span:nth-child(3) {
    animation-delay: 0.5s !important;
}

.playing-wave span:nth-child(4) {
    animation-delay: 0.7s !important;
}

.playing-wave span:nth-child(5) {
    animation-delay: 0.1s !important;
}

@keyframes wave {
    0% {
        height: 4px;
    }
    50% {
        height: 12px;
    }
    100% {
        height: 4px;
    }
}
</style>
