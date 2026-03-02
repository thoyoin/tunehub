<script setup lang="ts">
import { usePlaylistsStore } from "@/stores/AdminPanel/playlists";
import { onMounted } from "vue";

const playlistsStore = usePlaylistsStore();

const currentPage: number = 1;

onMounted( async () => {
    await playlistsStore.fetchPlaylists();
})

const fetchPage = async (page: number) => {
    currentPage.value = page;

    await playlistsStore.fetchPlaylists(page);
};

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
        <div class="fs-3 fw-bold">
            Playlists
        </div>
        <div class="mt-4" style="max-width: 250px">
            <div class="stat-card bg-minor d-flex flex-column">
                <div class="d-flex align-items-center">
                    <img class="me-2" src="@/assets/svg/playlistsMenu.svg" alt="playlists">
                    <span class="opacity-50">Total playlists</span>
                </div>
                <template v-if="playlistsStore.isLoading">
                    <div class="search-spinner mt-2 ms-1"></div>
                </template>
                <template v-else>
                    <span class="fs-4 mt-2" v-text="playlistsStore.playlists?.total"></span>
                </template>
            </div>
        </div>
        <div
            role="group"
            class="btn-group d-flex flex-row mt-4 w-50"
        >
            <button
                @click="playlistsStore.selectView('all')"
                style="border-bottom-left-radius: 15px;border-top-left-radius: 15px;"
                class="btn btn-view d-flex align-items-center"
                :class="{ 'activeView': playlistsStore.selectedView === 'all' }"
            >
                <img class="me-2" src="@/assets/svg/playlistsMenuWhite.svg" alt="clock">
                <span>All</span>
            </button>
            <button
                @click="playlistsStore.selectView('hidden')"
                class="btn btn-view d-flex align-items-center"
                style="border-bottom-right-radius: 15px;border-top-right-radius: 15px;"
                :class="{ 'activeView': playlistsStore.selectedView === 'hidden' }"
            >
                <img class="me-2" src="@/assets/svg/hidden.svg" alt="clock">
                Hidden
            </button>
        </div>
        <div class="position-relative">
            <transition name="fade">
                <div
                    v-if="playlistsStore.isLoading"
                    class="loading-overlay d-flex flex-column align-items-center justify-content-center"
                >
                    <div class="search-spinner mb-2"></div>
                </div>
            </transition>
            <table class="table table-borderless align-middle" style="padding: 25px 0 0 295px">
                <thead style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                <tr>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Playlist</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Author</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Tracks</th>
                    <th scope="col" style="font-weight: lighter; opacity: 60%">Visibility</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="playlist in playlistsStore.playlists?.data" :key="playlist.id">
                    <tr
                        data-bs-toggle="modal"
                        data-bs-target="#playlistViewModal"
                        @click="playlistsStore.setViewingPlaylist(playlist)"
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
                                    :src="playlist.cover_url"
                                    alt="cover"
                                />
                                <span>{{ playlist.title }}</span>
                            </div>
                        </td>
                        <td style="font-size: 15px">
                            <div class="d-flex flex-row align-items-center">
                                <img
                                    class="rounded-circle me-2"
                                    style="
                                    width: 35px;
                                    height: 35px;
                                    border: 1px solid rgba(228, 228, 228, 0.1);
                                "
                                    :src="playlist.user.profile_picture"
                                    alt="artist"
                                />
                                <span class="opacity-50">{{ playlist.user.username }}</span>
                            </div>
                        </td>
                        <td style="font-size: 15px">
                            {{ playlist.tracks.length }}
                        </td>
                        <td>
                            public
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
            <template v-if="playlistsStore.playlists?.data?.length !== 0">
                <div class="opacity-50 w-100">
            <span>
                Showing {{ playlistsStore.playlists?.from }}-{{ playlistsStore.playlists?.to }} of
                {{ playlistsStore.playlists?.total }}
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
                    :disabled="currentPage === playlistsStore.playlists?.last_page"
                >
                    <img src="@/assets/svg/arrowRight.svg" alt="next" />
                </button>
            </div>
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
    transition: .4s;
    backdrop-filter: blur(1px);
    z-index: 1;
    pointer-events: none;
}
.btn-pagination {
    border: 1px solid rgba(179, 27, 71,.5) !important;
    border-radius: 15px !important;
    color: rgb(228,228,228) !important;
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
.btn-view {
    background-color: rgba(50,50,51, 15%) !important;
    border: 1px solid rgba(50,50,51, 1) !important;
    color: rgb(228,228,228) !important;
    height: 35px !important;
    max-width: 100px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

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
