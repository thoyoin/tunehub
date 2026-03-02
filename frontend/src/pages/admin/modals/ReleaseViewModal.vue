<script setup lang="ts">
import { useModerationStore } from "@/stores/AdminPanel/moderation";
import { useToast } from "vue-toastification";
import { useAudioPlayer } from "@/composables/useAudioPlayer";

const moderationStore = useModerationStore();
const { currentTrack, isPlaying, toggleTrack } = useAudioPlayer()
const toast = useToast();

const handleReleaseStatusUpdate = async (status: string) => {
    try {
        await moderationStore.updateReleaseStatus(status, moderationStore.viewRelease?.id)
    } catch (e) {
        console.error(e)
    } finally {
        toast.success("Release status updated");
    }
}

</script>

<template>
    <div
        class="modal fade"
        id="releaseViewModal"
        tabindex="-1"
        aria-labelledby="releaseViewModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="releaseViewModalLabel">Release Details</h1>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column align-items-center">
                        <div
                            style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)"
                            class="d-flex w-100 pb-3 flex-row justify-content-start align-items-center w-50"
                        >
                            <img
                                :src="moderationStore.viewRelease?.cover_url"
                                style="width: 150px; height: 150px"
                                class="rounded-1 me-3"
                                alt="profile"
                            />
                            <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold fs-3">{{ moderationStore.viewRelease?.title }}</span>
                                <div class="fw-bold d-flex align-items-center">
                                    <img
                                        class="rounded-circle me-2"
                                        style="
                                                width: 30px;
                                                height: 30px;
                                                border: 1px solid rgba(228, 228, 228, 0.1);
                                            "
                                        :src="moderationStore.viewRelease?.user.profile_picture"
                                        alt="artist"
                                    >
                                    <span class="opacity-50">
                                        {{ moderationStore.viewRelease?.artist }}
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <span class="badge text-bg-light opacity-75 me-2">
                                        {{ moderationStore.viewRelease?.release_type }}
                                    </span>
                                    <span
                                        class="badge opacity-75"
                                        :class="{
                                            'text-bg-warning': moderationStore.viewRelease?.status === 'pending',
                                            'text-bg-success': moderationStore.viewRelease?.status === 'published',
                                            'text-bg-danger': moderationStore.viewRelease?.status === 'rejected',
                                        }"
                                    >
                                        {{ moderationStore.viewRelease?.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div
                            style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)"
                            class="d-flex flex-column pt-2 align-items-center w-100"
                        >
                            <div
                                class="d-flex justify-content-between pb-2 w-100"
                                style="font-size: 15px"
                            >
                                <div class="d-flex opacity-50">
                                    <img class="me-2" src="@/assets/svg/note.svg" alt="note" />
                                    Tracks:
                                </div>
                                <div class="d-flex" style="overflow: clip">
                                    {{ moderationStore.viewRelease?.tracks.length }}
                                </div>
                            </div>
                            <div
                                class="d-flex justify-content-between pb-2 w-100"
                                style="font-size: 15px"
                            >
                                <div class="d-flex opacity-50">
                                    <img
                                        class="me-2"
                                        src="@/assets/svg/calendar.svg"
                                        alt="calendar"
                                    />
                                    Release Date:
                                </div>
                                <div class="d-flex">
                                    {{ moderationStore.viewRelease?.release_date }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column pt-2 w-100 gap-1">
                            <template v-for="track in moderationStore.viewRelease?.tracks">
                                <div
                                    style="cursor: pointer !important;"
                                    class="d-flex track-row w-100 rounded-4 p-2 position-relative"
                                >
                                    <template v-if="currentTrack?.id !== track.id || !isPlaying">
                                        <span
                                            style="padding: 0 0 0 7px;"
                                            class="fw-lighter opacity-50 position-number"
                                            v-text="track.position"
                                        ></span>
                                    </template>
                                    <button
                                        style="top: 5px; left: 7px"
                                        type="button"
                                        class="btn z-3 btn-play-table position-absolute"
                                        @click="toggleTrack(
                                            track,
                                            moderationStore.viewRelease?.tracks,
                                            moderationStore.viewRelease
                                        )"
                                    >
                                        <template v-if="currentTrack?.id !== track.id">
                                            <img
                                                style="width: 30px"
                                                src="@/assets/svg/play.svg"
                                                alt="play"
                                            />
                                        </template>
                                        <template v-if="currentTrack?.id === track.id && !isPlaying">
                                            <img
                                                style="width: 30px"
                                                src="@/assets/svg/play.svg"
                                                alt="play"
                                            />
                                        </template>
                                        <template v-if="currentTrack?.id === track.id && isPlaying">
                                            <img
                                                style="width: 30px"
                                                src="@/assets/svg/pause.svg"
                                                alt="pause"
                                            />
                                        </template>
                                    </button>
                                    <div
                                        style="padding: 17px 0 0 7px"
                                        class="playing-wave"
                                        v-if="currentTrack?.id === track.id && isPlaying"
                                    >
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <span style="margin-left: 20px">{{ track.title }}</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <button
                        class="btn btn-reject w-25"
                        :disabled="moderationStore.isLoading || moderationStore.viewRelease?.status === 'rejected'"
                        @click="handleReleaseStatusUpdate('rejected')"
                    >
                        Reject
                    </button>
                    <button
                        class="btn btn-primary w-25"
                        :disabled="moderationStore.isLoading || moderationStore.viewRelease?.status === 'published'"
                        @click="handleReleaseStatusUpdate('approved')"
                    >
                        Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.modal-content {
    background: rgb(40, 40, 41);
    color: rgb(228, 228, 228);
    .modal-header {
        border-color: rgb(75, 75, 75);
    }
    .footer {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100% !important;
        border-color: rgb(75, 75, 75);
        padding: 10px;
        gap: 20px;
    }
}
.btn-reject {
    background: none !important;
    border: 1px solid rgba(189, 16, 69, 1) !important;
    border-radius: 15px !important;
    color: rgb(228,228,228) !important;
    height: 30px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 10px !important;

    &:hover {
        background-color: rgba(179, 27, 71, 0.28) !important;
    }

    &:active {
        background-color: #c11c4c !important;
        border-color: #c11c4c !important;
    }
}
.playing-wave {
    display: flex !important;
    align-items: flex-end !important;
    height: 12px !important;
    gap: 2px !important;
}

.playing-wave span {
    width: 2px !important;
    height: 4px !important;
    background: #ff2667 !important;
    animation: wave 1s infinite ease-in-out !important;
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
        height: 4px !important;
    }
    50% {
        height: 12px !important;
    }
    100% {
        height: 4px !important;
    }
}
</style>
