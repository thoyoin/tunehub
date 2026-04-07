<script setup lang="ts">
import MerchTable from "@/pages/admin/merch/MerchTable.vue";
import { useMerchStore } from "@/stores/AdminPanel/merch";

const adminMerchStore = useMerchStore();

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
        <div class="fs-3 fw-bold">Merch Products</div>
        <div class="mt-4 d-flex flex-row gap-3">
            <div class="stat-card bg-minor w-100 d-flex flex-column">
                <span class="d-flex flex-row align-items-center">
                    <img class="me-2" src="@/assets/svg/clockYellow.svg" alt="clock" />
                    <span class="opacity-50">Pending Review</span>
                </span>
                <span class="fs-4 mt-2 fw-bold">
                    {{ adminMerchStore.moderatingMerch.length }}
                </span>
                <template v-if="adminMerchStore.moderatingMerch.length">
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
                @click="adminMerchStore.selectView('all')"
                style="border-bottom-left-radius: 15px;border-top-left-radius: 15px;"
                class="btn btn-view d-flex align-items-center justify-content-between"
                :class="{ 'activeView': adminMerchStore.selectedView === 'all' }"
            >
                <span>All</span>
                <span
                    style="font-size: 13px"
                    class="badge-custom ms-2 opacity-0"
                    :class="{ 'opacity-100': adminMerchStore.selectedView === 'all' }"
                >
                    {{adminMerchStore.merch?.data.length}}
                </span>
            </button>
            <button
                @click="adminMerchStore.selectView('moderating')"
                class="btn btn-view d-flex align-items-center justify-content-between"
                :class="{ 'activeView': adminMerchStore.selectedView === 'moderating' }"
            >
                Moderating
                <span
                    style="font-size: 13px"
                    class="badge-custom ms-2 opacity-0"
                    :class="{ 'opacity-100': adminMerchStore.selectedView === 'moderating' }"
                >
                    {{adminMerchStore.moderatingMerch.length}}
                </span>
            </button>
            <button
                @click="adminMerchStore.selectView('rejected')"
                style="border-bottom-right-radius: 15px;border-top-right-radius: 15px;"
                class="btn btn-view d-flex align-items-center justify-content-between"
                :class="{ 'activeView': adminMerchStore.selectedView === 'rejected' }"
            >
                Rejected
                <span
                    style="font-size: 13px"
                    class="badge-custom ms-2 opacity-0"
                    :class="{ 'opacity-100': adminMerchStore.selectedView === 'rejected' }"
                >
                    {{adminMerchStore.rejectedMerch.length}}
                </span>
            </button>
        </div>
        <div class="mt-4">
            <MerchTable/>
        </div>
    </div>
</template>

<style scoped>
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
