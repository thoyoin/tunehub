<script setup>
import { useReleaseStore } from '@/stores/release.ts'
import { onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAudioPlayer } from '@/composables/useAudioPlayer.ts'
import { useAuthStore } from '@/stores/auth.ts'
import { useRecentlyPlayedStore } from '@/stores/recentlyPlayed.ts'

const releaseStore = useReleaseStore()
const recentlyPlayedStore = useRecentlyPlayedStore()
const router = useRouter()
const auth = useAuthStore()
const { currentTrack, isPlaying, toggleTrack, currentContext } = useAudioPlayer()

onMounted(async () => {
    await releaseStore.fetchLatestReleases()
    await recentlyPlayedStore.fetchRecentlyPlayed()
})

watch(
    () => currentContext,
    async (context) => {
        if (context) {
            await recentlyPlayedStore.fetchRecentlyPlayed()
        }
    },
)
</script>

<template>
    <div
        style="
            padding: 85px 30px 200px 320px;
            color: rgb(228, 228, 228);
            flex: 1 1 auto;
            overflow-y: auto;
            min-height: 0;
        "
        class="w-100 home-content"
    >
        <div>
            <div class="w-100" style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                <h1>New releases</h1>
            </div>
            <div class="d-flex flex-wrap">
                <template v-for="release in releaseStore.releases">
                    <div class="card me-1">
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
                                    @click="toggleTrack(release.tracks[0], release.tracks, release)"
                                    class="btn cover-play-btn"
                                >
                                    <template v-if="currentTrack?.id !== release.tracks[0]?.id">
                                        <img src="@/assets/svg/play.svg" alt="play" />
                                    </template>
                                    <template
                                        v-if="
                                            currentTrack?.id === release.tracks[0]?.id && !isPlaying
                                        "
                                    >
                                        <img src="@/assets/svg/play.svg" alt="play" />
                                    </template>
                                    <template
                                        v-if="
                                            currentTrack?.id === release.tracks[0]?.id && isPlaying
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
            <template v-if="auth.user">
                <div
                    class="w-100"
                    style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)"
                >
                    <h1>Recently played</h1>
                </div>
                <div
                    style="overflow-x: auto; overflow-y: hidden"
                    class="d-flex flex-nowrap recently-row"
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
                        <div class="card me-1">
                            <button class="btn btn-get-release p-0 position-relative">
                                <img
                                    @click="
                                        router.push({
                                            name: item.item_type,
                                            params: { [item.item_type + 'Id']: item.id },
                                        })
                                    "
                                    :src="item.cover_url"
                                    class="card-cover rounded-3"
                                    style="width: 190px; height: 190px"
                                    alt="cover"
                                />
                                <template v-if="auth.user">
                                    <button
                                        @click="toggleTrack(item.tracks[0], item.tracks, item)"
                                        class="btn cover-play-btn"
                                    >
                                        <template v-if="currentTrack?.id !== item.tracks[0]?.id">
                                            <img src="@/assets/svg/play.svg" alt="play" />
                                        </template>
                                        <template
                                            v-if="
                                                currentTrack?.id === item.tracks[0]?.id &&
                                                !isPlaying
                                            "
                                        >
                                            <img src="@/assets/svg/play.svg" alt="play" />
                                        </template>
                                        <template
                                            v-if="
                                                currentTrack?.id === item.tracks[0]?.id && isPlaying
                                            "
                                        >
                                            <img src="@/assets/svg/pause.svg" alt="pause" />
                                        </template>
                                    </button>
                                </template>
                            </button>
                            <div class="card-body p-0 pt-2">
                                <h5 class="card-title fw-bold m-0">
                                    {{ item.title }}
                                </h5>
                                <div v-if="item.item_type === 'playlist'">
                                    <span
                                        style="font-size: 13px; color: rgba(228, 228, 228, 0.5)"
                                        v-text="item.item_type"
                                    >
                                    </span>
                                    <span
                                        style="
                                            color: rgba(228, 228, 228, 0.5);
                                            font-size: 13px;
                                            padding: 0 5px;
                                        "
                                        >•</span
                                    >
                                    <span
                                        style="font-size: 13px; color: rgba(228, 228, 228, 0.5)"
                                        v-text="item.tracks.length + ' tracks'"
                                    >
                                    </span>
                                </div>
                                <template v-else>
                                    <span
                                        style="
                                            font-size: 13px;
                                            max-width: 130px;
                                            color: rgba(228, 228, 228, 0.5);
                                        "
                                        v-text="item.artist"
                                        class="text-truncate"
                                    >
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
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
    padding: 10px !important;
    background: none !important;
    border: none !important;
    border-radius: 15px !important;
    transition: .3s;
    min-width: 210px;

    &:hover {
        background-color: rgba(255,255,255, .02) !important;
    }

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
    background: rgb(158, 23, 63);
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

.recently-row {
    scroll-behavior: smooth !important;
}

.recently-row::-webkit-scrollbar {
    height: 4px !important;
}

.recently-row::-webkit-scrollbar-track {
    background: rgba(228, 228, 228, 0) !important;
    border-radius: 10px !important;
}

.recently-row::-webkit-scrollbar-thumb {
    background: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0),
            rgba(255, 255, 255, 0)
    ) !important;
    border-radius: 10px !important;
    transition: 0.2s !important;
}

.recently-row::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 38, 103, 1) !important;
}

.home-content::-webkit-scrollbar {
    height: 5px !important;
    width: 5px !important;
}

.home-content::-webkit-scrollbar-thumb {
    background: rgba(228, 228, 228, 0.15) !important;
    border-radius: 10px !important;
    transition: 0.2s !important;
}
</style>
