<script setup lang="ts">
import { useModerationStore } from "@/stores/AdminPanel/moderation";
import ReleasesTable from "@/pages/admin/moderation/ReleasesTable.vue";
import { watch } from "vue";

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
        <div class="mt-4 d-flex flex-row gap-3">
            <div class="stat-card bg-minor w-100 d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img class="me-2" src="@/assets/svg/clockYellow.svg" alt="clock" />
                    <span class="opacity-50">Pending Review</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    {{ moderationStore.pendingReleasesNumber }}
                </span>
                <template v-if="moderationStore.pendingReleasesNumber !== 0">
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
                class="btn btn-view d-flex align-items-center justify-content-between"
                :class="{ 'activeView': moderationStore.selectedView === 'pending' }"
            >
                <img class="me-2" src="@/assets/svg/clockWhite.svg" alt="clock">
                <span>Pending</span>
                <span
                    style="font-size: 13px"
                    class="badge-custom ms-2 opacity-0"
                    :class="{ 'opacity-100': moderationStore.selectedView === 'pending' }"
                >
                    {{moderationStore.pendingReleasesNumber}}
                </span>
            </button>
            <button
                @click="moderationStore.selectView('published')"
                class="btn btn-view d-flex align-items-center justify-content-between"
                :class="{ 'activeView': moderationStore.selectedView === 'published' }"
            >
                <img class="me-2" src="@/assets/svg/approve.svg" alt="clock">
                Published
                <span
                    style="font-size: 13px"
                    class="badge-custom ms-2 opacity-0"
                    :class="{ 'opacity-100': moderationStore.selectedView === 'published' }"
                >
                    {{moderationStore.releasesNumber}}
                </span>
            </button>
            <button
                @click="moderationStore.selectView('rejected')"
                style="border-bottom-right-radius: 15px;border-top-right-radius: 15px;"
                class="btn btn-view d-flex align-items-center justify-content-between"
                :class="{ 'activeView': moderationStore.selectedView === 'rejected' }"
            >
                <img class="me-2" src="@/assets/svg/reject.svg" alt="clock">
                Rejected
                <span
                    style="font-size: 13px"
                    class="badge-custom ms-2 opacity-0"
                    :class="{ 'opacity-100': moderationStore.selectedView === 'rejected' }"
                >
                    {{moderationStore.releasesNumber}}
                </span>
            </button>
        </div>
        <div
            class="d-flex flex-row mt-5 position-relative"
            style="max-width: 300px; max-height: 46px"
        >
            <img
                style="top: 12px; left: 15px"
                class="position-absolute z-2"
                src="@/assets/svg/search.svg"
                alt="search"
            />
            <input
                style="
                    border: 1px solid rgba(228, 228, 228, 0.15);
                    padding-left: 40px;
                    color: rgb(228, 228, 228);
                "
                class="w-100 form-control rounded-4 bg-minor"
                type="text"
                v-model="moderationStore.searchInput"
                placeholder="Search by title or artist..."
                data-bs-toggle="dropdown"
                aria-expanded="false"
            />
        </div>
        <div class="mt-4">
            <ReleasesTable/>
        </div>
    </div>
</template>

<style scoped lang="scss">
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
    max-width: 150px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;

    &:hover {
        background-color: rgba(189, 16, 69, .8) !important;
    }

    &:active {
        background-color: rgba(189, 16, 69, .8) !important;
        border-color: rgba(189, 16, 69, .01) !important;
    }
}
.activeView {
    background-color: rgba(189, 16, 69, 0.58) !important;
}
.badge-custom {
    background-color: rgb(32, 32, 32) !important;
    border-radius: 10px !important;
    padding: 1px 8px !important;
    transition: .2s !important;
    font-weight: bold !important;
}
</style>
