<script setup>
import { useAuthStore } from '@/stores/auth.ts'
import { useLibraryStore } from '@/stores/library.ts'
import { useSearchStore } from '@/stores/search.ts'
import { useReleaseStore } from '@/stores/release.ts'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { ref } from "vue";

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
            <div class="d-flex flex-row align-items-center">
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
                                    <template v-if="auth.user?.roles[0]?.slug === 'premium'">
                                        <button
                                            type="button"
                                            class="dropdown-item"
                                            @click="
                                                router.push({
                                                name: 'artist',
                                                params: { artistId: auth.user?.id},
                                            })"
                                        >
                                            Profile
                                        </button>
                                    </template>
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
                                    <button
                                        @click.prevent="logout"
                                        class="dropdown-item"
                                    >
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
