<script setup lang="ts">
import { useOverviewStore } from "@/stores/AdminPanel/overview";
import { computed, onMounted } from "vue";
import growthUp from "@/assets/svg/growth-up.svg";
import growthDown from "@/assets/svg/growth-down.svg";

const overviewStore = useOverviewStore();

onMounted(async () => {
    await overviewStore.fetchAllAnalytics();
});

</script>

<template>
    <div
        style="
            padding: 20px 30px 200px 300px;
            color: rgb(228, 228, 228);
            flex: 1 1 auto;
            overflow-y: auto;
            min-height: 0;
        "
        class="w-100 home-content"
    >
        <div class="d-flex flex-column align-items-start">
            <h1 class="fs-3 fw-bold m-0 p-0">Overview</h1>
            <span class="opacity-50">for the last month</span>
        </div>
        <div class="mt-4 d-flex gap-3 flex-wrap">
            <div class="stat-card bg-minor w-100 d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img class="me-1" src="@/assets/svg/playUnfilled.svg" alt="clock" />
                    <span class="opacity-50">Total Streams</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    <template v-if="overviewStore.isLoading">
                        <div class="search-spinner mt-2 ms-1"></div>
                    </template>
                    <template v-else>
                        <div class="d-flex flex-column align-items-start justify-content-start">
                            <span class="fs-2 me-2" v-text="overviewStore.totalPlays"></span>
                            <span
                                style="font-size: 15px"
                                class="opacity-50 fw-normal d-flex align-items-center gap-1"
                                :class="
                                    overviewStore.totalPlaysGrowth! >= 0
                                        ? 'growth-up'
                                        : 'growth-down'
                                "
                            >
                                <img :src="overviewStore.totalPlaysGrowth! >= 0
                                        ? growthUp : growthDown"
                                     alt="up"
                                />
                                {{ overviewStore.totalPlaysGrowth?.toFixed(1) }}% vs last month
                            </span>
                        </div>
                    </template>
                </span>
            </div>
            <div class="stat-card bg-minor w-100 d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img class="me-1" src="@/assets/svg/users.svg" alt="clock" />
                    <span class="opacity-50">New Users</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    <template v-if="overviewStore.isLoading">
                        <div class="search-spinner mt-2 ms-1"></div>
                    </template>
                    <template v-else>
                        <div class="d-flex flex-column align-items-start justify-content-start">
                            <span class="fs-2 me-2" v-text="overviewStore.newUsers"></span>
                            <span
                                style="font-size: 15px"
                                class="opacity-50 fw-normal d-flex align-items-center gap-1"
                                :class="
                                    overviewStore.newUsersGrowth! >= 0
                                        ? 'growth-up'
                                        : 'growth-down'
                                "
                            >
                                <img :src="overviewStore.newUsersGrowth! >= 0
                                        ? growthUp : growthDown"
                                     alt="up"
                                />
                                {{ overviewStore.newUsersGrowth?.toFixed(1) }}% vs last month
                            </span>
                        </div>
                    </template>
                </span>
            </div>
            <div class="stat-card bg-minor w-100 d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img class="me-1" src="@/assets/svg/users.svg" alt="clock" />
                    <span class="opacity-50">New Tracks</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    <template v-if="overviewStore.isLoading">
                        <div class="search-spinner mt-2 ms-1"></div>
                    </template>
                    <template v-else>
                        <div class="d-flex flex-column align-items-start justify-content-start">
                            <span class="fs-2 me-2" v-text="overviewStore.newTracks"></span>
                            <span
                                style="font-size: 15px"
                                class="opacity-50 fw-normal d-flex align-items-center gap-1"
                                :class="
                                    overviewStore.newTracksGrowth! >= 0
                                        ? 'growth-up'
                                        : 'growth-down'
                                "
                            >
                                <img :src="overviewStore.newTracksGrowth! >= 0
                                        ? growthUp : growthDown"
                                     alt="up"
                                />
                                {{ overviewStore.newTracksGrowth?.toFixed(1) }}% vs last month
                            </span>
                        </div>
                    </template>
                </span>
            </div>
            <div class="stat-card bg-minor w-100 d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img class="me-1" src="@/assets/svg/users.svg" alt="clock" />
                    <span class="opacity-50">New Releases</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    <template v-if="overviewStore.isLoading">
                        <div class="search-spinner mt-2 ms-1"></div>
                    </template>
                    <template v-else>
                        <div class="d-flex flex-column align-items-start justify-content-start">
                            <span class="fs-2 me-2" v-text="overviewStore.newReleases"></span>
                            <span
                                style="font-size: 15px"
                                class="opacity-50 fw-normal d-flex align-items-center gap-1"
                                :class="
                                    overviewStore.newReleasesGrowth! >= 0
                                        ? 'growth-up'
                                        : 'growth-down'
                                "
                            >
                                <img :src="overviewStore.newReleasesGrowth! >= 0
                                        ? growthUp : growthDown"
                                     alt="up"
                                />
                                {{ overviewStore.newReleasesGrowth?.toFixed(1) }}% vs last month
                            </span>
                        </div>
                    </template>
                </span>
            </div>
            <div class="stat-card bg-minor w-100 d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img class="me-1" src="@/assets/svg/users.svg" alt="clock" />
                    <span class="opacity-50">New Playlists</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    <template v-if="overviewStore.isLoading">
                        <div class="search-spinner mt-2 ms-1"></div>
                    </template>
                    <template v-else>
                        <div class="d-flex flex-column align-items-start justify-content-start">
                            <span class="fs-2 me-2" v-text="overviewStore.newPlaylists"></span>
                            <span
                                style="font-size: 15px"
                                class="opacity-50 fw-normal d-flex align-items-center gap-1"
                                :class="
                                    overviewStore.newPlaylistsGrowth! >= 0
                                        ? 'growth-up'
                                        : 'growth-down'
                                "
                            >
                                <img :src="overviewStore.newPlaylistsGrowth! >= 0
                                        ? growthUp : growthDown"
                                     alt="up"
                                />
                                {{ overviewStore.newPlaylistsGrowth?.toFixed(1) }}% vs last month
                            </span>
                        </div>
                    </template>
                </span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.search-spinner {
    width: 18px !important;
    height: 18px !important;
    border: 2px solid rgba(228, 228, 228, 0.2) !important;
    border-top: 2px solid rgb(158, 23, 63) !important;
    border-radius: 50% !important;
    animation: spin 0.4s linear infinite !important;
}
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.loading-overlay {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    transition: 0.4s;
    backdrop-filter: blur(1px);
    z-index: 1;
    pointer-events: none;
}

.growth-up {
    color: #3ecf8e;
}

.growth-down {
    color: #ff5a5a;
}
</style>
