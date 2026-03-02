<script setup lang="ts">
import { useModerationStore } from "@/stores/AdminPanel/moderation";
import { onMounted, ref, watch} from "vue";
import { useToast } from "vue-toastification";

const moderationStore = useModerationStore();
const toast = useToast();

const currentPage = ref<number>(1);

const fetchPage = async (page: number) => {
    currentPage.value = page;

    await moderationStore.fetchByStatus(moderationStore.selectedView, page);
};

onMounted( async() => {
    await moderationStore.fetchByStatus(moderationStore.selectedView, 1);
});

watch(() => moderationStore.selectedView, (status) =>
    moderationStore.fetchByStatus(status)
);

const handleReleaseStatusUpdate = async (status: string, id: number) => {
    try {
        await moderationStore.updateReleaseStatus(status, id)
    } catch (e) {
        console.error(e)
    } finally {
        toast.success("Release status updated");
    }
}

</script>

<template>
    <div class="position-relative">
        <transition name="fade">
            <div
                v-if="moderationStore.isLoading"
                class="loading-overlay d-flex flex-column align-items-center justify-content-center"
            >
                <div class="search-spinner mb-2"></div>
            </div>
        </transition>
        <table class="table table-borderless align-middle" style="padding: 25px 0 0 295px">
            <thead style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                <tr>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Release</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Artist</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Type</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Tracks</th>
<!--                    <th scope="col" style="font-weight: lighter; opacity: 60%">Actions</th>-->
                </tr>
            </thead>
            <tbody>
                <template v-for="release in moderationStore.releases?.data" :key="release.id">
                    <tr
                        data-bs-toggle="modal"
                        data-bs-target="#releaseViewModal"
                        @click="moderationStore.setViewRelease(release)"
                        class="table-row"
                        style="border-bottom: 1px solid rgba(228, 228, 228, 0.05)"
                    >
                        <td style="font-size: 15px">
                            <img
                                class="rounded-1 me-2"
                                style="
                                    width: 35px;
                                    height: 35px;
                                    border: 1px solid rgba(228, 228, 228, 0.1);
                                "
                                :src="release.cover_url"
                                alt="cover"
                            />
                            <span>{{ release.title }}</span>
                        </td>
                        <td style="font-size: 15px">
                            <img
                                class="rounded-circle me-2"
                                style="
                                    width: 35px;
                                    height: 35px;
                                    border: 1px solid rgba(228, 228, 228, 0.1);
                                "
                                :src="release.user.profile_picture"
                                alt="artist"
                            />
                            <span class="opacity-50">{{ release.artist }}</span>
                        </td>
                        <td style="font-size: 15px">
                            {{ release.release_type }}
                        </td>
                        <td>
                            {{ release.tracks.length }}
                        </td>
<!--                        <td style="width: 100px; padding-left: 20px">-->
<!--                            <img-->
<!--                                data-bs-toggle="dropdown"-->
<!--                                aria-expanded="false"-->
<!--                                style="cursor: pointer"-->
<!--                                src="@/assets/svg/horizontalSettingsWhite.svg"-->
<!--                                alt="settings"-->
<!--                                class="options"-->
<!--                            />-->
<!--                            <ul class="dropdown-menu">-->
<!--                                <li-->
<!--                                    style="border-bottom: 1px solid rgba(228, 228, 228, 0.2)"-->
<!--                                    class="dropdown-item d-flex align-items-center mb-1"-->
<!--                                    data-bs-toggle="modal"-->
<!--                                    data-bs-target="#releaseViewModal"-->
<!--                                    @click="moderationStore.setViewRelease(release)"-->
<!--                                >-->
<!--                                    <img class="me-2" src="@/assets/svg/view.svg" alt="view" />-->
<!--                                    View Details-->
<!--                                </li>-->
<!--                                <template v-if="moderationStore.selectedView !== 'published'">-->
<!--                                    <li-->
<!--                                        class="dropdown-item d-flex align-items-center  mb-1"-->
<!--                                        @click="handleReleaseStatusUpdate('approved', release.id)"-->
<!--                                    >-->
<!--                                        <img class="me-2" src="@/assets/svg/approve.svg" alt="delete" />-->
<!--                                        Approve-->
<!--                                    </li>-->
<!--                                </template>-->
<!--                                <template v-if="moderationStore.selectedView !== 'rejected'">-->
<!--                                    <li-->
<!--                                        class="dropdown-item d-flex align-items-center mb-1"-->
<!--                                        @click="handleReleaseStatusUpdate('rejected', release.id)"-->
<!--                                    >-->
<!--                                        <img class="me-2" src="@/assets/svg/reject.svg" alt="delete" />-->
<!--                                        Reject-->
<!--                                    </li>-->
<!--                                </template>-->
<!--                            </ul>-->
<!--                        </td>-->
                    </tr>
                </template>
            </tbody>
        </table>
        <template v-if="moderationStore.releases?.data?.length !== 0">
            <div class="opacity-50 w-100">
            <span>
                Showing {{ moderationStore.releases?.from }}-{{ moderationStore.releases?.to }} of
                {{ moderationStore.releases?.total }}
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
                class="btn btn-pagination"
                @click="fetchPage(currentPage - 1)"
                :disabled="currentPage === 1"
            >
                <img src="@/assets/svg/arrowLeft.svg" alt="prev" />
            </button>
            <button
                class="btn btn-pagination"
                @click="fetchPage(currentPage + 1)"
                :disabled="currentPage === moderationStore.releases?.last_page"
            >
                <img src="@/assets/svg/arrowRight.svg" alt="next" />
            </button>
        </div>
    </div>
</template>

<style scoped lang="scss">
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
        background-color: rgba(50,50,51, 50%) !important;
        cursor: pointer;
        transition: background-color .2s ease, box-shadow .15s ease !important;
    }

    &:hover {
        box-shadow: inset 0 0 0 1px rgb(60,60,61) !important;

        .add-like {
            opacity: .7 !important;
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
