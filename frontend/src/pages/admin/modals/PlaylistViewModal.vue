<script setup lang="ts">
import { usePlaylistsStore } from "@/stores/AdminPanel/playlists";
import { useAudioPlayer } from "@/composables/useAudioPlayer";

const playlistsStore = usePlaylistsStore();
const { currentTrack, isPlaying, toggleTrack } = useAudioPlayer()

const handlePlaylistVisionUpdate = (action: 'hide' | 'restore') => {
    if (action === 'hide') {
        playlistsStore.updateVisibility('private')
    } else {
        playlistsStore.updateVisibility('public')
    }
}

</script>

<template>
    <div
        class="modal fade"
        id="playlistViewModal"
        tabindex="-1"
        aria-labelledby="playlistViewModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="playlistViewModalLabel">Playlist Details</h1>
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
                                :src="playlistsStore.viewingPlaylist?.cover_url"
                                style="width: 150px; height: 150px"
                                class="rounded-1 me-3"
                                alt="profile"
                            />
                            <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold fs-3">{{ playlistsStore.viewingPlaylist?.title }}</span>
                                <div class="fw-bold d-flex align-items-center">
                                    <img
                                        class="rounded-circle me-2"
                                        style="
                                                width: 30px;
                                                height: 30px;
                                                border: 1px solid rgba(228, 228, 228, 0.1);
                                            "
                                        :src="playlistsStore.viewingPlaylist?.user.profile_picture"
                                        alt="artist"
                                    >
                                    <span class="opacity-50">
                                        {{ playlistsStore.viewingPlaylist?.user.username }}
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
                                    {{ playlistsStore.viewingPlaylist?.tracks.length }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column pt-2 w-100 gap-1">
                            <template v-for="track in playlistsStore.viewingPlaylist?.tracks">
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
                                            playlistsStore.viewingPlaylist?.tracks,
                                            playlistsStore.viewingPlaylist
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
                    <template v-if="playlistsStore.viewingPlaylist?.visibility === 'public'">
                        <button
                            class="btn btn-primary w-25"
                            :disabled="playlistsStore.isLoading"
                            @click="handlePlaylistVisionUpdate('hide')"
                        >
                            <img class="me-2" src="@/assets/svg/hidden.svg" alt="">
                            Hide
                        </button>
                    </template>
                    <template v-else>
                        <button
                            class="btn btn-primary w-25"
                            :disabled="playlistsStore.isLoading"
                            @click="handlePlaylistVisionUpdate('restore')"
                        >
                            <img class="me-2" src="@/assets/svg/globe.svg" alt="">
                            Restore
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
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
</style>
