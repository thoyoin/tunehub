<script setup lang="ts">
import { useModerationStore } from "@/stores/AdminPanel/moderation";
import ReleasesTable from "@/pages/admin/moderation/ReleasesTable.vue";

const moderationStore = useModerationStore();

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
        <div class="fs-3 fw-bold">Release Moderation</div>
        <div class="mt-4" style="max-width: 250px">
            <div class="stat-card bg-minor d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img class="me-2" src="@/assets/svg/clockYellow.svg" alt="clock" />
                    <span class="opacity-50">Pending Review</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    {{ moderationStore.releases?.data?.length }}
                </span>
                <template v-if="moderationStore.releases?.data?.length !== 0">
                    <span style="font-size: 13px; color: rgb(211, 181, 0)" class="mt-2 fw-light">
                        Requires attention
                    </span>
                </template>
            </div>
        </div>
        <div
            role="group"
            class="btn-group d-flex flex-row mt-4 w-50"
        >
            <button
                @click="moderationStore.selectView('pending')"
                style="border-bottom-left-radius: 15px;border-top-left-radius: 15px;"
                class="btn btn-view d-flex align-items-center justify-content-center"
                :class="{ 'activeView': moderationStore.selectedView === 'pending' }"
            >
                <img class="me-2" src="@/assets/svg/clockWhite.svg" alt="clock">
                Pending
            </button>
            <button
                @click="moderationStore.selectView('published')"
                class="btn btn-view d-flex align-items-center justify-content-center"
                :class="{ 'activeView': moderationStore.selectedView === 'published' }"
            >
                <img class="me-2" src="@/assets/svg/approve.svg" alt="clock">
                Published
            </button>
            <button
                @click="moderationStore.selectView('rejected')"
                style="border-bottom-right-radius: 15px;border-top-right-radius: 15px;"
                class="btn btn-view d-flex align-items-center justify-content-center"
                :class="{ 'activeView': moderationStore.selectedView === 'rejected' }"
            >
                <img class="me-2" src="@/assets/svg/reject.svg" alt="clock">
                Rejected
            </button>
        </div>
        <div class="mt-4">
            <ReleasesTable/>
        </div>
    </div>
</template>

<style scoped lang="scss">
.stat-card {
    padding: 15px;
    border: 1px solid rgba(228, 228, 228, 0.15) !important;
    border-radius: 15px;
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

.search-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(228, 228, 228, 0.2);
    border-top: 2px solid rgb(158, 23, 63);
    border-radius: 50%;
    animation: spin 0.4s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.btn-view {
    background-color: rgba(50,50,51, 15%) !important;
    border: 1px solid rgba(50,50,51, 1) !important;
    color: rgb(228,228,228) !important;
    height: 35px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    &:hover {
        background-color: rgba(189, 16, 69, .8) !important;
    }

    &:active {
        background-color: rgba(189, 16, 69, 1) !important;
        border-color: #c11c4c !important;
    }
}
.activeView {
    background-color: rgba(189, 16, 69, .8) !important;
}
</style>
