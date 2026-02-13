<script setup>
import { useReleaseStore } from '@/stores/release.js'
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const releaseStore = useReleaseStore()
const router = useRouter()

onMounted(() => {
    releaseStore.fetchLatestReleases()
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
            <div
                class="fw-bold w-100"
                style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)"
            >
                <h1>New releases</h1>
            </div>
            <div class="d-flex flex-wrap">
                <template v-for="release in releaseStore.releases">
                    <div class="card me-4" style="width: 12rem">
                        <button
                            @click="router.push({
                                 name: 'release',
                                 params: { releaseId: release.id }
                            })"
                            class="btn btn-get-release p-0"
                        >
                            <img
                                :src="release.cover_url"
                                class="card-cover rounded-3"
                                style="width: 190px; height: 190px"
                                alt="cover"
                            />
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
</style>
