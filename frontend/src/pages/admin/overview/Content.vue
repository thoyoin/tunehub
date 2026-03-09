<script setup lang="ts">
import { useOverviewStore } from "@/stores/AdminPanel/overview";
import { onMounted } from "vue";

const overviewStore = useOverviewStore();

onMounted( async () => {
    await overviewStore.fetchTotalPlays();
    await overviewStore.fetchNewUsers();
})

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
        <div class="fs-3 fw-bold">Overview</div>
        <div class="mt-4 d-flex flex-row gap-3">
            <div class="stat-card bg-minor w-100 d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img
                        class="me-1"
                        src="@/assets/svg/playUnfilled.svg"
                        alt="clock"
                    />
                    <span class="opacity-50">Total plays</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    <template v-if="overviewStore.isLoading">
                        <div class="search-spinner mt-2 ms-1"></div>
                    </template>
                    <template v-else>
                        <span class="fs-4 mt-2" v-text="overviewStore.totalPlays"></span>
                    </template>
                </span>
            </div>
            <div class="stat-card bg-minor w-100 d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img
                        class="me-1"
                        src="@/assets/svg/users.svg"
                        alt="clock"
                    />
                    <span class="opacity-50">New users</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    <template v-if="overviewStore.isLoading">
                        <div class="search-spinner mt-2 ms-1"></div>
                    </template>
                    <template v-else>
                        <span class="fs-4 mt-2" v-text="overviewStore.newUsers"></span>
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
</style>
