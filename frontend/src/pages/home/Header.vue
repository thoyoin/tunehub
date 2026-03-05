<script setup>
import { useAuthStore } from '@/stores/auth.ts'
import { useLibraryStore } from '@/stores/library.ts'
import { useSearchStore } from '@/stores/search.ts'
import { useReleaseStore } from '@/stores/release.ts'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

const router = useRouter()
const auth = useAuthStore()
const libraryStore = useLibraryStore()
const searchStore = useSearchStore()
const toast = useToast()

const logout = async () => {
    try {
        await auth.logout()

        window.location.href = '/login'
    } catch (e) {
        toast.error('Something went wrong')
    }
}

const routeHome = () => {
    router.push('/')
    libraryStore.clearAllSelectedItems()
}
</script>

<template>
    <header
        class="position-fixed w-100 px-3 z-3"
        style="max-height: 60px; top: 10px; flex: 0 0 auto"
    >
        <div
            style="
                border-bottom: 1px solid rgba(228, 228, 228, 0.15);
                border-top: 1px solid rgba(228, 228, 228, 0.15);
            "
            class="d-flex rounded-5 flex-row bg-minor py-2 w-100 justify-content-between align-items-center"
        >
            <button
                @click="routeHome"
                class="d-flex btn p-2 rounded-5 me-4 ms-3 align-items-center"
            >
                <img src="@/assets/svg/logo.svg" alt="logo" />
            </button>
            <div style="max-height: 46px" class="d-flex flex-row position-relative">
                <img
                    style="top: 12px; left: 15px"
                    class="position-absolute"
                    src="@/assets/svg/search.svg"
                    alt="search"
                />
                <div class="dropdown">
                    <input
                        style="
                            border: 1px solid rgba(228, 228, 228, 0.15);
                            padding-left: 40px;
                            color: rgb(228, 228, 228);
                        "
                        class="rounded w-100 form-control rounded-4 bg-minor"
                        type="text"
                        placeholder="Search..."
                        v-model="searchStore.search"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    />
                    <ul style="color: rgb(228, 228, 228);overflow: auto" class="dropdown-menu w-100 p-2">
                        <template v-if="!searchStore.hasResult && !searchStore.isLoading">
                            <div class="opacity-50 text-center">Nothing Found</div>
                        </template>
                        <template v-if="searchStore.isLoading">
                            <div class="d-flex flex-column align-items-center py-2 opacity-75">
                                <div class="search-spinner mb-1"></div>
                                <span style="font-size: 14px">Searching...</span>
                            </div>
                        </template>
                        <template
                            v-if="searchStore.result?.playlists?.length && !searchStore.isLoading"
                        >
                            <li
                                style="border-bottom: 1px solid rgba(228, 228, 228, 0.15);"
                                class="mb-1 fw-bold opacity-50"
                            >
                                Playlists
                            </li>
                            <li class="searchItem" v-for="playlist in searchStore.result?.playlists">
                                <div
                                    @click="
                                        router.push({
                                            name: 'playlist',
                                            params: { playlistId: playlist.id },
                                        })
                                    "
                                    class="d-flex flex-row align-items-center w-100"
                                >
                                    <img
                                        class="me-2 rounded-2"
                                        style="width: 40px; height: 40px"
                                        :src="playlist.cover_url"
                                        alt="cover"
                                    />
                                    <div class="d-flex flex-column justify-content-center">
                                        <span style="font-size: 15px" class="p-0 m-0 text-truncate">
                                            {{ playlist.title }}
                                        </span>
                                        <span
                                            style="font-size: 14px"
                                            class="opacity-50 text-truncate"
                                        >
                                            {{ playlist.user.username }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </template>
                        <template
                            v-if="searchStore.result?.releases?.length && !searchStore.isLoading"
                        >
                            <li
                                style="border-bottom: 1px solid rgba(228, 228, 228, 0.15);"
                                class="mb-1 fw-bold opacity-50"
                            >
                                Releases
                            </li>
                            <li class="searchItem" v-for="release in searchStore.result?.releases">
                                <div
                                    @click="
                                        router.push({
                                            name: 'release',
                                            params: { releaseId: release.id },
                                        })
                                    "
                                    class="d-flex flex-row align-items-center w-100"
                                >
                                    <img
                                        class="me-2 rounded-2"
                                        style="width: 40px; height: 40px"
                                        :src="release.cover_url"
                                        alt="cover"
                                    />
                                    <div class="d-flex flex-column justify-content-center">
                                        <span style="font-size: 15px" class="p-0 m-0 text-truncate">
                                            {{ release.title }}
                                        </span>
                                        <span
                                            style="font-size: 14px"
                                            class="opacity-50 text-truncate"
                                        >
                                            {{ release.artist }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </template>
                        <template
                            v-if="searchStore.result?.tracks?.length && !searchStore.isLoading"
                        >
                            <li
                                style="border-bottom: 1px solid rgba(228, 228, 228, 0.15);"
                                class="mb-1 fw-bold opacity-50"
                            >
                                Tracks
                            </li>
                            <li class="searchItem" v-for="track in searchStore.result?.tracks">
                                <div
                                    @click="
                                        router.push({
                                            name: 'release',
                                            params: { releaseId: track.release_id },
                                        })
                                    "
                                    class="d-flex flex-row align-items-center w-100"
                                >
                                    <img
                                        class="me-2 rounded-2"
                                        style="width: 40px; height: 40px"
                                        :src="track.cover_url"
                                        alt="cover"
                                    />
                                    <div class="d-flex flex-column justify-content-center">
                                        <span
                                            style="font-size: 15px; max-width: 170px"
                                            class="p-0 m-0 text-truncate"
                                        >
                                            {{ track.title }}
                                        </span>
                                        <span
                                            style="font-size: 14px"
                                            class="opacity-50 text-truncate"
                                        >
                                            {{ track.artist }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </template>
                        <template v-if="!searchStore.search && !searchStore.isLoading">
                            <div class="text-center opacity-75">
                                <img src="@/assets/svg/logo.svg" alt="logo">
                            </div>
                        </template>
                    </ul>
                </div>
            </div>
            <div class="d-flex flex-row align-items-center">
                <template v-if="auth.user">
                    <template v-if="auth.user?.roles[0]?.slug === 'user'">
                        <button class="btn btn-upgrade">
                            Upgrade now
                        </button>
                    </template>
                    <template v-else-if="auth.user?.roles[0]?.slug === 'premium'">
                        <button
                            @click="router.push('/artists')"
                            class="btn btn-artists d-flex rounded-5 px-2 py-0 align-items-center me-5"
                        >
                            Artist Studio
                        </button>
                    </template>
                    <template v-else>
                        <button
                            @click="router.push('/admin')"
                            class="btn btn-artists d-flex rounded-5 px-2 py-0 align-items-center me-5"
                        >
                            Admin panel
                        </button>
                    </template>
                </template>
                <template v-if="!auth.user">
                    <a
                        @click="router.push('/login')"
                        class="btn d-flex flex-row btn-primary fw-light me-4 ps-1 pe-2 py-0 my-0 align-items-center"
                    >
                        <img class="me-1" src="@/assets/svg/person.svg" alt="person" />
                        Login
                    </a>
                </template>
                <template v-if="auth.user">
                    <div class="dropdown">
                        <a
                            class="btn btn-settings me-4 p-0"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <img
                                :src="auth.user?.profile_picture"
                                style="width: 40px; height: 40px"
                                class="rounded-5 btn-play"
                                alt="account"
                            />
                        </a>
                        <form method="POST" @submit.prevent="logout">
                            <ul class="dropdown-menu">
                                <li>
                                    <button
                                        type="button"
                                        class="dropdown-item"
                                        data-bs-toggle="modal"
                                        data-bs-target="#settingsModal"
                                    >
                                        Settings
                                    </button>
                                </li>
                                <li>
                                    <button @click.prevent="logout" class="dropdown-item">
                                        Leave
                                    </button>
                                </li>
                            </ul>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </header>
</template>

<style scoped>
.form-control {
    &:focus {
        box-shadow: none;
        border-color: rgb(158, 23, 63) !important;
    }
}
.btn-upgrade {
    background-color: rgb(32, 32, 32) !important;
    padding: 0 10px;
    color: rgb(228, 228, 228);
    height: 30px !important;
    border-radius: 50px;
    display: flex;
    margin-right: 30px;
    align-items: center;

    &:hover {
        background-color: rgb(40, 40, 41) !important;
        color: rgb(158, 23, 63) !important;
    }
    &:active {
        border-color: rgb(158, 23, 63) !important;
    }
}
.searchItem {
    color: rgb(228, 228, 228) !important;
    padding: 5px !important;
    display: flex !important;
    border-radius: 10px !important;
    font-size: 15px !important;
    align-items: center !important;
    cursor: pointer !important;

    &:hover {
        background: rgb(158, 23, 63) !important;
        transition: 0.1s !important;
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
</style>
