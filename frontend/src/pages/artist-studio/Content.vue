<script setup>
import { useArtistStore } from '@/stores/artistStudio'
import { useArtistMerchStore } from "@/stores/artistMerch.ts";
import { onMounted } from 'vue'
import { useAudioPlayer } from '@/composables/useAudioPlayer'
import { useToast } from "vue-toastification";
import { useRouter } from "vue-router";
import ArtistEarnings from "@/pages/artist-studio/charts/ArtistEarnings.vue";
import ArtistStreamsCharts from "@/pages/artist-studio/charts/ArtistStreamsCharts.vue";

const artistStore = useArtistStore()
const merchStore = useArtistMerchStore()
const toast = useToast()
const router = useRouter()

const { currentTrack, isPlaying, toggleTrack } = useAudioPlayer()

onMounted(async () => {
    await Promise.all([
        artistStore.fetchTracks(),
        artistStore.fetchReleases(),
        artistStore.fetchArtistStreamsTotal(),
        artistStore.fetchArtistEarnings(),
        artistStore.fetchArtistStreamsDaily(),
        artistStore.fetchArtistTopTracks(),
        artistStore.fetchArtistTopReleases(),
        merchStore.fetchArtistMerch(),
    ])
})

const handleReleasePublication = async (id) => {
    try {
        await artistStore.publishRelease(id)

        toast.success('Release has been published successfully.')
    } catch (error) {
        console.log(error)

        toast.error('Something went wrong.')
    }
}

</script>

<template>
    <div class="flex-grow-1 release-content position-relative">
        <div class="d-flex flex-column">
            <div
                style="margin: 100px 150px 0 150px;padding: 20px 20px 0 20px; color: rgb(228,228,228);
                border: 1px solid rgba(228, 228, 228, 0.15);"
                class="rounded-5"
            >
                <transition name="fade">
                    <div
                        v-if="artistStore.isLoading.library"
                        style="z-index: 9999 !important;"
                        class="loading-overlay d-flex flex-column align-items-center justify-content-center"
                    >
                        <div class="search-spinner mb-2"></div>
                    </div>
                </transition>
                <div class="d-flex flex-row align-items-end">
                    <span class="fw-bold fs-5 me-3">Artist Studio</span>
                    <span style="font-size: 13px;line-height: 25px" class="fw-bold opacity-50">All time stats</span>
                </div>
                <div class="d-flex flex-row align-items-end mt-2 w-100">
                    <div
                        class="d-flex flex-row me-5 position-relative"
                        style="border-right:1px solid rgba(228, 228, 228, 0.15);padding-bottom: 20px"
                    >
                        <transition name="fade">
                            <div
                                v-if="artistStore.isLoading.streamsTotal"
                                class="loading-overlay d-flex flex-column align-items-center justify-content-center"
                            >
                                <div class="search-spinner mb-2"></div>
                            </div>
                        </transition>
                        <div class="d-flex flex-column mx-5 align-items-center">
                            <span class="fs-3 fw-bold">
                                {{ artistStore.artistStreamsTotal }}
                            </span>
                            <span class="opacity-50 fw-bold">plays</span>
                        </div>
                        <img class="opacity-50" src="@/assets/svg/line.svg" alt="">
                        <div class="d-flex flex-column mx-5 align-items-center">
                            <span class="fs-3 fw-bold">
                                {{ artistStore.tracks.length }}
                            </span>
                            <span class="opacity-50 fw-bold">tracks</span>
                        </div>
                        <img class="opacity-50" src="@/assets/svg/line.svg" alt="">
                        <div class="d-flex flex-column mx-5 align-items-center">
                            <span class="fs-3 fw-bold">
                                {{ artistStore.releases.length }}
                            </span>
                            <span class="opacity-50 fw-bold">releases</span>
                        </div>
                    </div>
                    <div
                        style="padding-bottom: 20px"
                        class="d-flex flex-column me-5 align-items-center gap-1"
                    >
                        <button
                            @click="artistStore.viewTracks()"
                            class="btn btn-earnings"
                            :class="{
                                'active-menu': artistStore.selectedView === 'tracks'
                                || artistStore.selectedView === 'releases'
                            }"
                        >
                            <img style="width: 30px" src="@/assets/svg/library.svg" alt="library">
                        </button>
                        <span
                            style="
                            line-height: 22px;
                            transition: 0.2s ease-in-out;
                            "
                            class="fw-bold opacity-50"
                            :class="{
                                'active-menu': artistStore.selectedView === 'tracks'
                                || artistStore.selectedView === 'releases'
                            }"
                        >
                            Library
                        </span>
                    </div>
                    <div
                        style="padding-bottom: 20px"
                        class="d-flex flex-column me-5 align-items-center gap-1"
                    >
                        <button
                            @click="artistStore.viewEarnings()"
                            class="btn btn-earnings"
                            :class="{
                                'active-menu': artistStore.selectedView === 'earnings',
                            }"
                        >
                            <img style="width: 30px" src="@/assets/svg/earnings.svg" alt="money">
                        </button>
                        <span
                            style="
                            line-height: 22px;
                            transition: 0.2s ease-in-out;
                            "
                            class="fw-bold opacity-50"
                            :class="{
                                'active-menu': artistStore.selectedView === 'earnings',
                            }"
                        >
                            Earnings
                        </span>
                    </div>
                    <div
                        style="padding-bottom: 20px"
                        class="d-flex flex-column me-5 align-items-center gap-1"
                    >
                        <button
                            @click="artistStore.viewAnalytics()"
                            class="btn btn-earnings"
                            :class="{
                                'active-menu': artistStore.selectedView === 'analytics',
                            }"
                        >
                            <img style="width: 30px" src="@/assets/svg/analytics.svg" alt="analytics">
                        </button>
                        <span
                            style="
                            line-height: 22px;
                            transition: 0.2s ease-in-out;
                            "
                            class="fw-bold opacity-50"
                            :class="{
                                'active-menu': artistStore.selectedView === 'analytics',
                            }"
                        >
                            Analytics
                        </span>
                    </div>
                    <div
                        style="padding-bottom: 20px"
                        class="d-flex flex-grow-1 flex-column me-5 align-items-end gap-1"
                    >
                        <button
                            @click="artistStore.viewMerch()"
                            class="btn btn-earnings"
                            :class="{
                                'active-menu': artistStore.selectedView === 'merch',
                            }"
                        >
                            <img style="width: 30px" src="@/assets/svg/bag.svg" alt="bag">
                        </button>
                        <span
                            style="
                            line-height: 22px;
                            transition: 0.2s ease-in-out;
                            "
                            class="fw-bold opacity-50"
                            :class="{
                                'active-menu': artistStore.selectedView === 'merch',
                            }"
                        >
                            Merch
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column position-relative">
                <template v-if="artistStore.selectedView === 'tracks' || artistStore.selectedView === 'releases'">
                    <div
                        class="d-flex flex-row z-3 position-sticky"
                        style="margin: 65px 0 0 115px;top: 80px;width: 390px"
                    >
                        <button
                            @click="artistStore.viewTracks"
                            class="btn btn-view fw-bold"
                            :class="{
                            'active-view': artistStore.selectedView === 'tracks',
                        }"
                        >
                            All My Tracks
                        </button>
                        <button
                            @click="artistStore.viewReleases"
                            class="btn btn-view fw-bold"
                            :class="{
                            'active-view': artistStore.selectedView === 'releases',
                        }"
                            style="
                            margin: 0 0 20px 40px;
                        "
                        >
                            Releases
                        </button>
                    </div>
                </template>
                <template v-if="artistStore.selectedView === 'tracks'">
                    <div class="allItems" style="margin-top: 20px">
                        <table class="table table-borderless align-middle">
                            <thead style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                                <tr>
                                    <th scope="col" style="font-weight: lighter; opacity: 60%"></th>
                                    <th scope="col" style="font-weight: lighter">
                                        <span style="opacity: 60%">Tracks</span>
                                    </th>
                                    <th scope="col" style="font-weight: lighter">
                                        <span style="opacity: 60%">Release</span>
                                    </th>
                                    <th scope="col" style="font-weight: lighter">
                                        <span style="opacity: 60%">Release Date</span>
                                    </th>
                                    <th scope="col" style="font-weight: lighter">
                                        <span style="opacity: 60%">Duration</span>
                                    </th>
                                    <th scope="col" style="font-weight: lighter">
                                        <span style="opacity: 60%">Plays</span>
                                    </th>
                                    <th scope="col" style="font-weight: lighter"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="artistStore.tracks">
                                    <template v-for="track in artistStore.tracks">
                                        <tr class="track-row rounded-3">
                                            <td
                                                scope="row"
                                                class="position-relative"
                                                style="max-width: 30px; padding-left: 20px"
                                            >
                                                <button
                                                    type="button"
                                                    style="left: 19px; top: 13px"
                                                    class="btn btn-play-table z-1 position-absolute"
                                                    @click="
                                                        toggleTrack(
                                                            track,
                                                            artistStore.tracks,
                                                            track.release,
                                                        )
                                                    "
                                                >
                                                    <template v-if="currentTrack?.id !== track.id">
                                                        <img
                                                            src="@/assets/svg/play.svg"
                                                            alt="play"
                                                        />
                                                    </template>
                                                    <template
                                                        v-if="
                                                            currentTrack?.id === track.id &&
                                                            !isPlaying
                                                        "
                                                    >
                                                        <img
                                                            src="@/assets/svg/play.svg"
                                                            alt="play"
                                                        />
                                                    </template>
                                                    <template
                                                        v-if="
                                                            currentTrack?.id === track.id &&
                                                            isPlaying
                                                        "
                                                    >
                                                        <img
                                                            src="@/assets/svg/pause.svg"
                                                            alt="pause"
                                                        />
                                                    </template>
                                                </button>
                                                <div
                                                    style="left: 30px; top: 25px"
                                                    class="playing-wave position-absolute"
                                                    v-if="
                                                        currentTrack?.id === track.id && isPlaying
                                                    "
                                                >
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </td>
                                            <td class="d-flex flex-row">
                                                <img
                                                    style="
                                                        width: 43px;
                                                        height: 43px;
                                                        object-fit: cover;
                                                        margin-right: 10px;
                                                    "
                                                    :src="track.cover_url"
                                                    class="rounded-1"
                                                    alt="cover"
                                                />
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-bold"
                                                        style="font-size: 15px; font-weight: normal"
                                                    >
                                                        {{ track.title }}
                                                    </span>
                                                    <div class="d-flex flex-row">
                                                        <span style="opacity: 60%; font-size: 15px">
                                                            {{ track.release?.release_type }}
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
                                                            {{ track.artist }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-lighter">
                                                <span style="opacity: 60%; font-size: 15px">
                                                    {{ track.release?.title }}
                                                </span>
                                            </td>
                                            <td class="fw-lighter">
                                                <span style="opacity: 60%; font-size: 15px">
                                                    {{ track.released_in }}
                                                </span>
                                            </td>
                                            <td class="fw-lighter">
                                                <span style="opacity: 60%; font-size: 15px">
                                                    {{ track.formatted_duration }}
                                                </span>
                                            </td>
                                            <td class="fw-lighter">
                                                <span
                                                    class="d-flex align-items-center justify-content-start"
                                                    style="opacity: 60%; font-size: 15px"
                                                >
                                                <img
                                                    style="width: 15px"
                                                    src="@/assets/svg/playWhite.svg"
                                                    alt="plays"
                                                >
                                                    {{ track.plays }}
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
                                                    />
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editTrackModal"
                                                            type="button"
                                                            class="dropdown-item"
                                                            @click="
                                                                artistStore.pullEditingItem(track)
                                                            "
                                                        >
                                                            Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button
                                                            @click="
                                                                artistStore.deleteTrack(track.id)
                                                            "
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
                    <template v-if="artistStore.tracks?.length === 0">
                        <div
                            class="p-5 fw-bold fs-5 d-flex justify-content-center align-items-center"
                            style="color: rgb(228, 228, 228); opacity: .8"
                        >
                            You have no tracks yet.
                        </div>
                    </template>
                    </div>
                </template>
                <template v-if="artistStore.selectedView === 'releases'">
                    <div class="allItems" style="margin-top: 20px">
                        <table class="table table-borderless align-middle">
                            <thead style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                                <tr>
                                    <th scope="col" style="font-weight: lighter; opacity: 60%"></th>
                                    <th scope="col" style="font-weight: lighter; opacity: 60%">Releases</th>
                                    <th scope="col" style="font-weight: lighter; opacity: 60%">Release date</th>
                                    <th scope="col" style="font-weight: lighter; opacity: 60%">Tracks</th>
                                    <th scope="col" style="font-weight: lighter; opacity: 60%">Duration</th>
                                    <th scope="col" style="font-weight: lighter; opacity: 60%">Status</th>
                                    <th scope="col" style="font-weight: lighter; opacity: 60%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="artistStore.releases">
                                    <template v-for="release in artistStore.releases">
                                        <tr class="track-row rounded-3">
                                            <td></td>
                                            <td class="d-flex flex-row">
                                                <img
                                                    :src="release.cover_url"
                                                    style="
                                                        width: 43px;
                                                        height: 43px;
                                                        object-fit: cover;
                                                        margin-right: 10px;
                                                    "
                                                    class="rounded-1"
                                                    alt="cover"
                                                />
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-bold"
                                                        style="font-size: 15px; font-weight: normal"
                                                    >
                                                        {{ release.title }}
                                                    </span>
                                                    <div class="d-flex flex-row">
                                                        <span style="opacity: 60%; font-size: 15px">
                                                            {{ release.release_type }}
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
                                                <span style="opacity: 60%; font-size: 15px">
                                                    {{ release.released_in }}
                                                </span>
                                            </td>
                                            <td class="fw-lighter">
                                                <span style="opacity: 60%; font-size: 15px">
                                                    {{ release.tracks.length }}
                                                </span>
                                            </td>
                                            <td class="fw-lighter">
                                                <span style="opacity: 60%; font-size: 15px">
                                                    {{ release.release_duration }}
                                                </span>
                                            </td>
                                            <td class="fw-lighter">
                                                <span style="font-size: 15px">
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
                                                    />
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editReleaseModal"
                                                            type="button"
                                                            class="dropdown-item"
                                                            @click="
                                                                artistStore.pullEditingItem(release)
                                                            "
                                                        >
                                                            Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button
                                                            type="button"
                                                            class="dropdown-item"
                                                            :disabled="release.status !== 'approved'
                                                                || artistStore.isLoading"
                                                            :class="{ 'opacity-50': release.status !== 'approved' }"
                                                            @click="
                                                                handleReleasePublication(release.id)
                                                            "
                                                        >
                                                            Publish
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button
                                                            @click="
                                                                artistStore.deleteRelease(
                                                                    release.id,
                                                                )
                                                            "
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
                    <template v-if="artistStore.releases?.length === 0">
                        <div
                            class="p-5 fw-bold fs-5 d-flex justify-content-center align-items-center"
                            style="color: rgb(228, 228, 228); opacity: .8"
                        >
                            You have no releases yet.
                        </div>
                    </template>
                    </div>
                </template>
                <template v-if="artistStore.selectedView === 'earnings'">
                    <div class="d-flex mt-5 border mx-5 p-3 flex-column align-items-center justify-content-center">
                        <div style="color: rgb(228, 228, 228)">
                            <span class="ms-4 mt-4 fw-bold fs-5">
                                Total Balance: ${{ artistStore.artistBalance / 100 }}
                            </span>
                        </div>
                        <ArtistEarnings/>
                    </div>
                </template>
                <template v-if="artistStore.selectedView === 'analytics'">
                    <div class="mt-5 border p-3 mx-5 d-flex flex-column align-items-center justify-content-center">
                        <div style="color: rgb(228,228,228)">
                            <span class="ms-4 mt-4 fw-bold fs-5">
                                Streams per Day
                            </span>
                        </div>
                        <ArtistStreamsCharts/>
                    </div>
                    <div class="d-flex flex-row">
                        <div
                            style="max-width: 700px;height: 300px"
                            class="mt-5 border position-relative px-3 w-100 mx-5 d-flex flex-column
                            align-items-center justify-content-start"
                        >
                            <div
                                style="color: rgb(228,228,228);height: 70px"
                                class="d-flex bg-title position-absolute flex-column align-items-center
                                justify-content-start w-100 rounded-4"
                            >
                                <span class="ms-4 mt-2 fw-bold fs-5">
                                    Top Tracks
                                </span>
                                <span class="ms-4 fw-bold opacity-50" style="line-height: 5px">
                                    last 30 days
                                </span>
                            </div>
                            <div
                                style="color: rgb(228,228,228); padding-top: 60px"
                                class="w-100 overflow-y-auto pb-3"
                            >
                            <template v-for="track in artistStore.artistTopTracks">
                                <div
                                    @click="
                                        router.push({
                                            name: 'release',
                                            params: { ['releaseId']: track.track.release_id }
                                    })"
                                    style="margin-top: 10px;"
                                    class="border artist-row d-flex flex-row
                                    justify-content-between align-items-center p-2
                                    "
                                >
                                    <div class="d-flex flex-row align-items-center">
                                        <img
                                            style="width: 50px;height: 50px"
                                            :src="track.track.cover_url"
                                            alt="cover"
                                            class="me-3 rounded-2"
                                        >
                                        <span class="fw-bold">
                                            {{ track.track.title }}
                                        </span>
                                    </div>
                                    <span class="fw-bold opacity-50 me-3">
                                        {{ track.streams }} plays
                                    </span>
                                </div>
                            </template>
                            </div>
                        </div>
                        <div
                            style="max-width: 700px;height: 300px"
                            class="mt-5 border position-relative px-3 w-100 mx-5 d-flex flex-column
                            align-items-center justify-content-start"
                        >
                            <div
                                style="color: rgb(228,228,228);height: 70px"
                                class="d-flex bg-title position-absolute flex-column align-items-center
                                justify-content-start w-100 rounded-4"
                            >
                                <span class="ms-4 mt-2 fw-bold fs-5">
                                    Top Releases
                                </span>
                                <span class="ms-4 fw-bold opacity-50" style="line-height: 5px">
                                    last 30 days
                                </span>
                            </div>
                            <div
                                style="color: rgb(228,228,228); padding-top: 60px"
                                class="w-100 overflow-y-auto pb-3"
                            >
                                <template v-for="release in artistStore.artistTopReleases">
                                    <div
                                        @click="
                                            router.push({
                                                name: 'release',
                                                params: { ['releaseId']: release.id }
                                        })"
                                        style="margin-top: 10px;"
                                        class="border artist-row d-flex flex-row
                                    justify-content-between align-items-center p-2
                                    "
                                    >
                                        <div class="d-flex flex-row align-items-center">
                                            <img
                                                style="width: 50px;height: 50px"
                                                :src="release.cover_url"
                                                alt="cover"
                                                class="me-3 rounded-2"
                                            >
                                            <span class="fw-bold">
                                            {{ release.title }}
                                        </span>
                                        </div>
                                        <span class="fw-bold opacity-50 me-3">
                                        {{ release.plays }} plays
                                    </span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-if="artistStore.selectedView === 'merch'">
                    <div class="mt-5 p-3 mx-5 d-flex flex-column align-items-center justify-content-center">
                        <div style="color: rgb(228,228,228)" class="d-flex flex-column align-items-center">
                            <span class=" fw-bold fs-5">
                                Your Products
                            </span>
                            <div class="mt-3">
                                <template v-if="merchStore.artistMerch.length !== 0">
                                    <div class="d-flex flex-row flex-wrap gap-3 w-100">
                                        <template v-for="product in merchStore.artistMerch">
                                            <div
                                                @click="merchStore.setEditingMerch(product)"
                                                class="border rounded-5 p-3 position-relative"
                                                data-bs-target="#editMerchModal"
                                                data-bs-toggle="modal"
                                            >
                                                <img
                                                    class="rounded-4 mb-2"
                                                    width="200px"
                                                    height="200px"
                                                    :src="product.cover_url"
                                                    alt="cover"
                                                >
                                                <span
                                                    class="badge position-absolute"
                                                    style="
                                                            color: black;
                                                            border-bottom-left-radius:100px;
                                                            border-bottom-right-radius:100px;
                                                            background-color: rgba(228,228,228, .8);
                                                            opacity: .5;
                                                            padding: 1px 7px 7px;
                                                            top: 0;
                                                            left:36%;
                                                        "
                                                >
                                                    {{ product.status }}
                                                </span>
                                                <div
                                                    style="border-top:1px solid rgba(228, 228, 228, 0.15)"
                                                    class="d-flex flex-column align-items-center mt-2 pt-2"
                                                >
                                                    <span class="fw-bold fs-5">{{ product.title }}</span>
                                                    <div
                                                        class="d-flex flex-column align-items-start w-100"
                                                    >
                                                        <table class="table table-borderless align-middle">
                                                            <thead>
                                                                <tr>
                                                                    <th class="col p-0 ps-1"></th>
                                                                    <th
                                                                        class="col p-0 ps-1"
                                                                        style="font-weight: lighter;
                                                                        opacity: 60%;font-size: 15px"
                                                                    >
                                                                        Stock
                                                                    </th>
                                                                    <th
                                                                        class="col p-0 ps-2"
                                                                        style="font-weight: lighter;
                                                                        opacity: 60%;font-size: 15px"
                                                                    >
                                                                        Price
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <template v-for="(variant, index) in product.product_variants">
                                                                    <tr>
                                                                        <td style="font-size: 15px" class="p-0 fw-light">
                                                                            <span
                                                                                style="font-size: 13px"
                                                                                class="opacity-50 me-2"
                                                                            >
                                                                                {{ index + 1 }}
                                                                            </span>
                                                                            <span
                                                                                class="opacity-50"
                                                                            >
                                                                                {{ variant.variant_name }}
                                                                            </span>
                                                                        </td>
                                                                        <td style="font-size: 15px" class="p-0 fw-light">
                                                                            <span
                                                                                class="opacity-50 ms-1"
                                                                            >
                                                                                {{ variant.stock }}
                                                                            </span>
                                                                        </td>
                                                                        <td style="font-size: 15px" class="p-0 fw-light">
                                                                            <div class="opacity-50 ms-2 me-1">
                                                                                ${{ variant.price }}
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </template>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-4 w-100 d-flex align-items-center justify-content-center">
                                        <button
                                            @click="router.push('/artists/merch/upload')"
                                            class="btn btn-artists d-flex w-50 justify-content-center"
                                        >
                                            Upload Merch
                                        </button>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="fw-bold d-flex flex-column align-items-center p-5">
                                        <span class="opacity-50 my-5">Nothing...</span>
                                        <span class="">Put your first product up for sale!</span>
                                        <button
                                            @click="router.push('/artists/merch/upload')"
                                            class="btn btn-primary mt-3 fw-bold"
                                            style="color: rgb(228,228,228)"
                                        >
                                            Get started
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.loading-overlay {
    position: absolute;
    inset: 0;
    background: rgba(32, 32, 32, 0.5);
    backdrop-filter: blur(4px);
    border-radius: 18px;
    z-index: 10;
    pointer-events: all;
    display: flex;
    align-items: center;
    justify-content: center;
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
    border-radius: 30px;
    height: 50px;
    background-color: rgba(32,32,32, .5);
    backdrop-filter: blur(4px);
    color: rgb(228, 228, 228) !important;
    font-size: 25px;
    cursor: pointer;
    opacity: .7;
    transition: .3s all ease;

    &:active {
        border: solid 1px rgb(75, 75, 75) !important;
    }

    &:hover {
        border: solid 1px rgb(75, 75, 75) !important;
        opacity: 1;
    }
}

.active-view {
    border-bottom: solid 1px rgb(75, 75, 75) !important;
    box-shadow: 0 0 5px 3px rgb(32, 32, 32) !important;
    opacity: 1 !important;
}

.active-menu {
    opacity: 1 !important;
}

.scrollable-table thead,
.scrollable-table tbody tr {
    display: table !important;
    width: 100% !important;
    table-layout: fixed !important;
}

.allItems {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 0 0 90px 0 !important;
    min-height: 0 !important;
}

.release-content::-webkit-scrollbar {
    height: 5px !important;
    width: 5px !important;
}

.release-content::-webkit-scrollbar-thumb {
    background: rgba(228, 228, 228, 0.15) !important;
    border-radius: 10px !important;
    transition: 0.2s !important;
}

.btn-earnings {
    cursor: default;
    transition: transform 0.2s ease-in-out, width 0.1s ease-in-out;
    border: none;

    img {
        transition: .1s ease-in-out !important;
    }

    &:hover {
        transform: scale(1.1);
    }

    &:active {
        img {
            opacity: 0.7;
        }
        transform: scale(1);
    }
}

.border {
    border:1px solid rgba(228, 228, 228, 0.15) !important;
    border-radius: 18px;
}

.artist-row {
    transition: .2s;
    cursor: default;

    &:hover {
        background-color: rgba(228, 228, 228, 0.05) !important;
    }
}

.bg-title {
    background: linear-gradient(
        to bottom,
        rgba(32, 32, 32, 1) 0%,
        rgba(32, 32, 32, .8) 40%,
        rgba(32, 32, 32, 0) 100%
    ) !important;
    backdrop-filter: blur(3px) !important;
    mask-image: linear-gradient(black 60%, transparent 100%);
    z-index: 10 !important;
}

.allItems::-webkit-scrollbar,
.overflow-y-auto::-webkit-scrollbar {
    display: none;
}

.allItems,
.overflow-y-auto {
    scrollbar-width: none;
}

.allItems,
.overflow-y-auto {
    -ms-overflow-style: none;
}

</style>
