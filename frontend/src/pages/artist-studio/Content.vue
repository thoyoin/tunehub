<script setup>
import {useArtistStore} from "@/stores/artistStudio.js";
import {onMounted} from "vue";
import {useAudioPlayer} from "@/composables/useAudioPlayer.js";

const artistStore = useArtistStore();

const {currentTrack, isPlaying, toggleTrack} = useAudioPlayer();

onMounted(async () => {
    await artistStore.fetchTracks()
    await artistStore.fetchReleases()
})

</script>

<template>
    <div>
        <div class="d-flex flex-column" style="height: 100vh">
            <div class="release-content">
                <div class="d-flex flex-row" style="flex: 0 0 auto">
                    <button
                        @click="artistStore.viewTracks"
                        class="btn btn-view fw-bold"
                        :class="{
                            'active-view': artistStore.selectedView === 'tracks'
                        }"
                        style="color: rgb(228,228,228);margin: 0 0 20px 40px; font-size: 25px; cursor: pointer"
                    >
                        All your tracks
                    </button>
                    <button
                        @click="artistStore.viewReleases"
                        class="btn btn-view fw-bold"
                        :class="{
                            'active-view': artistStore.selectedView === 'releases'
                        }"
                        style="color: rgb(228,228,228);margin: 0 0 20px 40px; font-size: 25px; cursor: pointer"
                    >
                        Releases
                    </button>
                </div>
                <template v-if="artistStore.selectedView === 'tracks'">
                    <table class="table table-borderless align-middle scrollable-table">
                        <thead style="border-bottom:1px solid rgba(228, 228, 228, 0.15)">
                        <tr>
                            <th scope="col" style="font-weight:lighter;opacity: 60%"></th>
                            <th scope="col" style="font-weight:lighter;opacity: 60%">Tracks</th>
                            <th scope="col" style="font-weight:lighter;opacity: 60%">Release</th>
                            <th scope="col" style="font-weight:lighter;opacity: 60%">Release date
                            </th>
                            <th scope="col" style="font-weight:lighter;opacity: 60%">Duration</th>
                            <th scope="col" style="font-weight:lighter;opacity: 60%"></th>
                        </tr>
                        </thead>
                        <tbody class="scrollable-tbody">
                        <template v-if="artistStore.tracks.tracks">
                            <template v-for="track in artistStore.tracks.tracks">
                                <tr class="track-row rounded-3">
                                    <td
                                        scope="row"
                                        class="position-relative"
                                        style="max-width: 30px; padding-left: 20px"
                                    >
                                        <button
                                            type="button"
                                            style="left:19px; top: 13px"
                                            class="btn z-3 btn-play-table position-absolute"
                                            @click="toggleTrack(track, artistStore.tracks.tracks)"
                                        >
                                            <template v-if="currentTrack?.id !== track.id">
                                                <img src="@/assets/svg/play.svg" alt="play">
                                            </template>
                                            <template v-if="currentTrack?.id === track.id && !isPlaying">
                                                <img src="@/assets/svg/play.svg" alt="play">
                                            </template>
                                            <template v-if="currentTrack?.id === track.id && isPlaying">
                                                <img src="@/assets/svg/pause.svg" alt="pause">
                                            </template>
                                        </button>
                                        <div
                                            style="left:30px; top: 25px"
                                            class="playing-wave position-absolute"
                                            v-if="currentTrack?.id === track.id && isPlaying"
                                        >
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </td>
                                    <td class="d-flex flex-row">
                                        <img
                                            style="width:43px;height:43px;object-fit:cover; margin-right: 10px;"
                                            :src="track.cover_url"
                                            class="rounded-1"
                                            alt="cover"
                                        >
                                        <div class="d-flex flex-column">
                                            <span
                                                class="fw-bold"
                                                style="font-size: 15px; font-weight: normal"
                                            >
                                                {{ track.title }}
                                            </span>
                                            <div class="d-flex flex-row">
                                                <span
                                                    style="opacity: 60%; font-size: 15px"
                                                >
                                                    {{ track.release?.type }}
                                                </span>
                                                <span class="mx-2"
                                                      style="opacity: 60%; font-size: 15px"
                                                >
                                                    –
                                                </span>
                                                <span
                                                    class="fw-normal"
                                                    style="opacity: 60%; font-size: 15px"
                                                >
                                                {{ track.artist }}
                                            </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-lighter">
                                        <span
                                            style="opacity: 60%; font-size: 15px">
                                            {{ track.release?.title }}
                                        </span>
                                    </td>
                                    <td class="fw-lighter">
                                        <span
                                            style="opacity: 60%; font-size: 15px">
                                            {{ track.released_in }}
                                        </span>
                                    </td>
                                    <td class="fw-lighter">
                                        <span
                                            style="opacity: 60%; font-size: 15px">
                                            {{ track.formatted_duration }}
                                        </span>
                                    </td>
                                    <td>
                                        <a
                                            class="btn btn-settings p-0"
                                            href="#"
                                            role="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                        >
                                            <img
                                                style="opacity: 60%; font-size: 15px"
                                                src="@/assets/svg/settingsWhite.svg" alt="settings"
                                            >
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editTrackModal"
                                                    type="button"
                                                    class="dropdown-item"
                                                    @click="artistStore.pullEditingItem(track)"
                                                >
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    @click="artistStore.deleteTrack(track.id)"
                                                    class="dropdown-item"
                                                >
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        </tbody>
                    </table>
                    <template v-if="artistStore.tracks.tracks?.length === 0">
                        <div
                            class="p-5 fw-bold fs-5 d-flex justify-content-center align-items-center"
                            style="color: rgb(228,228,228)"
                        >
                            You have no tracks yet.
                        </div>
                    </template>
                </template>
                <template v-if="artistStore.selectedView === 'releases'">
                    <table class="table table-borderless align-middle scrollable-table">
                        <thead style="border-bottom:1px solid rgba(228, 228, 228, 0.15)">
                        <tr>
                            <th scope="col" style="font-weight:lighter;opacity: 60%"></th>
                            <th scope="col" style="font-weight:lighter;opacity: 60%">Releases</th>
                            <th scope="col" style="font-weight:lighter;opacity: 60%">Release date
                            </th>
                            <th scope="col" style="font-weight:lighter;opacity: 60%">Status</th>
                            <th scope="col" style="font-weight:lighter;opacity: 60%"></th>
                        </tr>
                        </thead>
                        <tbody class="scrollable-tbody">
                        <template v-if="artistStore.releases.releases">
                            <template v-for="release in artistStore.releases.releases">
                                <tr class="track-row rounded-3">
                                    <td></td>
                                    <td class="d-flex flex-row">
                                        <img
                                            :src="release.cover_url"
                                            style="width:43px;height:43px;object-fit:cover; margin-right: 10px;"
                                            class="rounded-1"
                                            alt="cover"
                                        >
                                        <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-bold"
                                                        style="font-size: 15px; font-weight: normal"
                                                    >
                                                        {{ release.title }}
                                                    </span>
                                            <div class="d-flex flex-row">
                                                <span
                                                    style="opacity: 60%; font-size: 15px"
                                                >
                                                    {{ release.type }}
                                                </span>
                                                <span
                                                    class="mx-2"
                                                    style="opacity: 60%; font-size: 15px"
                                                >
                                                    –
                                                </span>
                                                <span
                                                    class="fw-normal"
                                                    style="opacity: 60%; font-size: 15px"
                                                >
                                                    {{ release.artist }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-lighter">
                                        <span
                                            style="opacity: 60%; font-size: 15px">
                                            {{ release.released_in }}
                                        </span>
                                    </td>
                                    <td class="fw-lighter">
                                        <span
                                            style="opacity: 60%; font-size: 15px">
                                            {{ release.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a
                                            class="btn btn-settings p-0"
                                            href="#"
                                            role="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                        >
                                            <img
                                                style="opacity: 60%; font-size: 15px"
                                                src="@/assets/svg/settingsWhite.svg"
                                                alt="settings"
                                            >
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editReleaseModal"
                                                    type="button"
                                                    class="dropdown-item"
                                                    @click="artistStore.pullEditingItem(release)"
                                                >
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    @click="artistStore.deleteRelease(release.id)"
                                                    class="dropdown-item"
                                                >
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        </tbody>
                    </table>
                    <template v-if="artistStore.releases.releases?.length === 0">
                        <div
                            class="p-5 fw-bold fs-5 d-flex justify-content-center align-items-center"
                            style="color: rgb(228,228,228)"
                        >
                            You have no releases yet.
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.release-content {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow-y: auto;
    padding: 100px 0 100px 0;
}

.btn-view {
    border-radius: 30px;

    &:active {
        border: solid 1px rgb(75, 75, 75) !important;
    }

    &:hover {
        border: solid 1px rgb(75, 75, 75) !important;
    }
}

.active-view {
    border-bottom: solid 1px rgb(75, 75, 75) !important;
    box-shadow: 0 0 5px 3px rgb(32, 32, 32) !important;
}

.scrollable-table {
    width: 100% !important;
    border-collapse: collapse !important;
}

.scrollable-table thead,
.scrollable-table tbody tr {
    display: table !important;
    width: 100% !important;
    table-layout: fixed !important;
}
.scrollable-tbody {
    display: block !important;
    max-height: 60vh !important;
    padding-bottom: 150px !important;
    overflow-y: auto !important;
}
</style>
