<script setup>
import { useLibraryStore } from '@/stores/library.js'
import { useAuthStore } from '@/stores/auth.js'
import { useReleaseStore } from '@/stores/release.js'
import { useAudioPlayer } from '@/composables/useAudioPlayer.js'
import { useToast } from 'vue-toastification'
import api from '@/lib/api.js'
import { watch } from 'vue'
import addedIcon from '@/assets/svg/added.svg'
import addIcon from '@/assets/svg/add.svg'
import { useVibrantPalette } from '@/composables/useVibrantPalette.js'
import router from '@/router/index.js'

const libraryStore = useLibraryStore()
const toast = useToast()
const auth = useAuthStore()
const releaseStore = useReleaseStore()
const { palette, getCoverPalette } = useVibrantPalette()
const { currentTrack, isPlaying, toggleTrack } = useAudioPlayer()

const handlePlaylistDeletion = async () => {
    try {
        await api.delete(`/api/playlist/${libraryStore.libraryItem.id}`)

        await libraryStore.fetchItems()

        libraryStore.clearSelectedItem()

        toast.success('Playlist deleted successfully.')

        await router.push('/')
    } catch (error) {
        console.log(error)

        toast.error('Something went wrong.')
    }
}

watch(
    () => libraryStore.libraryItem?.cover_url,
    async (url) => {
        if (url) {
            await getCoverPalette(url)
        }
    },
    { immediate: true },
)
</script>

<template>
    <div style="color: rgb(228, 228, 228)" class="flex-grow-1 playlist-content">
        <div
            :style="{
                background: `
                    linear-gradient(
                      180deg,
                      rgba(${palette?.LightMuted?.rgb}, 0.3),
                      rgb(32,32,32)
                    )
                  `,
                backdropFilter: 'blur(40px)',
            }"
            class="d-flex pb-5"
            style="padding: 90px 0 0 320px"
        >
            <div>
                <img
                    :src="libraryStore.libraryItem?.cover_url"
                    alt="cover"
                    :style="{
                        width: '210px',
                        height: '210px',
                    }"
                    class="rounded-1"
                />
            </div>
            <div style="max-width: 600px" class="d-flex flex-column w-100 justify-content-end">
                <h1
                    style="font-size: 55px"
                    class="ms-4 fw-bold"
                    v-text="libraryStore.libraryItem?.title"
                ></h1>
                <h5
                    style="opacity: 0.3; font-size: 20px"
                    class="ms-4 mt-1 fw-light"
                    v-text="libraryStore.libraryItem?.description"
                ></h5>
                <div class="d-flex flex-row mt-1 align-items-center">
                    <img
                        :src="libraryStore.libraryItem?.user.profile_picture"
                        style="width: 30px; height: 30px"
                        class="rounded-5 ms-4"
                        alt="profile"
                    />
                    <h3
                        style="opacity: 10"
                        class="ms-2 mb-0 fs-5 fw-bold"
                        v-text="libraryStore.libraryItem?.user.username"
                    ></h3>
                    <span style="font-size: 25px; opacity: 50%; padding: 0 5px 2px 5px">•</span>
                    <h3
                        style="font-size: 18px; opacity: 50%"
                        class="fw-bold m-0"
                        v-text="libraryStore.itemTracks?.length + ' tracks'"
                    ></h3>
                </div>
            </div>
            <template v-if="libraryStore.libraryItem?.slug !== 'liked-tracks'">
                <div class="w-50 text-end p-2 me-5 z-">
                    <a
                        class="btn btn-settings p-0"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <img src="@/assets/svg/settings.svg" alt="settings" />
                    </a>
                    <form @submit.prevent="handlePlaylistDeletion">
                        <ul class="dropdown-menu">
                            <li>
                                <button
                                    type="button"
                                    class="dropdown-item"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                >
                                    Edit
                                </button>
                            </li>
                            <li>
                                <button type="submit" class="dropdown-item">Delete</button>
                            </li>
                        </ul>
                    </form>
                </div>
            </template>
        </div>
        <table class="table table-borderless align-middle">
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
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Album</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Date of adding</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%"></th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">
                        <img src="@/assets/svg/clock.svg" alt="clock" />
                    </th>
                </tr>
            </thead>
            <tbody>
                <template v-for="track in libraryStore.itemTracks" :key="track.id">
                    <tr class="track-row rounded-5">
                        <td
                            class="position-relative"
                            style="width: 10px; padding-left: 320px; padding-right: 0"
                        >
                            <template v-if="currentTrack?.id !== track.id || !isPlaying">
                                <span
                                    class="fw-lighter opacity-50 position-number"
                                    v-text="track.pivot.position"
                                ></span>
                            </template>
                            <template v-if="auth.user">
                                <button
                                    type="button"
                                    style="left: 308px; top: 13px"
                                    class="btn z-3 btn-play-table position-absolute"
                                    @click="toggleTrack(track, libraryStore.itemTracks[0])"
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
                        <td style="width: 10px; padding: 0"></td>
                        <td>
                            <div class="d-flex flex-row">
                                <img
                                    style="
                                        width: 43px;
                                        height: 43px;
                                        object-fit: cover;
                                        margin-right: 10px;
                                    "
                                    :src="track.cover_url"
                                    class="rounded-1"
                                    alt="cover"
                                />
                                <div class="d-flex flex-column">
                                    <span
                                        v-text="track.title"
                                        style="font-size: 15px; font-weight: normal"
                                    ></span>
                                    <span
                                        v-text="track.artist"
                                        style="opacity: 60%; font-size: 15px; font-weight: lighter"
                                    ></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <button
                                @click=""
                                style="opacity: 60%; font-size: 15px; font-weight: lighter"
                                class="btn btn-get-release p-0"
                                v-text="track.release.title"
                            ></button>
                        </td>
                        <td>
                            <span
                                style="opacity: 60%; font-size: 15px; font-weight: lighter"
                                v-text="track.added_ago"
                            ></span>
                        </td>
                        <td class="text-center">
                            <template v-if="auth.user">
                                <button
                                    class="btn btn-add-like"
                                    @click="releaseStore.addTrackToLikes(track.id)"
                                >
                                    <img
                                        style="width: 25px"
                                        class="add-like"
                                        :src="track.is_added ? addedIcon : addIcon"
                                        alt="add"
                                    />
                                </button>
                            </template>
                        </td>
                        <td>
                            <span
                                style="opacity: 60%; font-size: 15px; font-weight: lighter"
                                v-text="track.formatted_duration"
                            ></span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <template v-if="libraryStore.itemTracks.length === 0">
            <div class="d-flex w-100 align-items-center justify-content-center">
                <span class="fw-light">You haven't added any tracks yet.</span>
            </div>
        </template>
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
</style>
