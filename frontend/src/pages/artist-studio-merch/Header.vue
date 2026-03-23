<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.ts'
import { useLibraryStore } from '@/stores/library.ts'
import { useToast } from "vue-toastification";

const router = useRouter()
const auth = useAuthStore()
const libraryStore = useLibraryStore()
const toast = useToast()

const logout = async () => {
    try {
        await auth.logout()

        window.location.href = '/login';
    } catch (e) {
        toast.error("Something went wrong");
    }
};

const routeHome = () => {
    libraryStore.clearAllSelectedItems()
    router.push('/')
}

const routeLogin = () => {
    router.push('login')
}
</script>

<template>
    <header class="position-fixed w-100 px-3 z-3" style="max-height: 60px; top: 10px">
        <div
            class="d-flex rounded-5 flex-row w-100 justify-content-between align-items-center"
        >
            <div
                class="bg-minor d-flex rounded-5 align-items-center justify-content-center"
                style="height: 60px; border-bottom: 1px solid rgba(228, 228, 228, 0.15);
                border-top: 1px solid rgba(228, 228, 228, 0.15);"
            >
                <button
                    @click="routeHome"
                    class="d-flex btn btn-home rounded-5 mx-3 align-items-center"
                >
                    <img src="@/assets/svg/logo.svg" alt="logo" />
                </button>
            </div>
            <div
                style="height: 60px; border-bottom: 1px solid rgba(228, 228, 228, 0.15);
                border-top: 1px solid rgba(228, 228, 228, 0.15);"
                class="bg-minor px-3 d-flex rounded-5 align-items-center justify-content-center"
            >
                <div class="dropdown">
                    <a
                        class="btn btn-settings me-2 p-0"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <img
                            :src="auth.user.profile_picture"
                            style="width: 40px; height: 40px"
                            class="rounded-5"
                            alt="account"
                        />
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <button @click="logout" class="dropdown-item">Leave</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
.btn-home {
    border: none;
    &:active {
        text-decoration-color: #9e173f !important;
    }
}

</style>
