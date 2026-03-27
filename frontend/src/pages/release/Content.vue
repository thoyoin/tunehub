<script setup>
import { useAuthStore } from '@/stores/auth.ts'
import { useReleaseStore } from '@/stores/release.ts'
import { useLibraryStore } from '@/stores/library.ts'
import { useAudioPlayer } from '@/composables/useAudioPlayer.ts'
import { useRouter } from 'vue-router'
import { watch } from 'vue'
import addedIcon from '@/assets/svg/added.svg'
import addIcon from '@/assets/svg/add.svg'
import likedIcon from '@/assets/svg/heartFilled.svg'
import likeIcon from '@/assets/svg/heart.svg'
import { useVibrantPalette } from '@/composables/useVibrantPalette.ts'

const auth = useAuthStore()
const releaseStore = useReleaseStore()
const libraryStore = useLibraryStore()
const router = useRouter()
const { palette, getCoverPalette } = useVibrantPalette()
const { currentTrack, isPlaying, toggleTrack } = useAudioPlayer()

function isTrackAdded(track, playlistId) {
    return track.playlist_ids.includes(playlistId)
}

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
    <div style="color: rgb(228, 228, 228)" class="flex-grow-1 release-content position-relative">
        <transition name="fade">
            <div
                v-if="releaseStore.isLoading"
                class="loading-overlay"
            >
                <div class="search-spinner mb-2"></div>
            </div>
        </transition>
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
                            :style="{
                                width: '210px',
                                height: '210px',
                            }"
                            style="box-shadow: 0 0 20px 1px rgba(0, 0, 0, 0.5)"
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
                        <div class="d-flex flex-row align-items-center mb-2">
                            <h3
                                style="opacity: 10; cursor: pointer"
                                class="ms-4 m-0 fs-5 fw-bold"
                                v-text="releaseStore.pickedRelease?.artist"
                                @click="
                                    router.push({
                                    name: 'artist',
                                    params: { artistId: releaseStore.pickedRelease?.user_id},
                                })"
                            ></h3>
                            <img
                                class="opacity-50"
                                src="@/assets/svg/dot.svg"
                                alt="dot"
                            >
                            <span
                                style="font-size: 16px"
                                class="m-0 opacity-50"
                            >
                                {{releaseStore.releaseTracks?.length}} songs,
                                {{releaseStore.pickedRelease?.release_duration}}
                            </span>
                        </div>
                    </div>
                </div>
                <template v-if="auth.user">
                    <div class="pb-3">
                        <button
                            class="btn btn-add-like mb-4"
                            @click="releaseStore.addReleaseToLikes(releaseStore.pickedRelease.id)"
                        >
                            <img
                                style="width: 35px"
                                :src="releaseStore.pickedRelease?.isReleaseLiked ? addedIcon : addIcon"
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
                    <th scope="col" style="font-weight: lighter; opacity: 60%"></th>
                    <th scope="col" class="text-center" style="font-weight: lighter; opacity: 60%">
                        <img src="@/assets/svg/clock.svg" alt="clock" />
                    </th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <template v-for="track in releaseStore.releaseTracks" :key="track.id">
                    <tr class="track-row">
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
                                    style="left: 311px; top: 13px"
                                    class="btn z-3 btn-play-table position-absolute"
                                    @click="toggleTrack(
                                        track,
                                        releaseStore.releaseTracks,
                                        releaseStore.pickedRelease
                                        )"
                                >
                                    <template v-if="currentTrack?.id !== track.id">
                                        <img src="@/assets/svg/play.svg" alt="play" />
                                    </template>
                                    <template v-if="currentTrack?.id === track.id && !isPlaying">
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
                        <td class="text-end pe-5">
                            <template v-if="auth.user">
                                <button
                                    class="btn btn-add-like"
                                    @click="releaseStore.addTrackToLikes(track.id)"
                                >
                                    <img
                                        style="width: 25px"
                                        :class="track.is_liked ? '' : 'add-like'"
                                        :src="track.is_liked ? addedIcon : addIcon"
                                        alt="add"
                                    />
                                </button>
                            </template>
                        </td>
                        <td class="text-center" style="max-width: 20px">
                            <template v-if="track.plays > 0">
                                <span
                                    style="font-size: 15px"
                                    class="opacity-50 fw-lighter d-flex align-items-center justify-content-start"
                                >
                                    <img
                                        class="me-1"
                                        style="width: 15px"
                                        src="@/assets/svg/playWhite.svg"
                                        alt="plays"
                                    >
                                    {{ track.plays }}
                                </span>
                            </template>
                        </td>
                        <td class="text-center">
                            <span class="fw-lighter opacity-50" v-text="track.formatted_duration">
                            </span>
                        </td>
                        <td>
                            <div data-bs-toggle="dropdown" aria-expanded="false">
                                <img
                                    class="add-like"
                                    style="cursor: pointer !important"
                                    src="@/assets/svg/horizontalSettings.svg"
                                    alt="options"
                                />
                            </div>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu">
                                    <button class="dropdown-item d-flex px-2 align-items-center justify-content-between">
                                        Add to playlist
                                        <img src="@/assets/svg/dropdownArrow.svg" alt="arrow">
                                    </button>
                                    <ul class="dropdown-menu submenu">
                                        <template v-for="playlist in libraryStore.userPlaylists">
                                            <template v-if="playlist.slug === 'liked-tracks'">
                                                <li class="d-flex align-items-center">
                                                    <button
                                                        @click="releaseStore.addTrackToLikes(track.id)"
                                                        class="dropdown-item d-flex justify-content-between"
                                                    >
                                                        {{ playlist.title }}
                                                        <img
                                                            class="ms-3"
                                                            :class="track.is_liked ? '' : 'add-like'"
                                                            :src="track.is_liked ? likedIcon : likeIcon"
                                                            alt=""
                                                        >
                                                    </button>
                                                </li>
                                                <li class="d-flex align-items-center justify-content-center">
                                                    <div class="dropdown-border"></div>
                                                </li>
                                            </template>
                                            <template v-else>
                                                <li>
                                                    <button
                                                        @click="releaseStore.addTrackToPlaylist(track.id, playlist.id)"
                                                        class="dropdown-item d-flex justify-content-between"
                                                    >
                                                        {{ playlist.title }}
                                                        <img
                                                            class="ms-3"
                                                            :class="{ 'add-like': !isTrackAdded(track, playlist.id) }"
                                                            :src="isTrackAdded(track, playlist.id) ? likedIcon : likeIcon"
                                                            alt=""
                                                        >
                                                    </button>
                                                </li>
                                            </template>
                                        </template>
                                    </ul>
                                </li>
                            </ul>
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

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.submenu {
    opacity: 0 !important;
    display: block !important;
    transition: .2s !important;
    border: 1px solid rgba(228, 228, 228, 0.2) !important;
}

.dropdown-menu {
    border: 1px solid rgba(228, 228, 228, 0.2) !important;
}

.dropdown-border {
    margin: 5px 0;
    border-bottom: 1px solid rgba(228, 228, 228, 0.2) !important;
    width: 80%;
}

.dropdown-submenu {
    position: relative !important;
}

.dropdown-submenu .submenu {
    position: absolute !important;
    right: 100% !important;
    top: 0 !important;
    backdrop-filter: blur(1px);
}

.dropdown-submenu:hover .submenu {
    opacity: 1 !important;
}
.release-content::-webkit-scrollbar {
    height: 5px !important;
    width: 5px !important;
}

.release-content::-webkit-scrollbar-thumb {
    background: rgba(228, 228, 228, 0.15) !important;
    border-radius: 10px !important;
    transition: 0.2s !important;
}
</style>
