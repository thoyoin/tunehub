<script setup lang="ts">
import { useMerchStore } from "@/stores/AdminPanel/merch";
import { onMounted, ref, watch } from "vue";

const adminMerchStore = useMerchStore();

const currentPage = ref<number>(1);

const fetchPage = async (page: number) => {
    currentPage.value = page;

    await adminMerchStore.fetchMerchData(adminMerchStore.selectedView, page);
};

onMounted(async () => {
    await adminMerchStore.fetchMerchData()
})

watch(
    () => adminMerchStore.selectedView,
    (status) => adminMerchStore.fetchMerchData(status, 1),
);
</script>

<template>
    <div class="position-relative">
        <transition name="fade">
            <div
                v-if="adminMerchStore.isLoading"
                class="loading-overlay d-flex flex-column align-items-center justify-content-center"
            >
                <div class="search-spinner mb-2"></div>
            </div>
        </transition>
        <table class="table table-borderless align-middle" style="padding: 25px 0 0 295px">
            <thead style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                <tr>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Merch</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Artist</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Status</th>
                </tr>
            </thead>
            <tbody>
                <template v-for="product in adminMerchStore.merch?.data" :key="product.id">
                    <tr
                        data-bs-toggle="modal"
                        data-bs-target="#merchViewModal"
                        @click="adminMerchStore.setViewingMerch(product)"
                        class="table-row"
                        style="border-bottom: 1px solid rgba(228, 228, 228, 0.05)"
                    >
                        <td style="font-size: 15px">
                            <div class="d-flex align-items-center">
                                <img
                                    class="rounded-1 me-2"
                                    style="
                                        width: 35px;
                                        height: 35px;
                                        border: 1px solid rgba(228, 228, 228, 0.1);
                                    "
                                    :src="product.cover_url"
                                    alt="cover"
                                />
                                <span class="opacity-75">{{ product.title }}</span>
                            </div>
                        </td>
                        <td style="font-size: 15px">
                            <div class="d-flex flex-row align-items-center">
                                <img
                                    class="rounded-circle me-2"
                                    style="
                                        width: 30px;
                                        height: 30px;
                                        border: 1px solid rgba(228, 228, 228, 0.1);
                                    "
                                    :src="product.user?.profile_picture"
                                    alt="artist"
                                />
                                <span class="opacity-75">{{ product.user?.username }}</span>
                            </div>
                        </td>
                        <td style="font-size: 15px">
                            <div>
                                <span class="opacity-50">{{ product.status }}</span>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <template v-if="adminMerchStore.merch?.data?.length !== 0">
            <div class="opacity-50 w-100">
                <span>
                    Showing {{ adminMerchStore.merch?.from }}-{{ adminMerchStore.merch?.to }} of
                    {{ adminMerchStore.merch?.total }}
                </span>
            </div>
        </template>
        <template v-else>
            <div class="d-flex w-100 justify-content-center align-items-center opacity-50">
                Nothing...
            </div>
        </template>
        <div class="d-flex justify-content-end align-items-center mt-3" style="gap: 10px">
            <button
                @click="fetchPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="btn btn-pagination"
            >
                <img src="@/assets/svg/arrowLeft.svg" alt="prev" />
            </button>
            <button
                @click="fetchPage(currentPage + 1)"
                :disabled="currentPage === adminMerchStore.merch?.last_page"
                class="btn btn-pagination"
            >
                <img src="@/assets/svg/arrowRight.svg" alt="next" />
            </button>
        </div>
    </div>
</template>

<style scoped>
.options {
    transition: 0.2s;

    &:hover {
        opacity: 0.5;
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
.btn-pagination {
    border: 1px solid rgba(179, 27, 71, 0.5) !important;
    border-radius: 15px !important;
    color: rgb(228, 228, 228) !important;
    height: 30px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 10px !important;

    &:hover {
        background-color: rgba(179, 27, 71, 0.59) !important;
    }

    &:active {
        background-color: #c11c4c !important;
        border-color: #c11c4c !important;
    }
}
.table-row {
    &:hover td {
        background-color: rgba(50, 50, 51, 50%) !important;
        cursor: pointer;
        transition:
            background-color 0.2s ease,
            box-shadow 0.15s ease !important;
    }

    &:hover {
        box-shadow: inset 0 0 0 1px rgb(60, 60, 61) !important;

        .add-like {
            opacity: 0.7 !important;
        }
        .btn-play-table {
            opacity: 1 !important;
            z-index: 1 !important;
        }
        .position-number {
            opacity: 0 !important;
        }
        .playing-wave {
            opacity: 0 !important;
        }
    }
}
</style>
