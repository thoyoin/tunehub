<script setup lang="ts">
import { useOverviewStore } from "@/stores/AdminPanel/overview";
import { onMounted } from "vue";
import growthUp from "@/assets/svg/growth-up.svg";
import growthDown from "@/assets/svg/growth-down.svg";
import StreamsCharts from "@/pages/admin/overview/charts/StreamsCharts.vue";
import UserGrowthCharts from "@/pages/admin/overview/charts/UserGrowthCharts.vue";
import { useModerationStore } from "@/stores/AdminPanel/moderation";
import { useUsersStore } from "@/stores/AdminPanel/users";

const overviewStore = useOverviewStore();
const moderationStore = useModerationStore();
const usersStore = useUsersStore();

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
            <h1 class="fs-3 fw-bold m-0 p-0">Analytics</h1>
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
        <div class="mt-4 border px-2">
            <div class="d-flex flex-column align-items-start">
                <span class="ms-4 mt-4 fw-bold fs-5">Streams Overview</span>
                <span class="ms-4 fw-normal opacity-50 ">for the last month</span>
            </div>
            <StreamsCharts/>
        </div>
        <div class="mt-4 border px-2">
            <div class="d-flex flex-column align-items-start">
                <span class="ms-4 mt-4 fw-bold fs-5">User Growth</span>
            </div>
            <UserGrowthCharts/>
        </div>
        <div class="mt-4 border px-4 pb-4">
            <div class="d-flex flex-column align-items-start">
                <span class="mt-4 fw-bold fs-5">Top Artists</span>
            </div>
            <div>
                <template v-for="artist in overviewStore.topArtists">
                    <div
                        data-bs-toggle="modal"
                        data-bs-target="#userProfileModal"
                        @click="usersStore.setViewUser(artist.artist)"
                        class="border artist-row mt-3 mb-2 p-3 d-flex flex-row
                            justify-content-between align-items-center
                        "
                    >
                        <div>
                            <img
                            style="width: 35px"
                            class="rounded-circle me-3"
                            :src="artist.artist.profile_picture"
                            alt=""
                            >
                            <span class="fw-bold">
                                {{ artist.artist.username }}
                            </span>
                        </div>
                        <div>
                            <span class="fw-bolder">{{ artist.streams }}</span>
                            <span class="fw-normal"> streams</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="mt-4 border px-4 pb-4">
            <div class="d-flex flex-column align-items-start">
                <span class="mt-4 fw-bold fs-5">Top Releases</span>
            </div>
            <div>
                <template v-for="release in overviewStore.topReleases">
                    <div
                        data-bs-toggle="modal"
                        data-bs-target="#releaseViewModal"
                        @click="moderationStore.setViewRelease(release)"
                        class="border artist-row mt-3 mb-2 p-3 d-flex flex-row
                            justify-content-between align-items-center
                        "
                    >
                        <div class="d-flex flex-row align-items-center">
                            <img
                                style="width: 35px;height: 35px;"
                                class="rounded-circle me-3"
                                :src="release.cover_url"
                                alt=""
                            >
                            <div class="d-flex flex-column">
                                <span
                                    style="line-height: 18px"
                                    class="fw-bold"
                                >
                                    {{ release.title }}
                                </span>
                                <span
                                    style="line-height: 18px;font-size: 15px"
                                    class="fw-normal opacity-50"
                                >
                                    {{ release.artist }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <span class="fw-bolder">{{ release.plays }}</span>
                            <span class="fw-normal"> streams</span>
                        </div>
                    </div>
                </template>
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

:deep(.apexcharts-tooltip) {
    background: transparent !important;
    color: rgb(228, 228, 228) !important;
    backdrop-filter: blur(10px) !important;
    border: 1px solid #3a3a3a !important;
    border-radius: 12px !important;
}

:deep(.apexcharts-tooltip-title) {
    background: rgba(158, 23, 63, .2) !important;
    backdrop-filter: blur(10px) !important;
    color: #ffffff !important;
    border-bottom: none !important;
}

:deep(.apexcharts-xaxistooltip) {
    background: none !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
}

.border {
    border:1px solid rgba(228, 228, 228, 0.15) !important;
    border-radius: 18px;
}

.artist-row {
    transition: .2s;
    cursor: pointer;

    &:hover {
        background-color: rgba(228, 228, 228, 0.05) !important;
    }
}
</style>
