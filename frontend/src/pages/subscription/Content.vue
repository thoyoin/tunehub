<script setup lang="ts">
import { useRouter } from "vue-router";
import { useSubscriptionStore } from "@/stores/subscription";
import { onMounted } from "vue";

const router = useRouter()
const subscriptionStore = useSubscriptionStore();

</script>

<template>
    <div
        style="color: rgb(228, 228, 228)"
        class="flex-grow-1 subscription-content position-relative"
    >
        <div
            class="rounded-5"
            style="border: 1px solid rgba(228, 228, 228, 0.15); margin: 100px 50px 50px 50px; padding: 20px"
        >
            <div class="d-flex flex-column">
                <span class="fs-2 mb-3 fw-bold">Unlock Artist Tools with TuneHub Premium</span>
                <span class="fw-normal opacity-50" style="font-size: 17px">
                    Publish your own tracks, earn from streams, access artist analytics,
                    and distribute exclusive early releases. Choose a plan built for creators and
                    independent labels.
                </span>
                <button
                    style="max-width: 120px"
                    class="btn btn-subscription mt-4"
                >
                    Get Started
                </button>
                <div class="d-flex flex-row mt-4 opacity-50 gap-5">
                    <span class="d-flex align-items-center gap-2">
                        <img src="@/assets/svg/upload.svg" alt="">
                        Upload and manage unlimited tracks
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <img src="@/assets/svg/dollar.svg" alt="">
                        Monetize streams with transparent payouts
                    </span>
                </div>
            </div>
        </div>
        <div class="d-flex flex-row gap-4 justify-content-center">
            <div
                style="border: 1px solid rgba(228, 228, 228, 0.15);padding: 20px;
                max-width: 500px;height: 400px"
                class="rounded-5 w-100 d-flex flex-column justify-content-between"
            >
                <div>
                    <div class="d-flex flex-row justify-content-between">
                        <div class="d-flex flex-column">
                        <span class="fw-bold fs-3">
                            Free
                        </span>
                            <span class="fw-normal opacity-50" style="font-size: 15px">
                            Listen and discover with core features
                        </span>
                        </div>
                        <div class="badge opacity-75" style="color: #ff2667">
                            For Listeners
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="fs-2 fw-bolder">$0</span>
                        <span class="fs-5 fw-normal opacity-50">/mo</span>
                    </div>
                    <div class="d-flex flex-column gap-2 mt-4">
                        <span>Ad-free streaming for listeners</span>
                        <span>Curated playlists</span>
                        <span>Create and save personal playlists</span>
                    </div>
                </div>
                <div class="mt-4">
                    <button
                        @click="router.push('/')"
                        class="btn btn-free w-100"
                    >
                        Start Listening
                    </button>
                </div>
            </div>
            <div
                style="border: 1px solid rgba(228, 228, 228, 0.15);padding: 20px;max-width: 500px"
                class="rounded-5 w-100 d-flex flex-column justify-content-between"
            >
                <div>
                    <div class="d-flex flex-row justify-content-between">
                        <div class="d-flex flex-column">
                        <span class="fw-bold fs-3">
                            Premium
                        </span>
                            <span class="fw-normal opacity-50" style="font-size: 15px">
                            For independent artists
                        </span>
                        </div>
                        <div class="badge opacity-75" style="color: #ff2667">
                            For Creators
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="fs-2 fw-bolder">$13.99</span>
                        <span class="fs-5 fw-normal opacity-50">/mo</span>
                    </div>
                    <div class="d-flex flex-column gap-2 mt-4">
                        <span>Unlimited track uploads</span>
                        <span>Direct monetization and payout dashboard</span>
                        <span>Release analytics (plays, regions)</span>
                        <span>Promote tracks in curated feeds</span>
                    </div>
                </div>
                <div class="mt-4">
                    <button
                        data-bs-toggle="modal"
                        data-bs-target="#subscribeModal"
                        class="btn btn-subscription w-100"
                    >
                        Choose Premium
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.subscription-content {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 0 30px 150px 30px !important;
    min-height: 0 !important;
}
.btn-subscription {
    background-color: rgba(32,32,32, 30%) !important;
    backdrop-filter: blur(10px) !important;
    height: 35px !important;
    padding: 0 5px !important;
    color: #ff2667 !important;
    border-radius: 15px !important;
    background-color: rgb(40,40,41) !important;

    &::placeholder {
        color: rgba(228,228,228, .5) !important;
    }

    &:hover {
        border-color:  rgb(75,75,75) !important;
    }
    &:active {
        border-color:  #ff2667 !important;
    }
}

.btn-free {
    background-color: rgba(32,32,32, 30%) !important;
    backdrop-filter: blur(10px) !important;
    height: 35px !important;
    padding: 0 5px !important;
    color: rgb(228,228,228) !important;
    border-radius: 15px !important;
    background-color: rgb(40,40,41) !important;

    &::placeholder {
        color: rgba(228,228,228, .5) !important;
    }

    &:hover {
        border-color:  rgb(75,75,75) !important;
    }
    &:active {
        border-color:  #ff2667 !important;
    }
}
</style>
