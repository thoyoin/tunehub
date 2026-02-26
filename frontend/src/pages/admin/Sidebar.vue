<script setup>
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth.ts'
import Moderation from "@/pages/admin/moderation/Moderation.vue";

const route = useRoute()
const auth = useAuthStore()

</script>

<template>
    <div class="position-relative">
        <div
            style="
                color: rgb(228,228,228);
                max-width: 250px;
                max-height: 650px;
                margin: 20px 0 15px 0;
                flex: 0 0 auto
            "
            class="d-flex h-100 z-2 border position-fixed w-100 rounded-5 ms-3 flex-column
             bg-minor justify-content-between"
        >
            <div>
                <div
                    style="border-bottom:1px solid rgba(228, 228, 228, 0.15);"
                    class="ps-3 py-2 fw-bold d-flex flex-row align-items-center"
                >
                    <img
                        style="width: 35px;margin-right: 10px; opacity: 50%"
                        src="@/assets/svg/logo.svg"
                        alt="logo"
                    >
                    <div class="d-flex flex-column">
                        <span>TuneHub</span>
                        <span class="fw-normal" style="font-size: 13px;opacity: 50%">Admin Panel</span>
                    </div>
                </div>
                <div class="d-flex flex-row mt-1 p-2 overflow-auto position-relative">
                    <div
                        class="d-flex flex-column p-1 w-100 gap-2"
                        role="group"
                    >
                        <RouterLink
                            to="/admin/users"
                            style="height: 45px"
                            class="btn btn-playlist d-flex flex-row justify-content-start align-items-center"
                            :class="{ 'active-item': route.path.startsWith('/admin/users') }"
                        >
                            <img
                                style="margin-right: 12px"
                                src="@/assets/svg/users.svg"
                                alt="users"
                            >
                            <span>Users</span>
                        </RouterLink>
                        <RouterLink
                            to="/admin/moderation"
                            style="height: 45px"
                            class="btn btn-playlist d-flex flex-row justify-content-start align-items-center"
                            :class="{ 'active-item': route.path.startsWith('/admin/moderation') }"
                        >
                            <img
                                style="margin-right: 12px"
                                src="@/assets/svg/moderation.svg"
                                alt="users"
                            >
                            <span :class="{ 'active-item': route.path.startsWith('/admin/moderation')}">
                                Moderation
                            </span>
                        </RouterLink>
                    </div>
                </div>
            </div>
            <div
                class="d-flex flex-row align-items-center dropdown"
                style="border-top:1px solid rgba(228, 228, 228, 0.15); padding: 10px"
            >
                <img
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    style="width: 45px; height: 45px"
                    class="rounded-5"
                    :src="auth.user.profile_picture"
                    alt="profile"
                >
                <div class="d-flex flex-column">
                    <span
                        class="ms-2"
                        style="font-size: 15px"
                        v-text="auth.user.username"
                    ></span>
                    <span
                        class="ms-2 opacity-50"
                        v-text="auth.user.email"
                    >
                    </span>
                </div>
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
                            <button type="submit" class="dropdown-item">
                                Leave
                            </button>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .border {
        border:1px solid rgba(228, 228, 228, 0.15) !important;
    }
    .active-item {
        border-color: rgba(75,75,75,.3) !important;
        box-shadow: 0 0 10px 3px rgb(32,32,32) !important;
        background-color: rgb(32,32,32) !important;
        color: rgb(158, 23, 63) !important;
    }
</style>
