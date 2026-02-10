<script setup>
    import { useAuthStore } from '@/stores/auth'
    import { useLibraryStore} from "@/stores/library.js";
    import { useReleaseStore } from "@/stores/release.js";
    import { useRouter } from 'vue-router';

    const router = useRouter();
    const auth = useAuthStore();
    const libraryStore = useLibraryStore();

    const logout = () => {
        auth.logout()
    };

</script>

<template>
    <header
        class="position-fixed w-100 px-3 z-3"
        style="max-height: 60px; top: 10px; flex: 0 0 auto"
    >
        <div
            style="border-bottom:1px solid rgba(228, 228, 228, 0.15);border-top: 1px solid rgba(228, 228, 228, 0.15)"
            class="d-flex rounded-5 flex-row bg-minor py-2 w-100 justify-content-between align-items-center"
        >
            <button
                @click="libraryStore.clearAllSelectedItems"
                class="d-flex btn p-2 rounded-5 me-4 ms-3 align-items-center"
            >
                <img src="@/assets/svg/logo.svg" alt="logo">
            </button>
            <div style="max-height: 46px" class="d-flex flex-row position-relative">
                <img style="top: 12px; left: 15px" class="position-absolute" src="@/assets/svg/search.svg" alt="search"/>
                <input
                    style="border: 1px solid rgba(228, 228, 228, 0.15); padding-left: 40px;color: rgb(228,228,228);"
                    class="rounded w-100 form-control rounded-4 bg-minor"
                    type="text"
                    placeholder="Search..."
                />
            </div>
            <div class="d-flex flex-row">
                <template v-if="auth.user">
                    <template v-if="auth.user?.role === '1'">
                        <button class="btn btn-upgrade rounded-5 px-2 py-2 me-5">Upgrade now
                        </button>
                    </template>
                    <template v-else>
                        <button
                            @click="router.push('artists')"
                            class="btn btn-artists d-flex rounded-5 px-2 py-0 align-items-center me-5"
                        >
                            Artist Studio
                        </button>
                    </template>
                </template>
                <template v-if="!auth.user">
                    <a
                        @click="router.push('login')"
                        class="btn d-flex flex-row btn-primary fw-light me-4 ps-1 pe-2 py-0 my-0 align-items-center"
                    >
                        <img class="me-1" src="@/assets/svg/person.svg" alt="person">
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
                            aria-expanded="false">
                            <img
                                :src="auth.user?.profile_picture"
                                style="width: 40px; height: 40px;"
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
            border-color: #ff2667 !important;
        }
    }
    .btn-upgrade {
        background-color: rgb(32,32,32) !important;
        padding: 0 5px 3px;
        color: rgb(228,228,228);
        &:hover {
            background-color: rgb(40,40,41) !important;
            color: #ff2667 !important;
        }
        &:active {
            border-color: #ff2667 !important;
        }
    }
</style>
