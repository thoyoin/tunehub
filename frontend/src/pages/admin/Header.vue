<script setup>
import { useRouter } from "vue-router";
import { useToast } from 'vue-toastification'

import { useAuthStore } from '@/stores/auth.ts'

const auth = useAuthStore()
const router = useRouter()
const toast = useToast()

const logout = async () => {
    try {
        await auth.logout()

        window.location.href = '/login'
    } catch (e) {
        toast.error('Something went wrong')
    }
}

</script>

<template>
    <header class="position-fixed w-100 px-3 z-3" style="max-height: 60px; top: 10px">
        <div
            style="
                border-bottom: 1px solid rgba(228, 228, 228, 0.15);
                border-top: 1px solid rgba(228, 228, 228, 0.15);
            "
            class="d-flex rounded-5 flex-row bg-minor py-2 w-100 justify-content-between align-items-center"
        >
            <button
                @click="router.push('/')"
                class="d-flex btn btn-home p-2 rounded-5 me-4 ms-3 align-items-center"
            >
                <img src="@/assets/svg/logo.svg" alt="logo" />
            </button>
            <div class="d-flex flex-row align-items-center">
                <div class="dropdown">
                    <a
                        class="btn btn-settings me-4 p-0"
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
                            <button @click="logout" class="dropdown-item">Leave</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped></style>
