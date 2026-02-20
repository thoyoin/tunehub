<script setup>
import { useReleaseStore } from '@/stores/release.js'
import { onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAudioPlayer } from '@/composables/useAudioPlayer.js'
import { useAuthStore } from '@/stores/auth.js'
import { useRecentlyPlayedStore } from '@/stores/recentlyPlayed.js'

const releaseStore = useReleaseStore()
const recentlyPlayedStore = useRecentlyPlayedStore()
const router = useRouter()
const auth = useAuthStore()
const { currentTrack, isPlaying, toggleTrack, currentContext } = useAudioPlayer()

onMounted(async () => {
    await releaseStore.fetchLatestReleases()
    await recentlyPlayedStore.fetchRecentlyPlayed()
})

watch(() => currentContext, async (context) => {
    if (context) {
        await recentlyPlayedStore.fetchRecentlyPlayed()
    }
})

</script>

<template>
    <div
        style="
            padding: 85px 30px 0 320px;
            color: rgb(228, 228, 228);
            flex: 1 1 auto;
            overflow-y: auto;
            min-height: 0;
        "
        class="w-100"
    >
        <div>
            <div class="w-100" style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                <h1>New releases</h1>
            </div>
            <div class="d-flex flex-wrap">
                <template v-for="release in releaseStore.releases">
                    <div class="card me-4" style="width: 12rem">
                        <button class="btn btn-get-release p-0 position-relative">
                            <img
                                @click="
                                    router.push({
                                        name: 'release',
                                        params: { releaseId: release.id },
                                    })
                                "
                                :src="release.cover_url"
                                class="card-cover rounded-3"
                                style="width: 190px; height: 190px"
                                alt="cover"
                            />
                            <template v-if="auth.user">
                                <button
                                    @click="
                                        toggleTrack(
                                            release.tracks[0],
                                            release.tracks,
                                            release,
                                        )
                                    "
                                    class="btn cover-play-btn"
                                >
                                    <template v-if="currentTrack?.id !== release.tracks[0].id">
                                        <img src="@/assets/svg/play.svg" alt="play" />
                                    </template>
                                    <template
                                        v-if="
                                            currentTrack?.id === release.tracks[0].id && !isPlaying
                                        "
                                    >
                                        <img src="@/assets/svg/play.svg" alt="play" />
                                    </template>
                                    <template
                                        v-if="
                                            currentTrack?.id === release.tracks[0].id && isPlaying
                                        "
                                    >
                                        <img src="@/assets/svg/pause.svg" alt="pause" />
                                    </template>
                                </button>
                            </template>
                        </button>
                        <div class="card-body p-0 pt-2">
                            <h5 class="card-title fw-bold">{{ release.title }}</h5>
                            <div class="d-flex flex-row">
                                <p class="card-text">{{ release.artist }}</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="w-100 mt-4" style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                <h1>Recently played</h1>
            </div>
            <div
                style="padding-bottom: 200px;"
                class="d-flex flex-wrap"
            >
                <template v-if="recentlyPlayedStore.items?.length === 0">
                    <div
                        class="p-5 fw-bold fs-5 d-flex w-100 justify-content-center align-items-center"
                        style="color: rgb(228, 228, 228); opacity: 0.8"
                    >
                        You haven't listened to anything yet...
                    </div>
                </template>
                <template v-for="item in recentlyPlayedStore.items">
                    <div class="card me-4" style="width: 12rem">
                        <button class="btn btn-get-release p-0 position-relative">
                            <img
                                @click="
                                    router.push({
                                        name: item.item_type,
                                        params: { [item.item_type + 'Id']: item.item_id },
                                    })
                                "
                                :src="item.item.cover_url"
                                class="card-cover rounded-3"
                                style="width: 190px; height: 190px"
                                alt="cover"
                            />
                            <template v-if="auth.user">
                                <button
                                    @click="toggleTrack(
                                        item.item.tracks[0],
                                        item.item.tracks,
                                        item.item
                                    )"
                                    class="btn cover-play-btn"
                                >
                                    <template v-if="currentTrack?.id !== item.item.tracks[0].id">
                                        <img src="@/assets/svg/play.svg" alt="play" />
                                    </template>
                                    <template
                                        v-if="
                                            currentTrack?.id === item.item.tracks[0].id
                                            && !isPlaying
                                        "
                                    >
                                        <img src="@/assets/svg/play.svg" alt="play" />
                                    </template>
                                    <template
                                        v-if="
                                            currentTrack?.id === item.item.tracks[0].id
                                            && isPlaying
                                        "
                                    >
                                        <img src="@/assets/svg/pause.svg" alt="pause" />
                                    </template>
                                </button>
                            </template>
                        </button>
                        <div class="card-body p-0 pt-2">
                            <h5 class="card-title fw-bold">
                                {{ item.item.title }}
                            </h5>
                            <template v-if="item.item.title === 'Liked tracks'">
                                <span
                                    style="font-size: 13px; color: rgba(228,228,228,.5);"
                                    v-text="item.item.tracks.length + ' tracks'"
                                >
                                </span>
                            </template>
                            <template v-else>
                                <span
                                    style="font-size: 13px;max-width: 130px;color: rgba(228,228,228,.5);"
                                    v-text="item.item.artist"
                                    class="text-truncate"
                                >
                                </span>
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

.btn-get-release {
    color: rgb(228, 228, 228) !important;
    border: none !important;
    transition: 0.2s;

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

    .btn-get-release:hover {
        .card-cover {
            opacity: 0.8;
        }
        .cover-play-btn {
            opacity: 0.7;
        }
    }

    .card-title {
        color: rgb(228, 228, 228);
        font-size: 15px;
        margin: 0 0 5px 0;
    }

    .card-cover {
        transition: 0.2s;
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

.cover-play-btn {
    z-index: 100;
    position: absolute;
    transition: 0.2s;
    opacity: 0;
    bottom: 2px;
    right: 3px;
    border-radius: 50%;
    max-width: 40px;
    border: none !important;
    padding: 0;

    &:hover {
        opacity: 1 !important;
    }
}
</style>
