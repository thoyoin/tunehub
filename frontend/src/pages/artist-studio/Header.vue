<script setup>
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.js";

const router = useRouter();
const auth = useAuthStore();

const logout = () => {
    auth.logout()
};

const routeHome = () => {
    router.push("/");
}

const routeLogin = () => {
    router.push("login");
}

</script>

<template>
    <header class="position-fixed w-100 px-3 z-3" style="max-height: 60px; top: 10px">
        <div
            style="border-bottom:1px solid rgba(228, 228, 228, 0.15);border-top:1px solid rgba(228, 228, 228, 0.15)"
            class="d-flex rounded-5 flex-row bg-minor py-2 w-100 justify-content-between align-items-center"
        >
            <button @click="routeHome" class="d-flex btn btn-home p-2 rounded-5 me-4 ms-3 align-items-center">
                <img src="@/assets/svg/logo.svg" alt="logo"/>
            </button>
            <template v-if="auth.user">
                <div class="d-flex flex-row">
                    <button
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#uploadModal"
                        class="btn btn-artists rounded-5 d-flex align-items-center px-2 py-1 me-5"
                    >
                        <img src="@/assets/svg/upload.svg" alt="">
                        <span class="ps-2">Upload</span>
                    </button>
                    <div class="dropdown">
                        <a class="btn btn-settings me-4 p-0" href="#" role="button" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            <img
                                :src="auth.user.profile_picture"
                                style="width: 40px; height: 40px;"
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
            </template>
            <template v-else>
                <button @click="routeLogin" class="btn d-flex flex-row btn-primary fw-light me-4 ps-1 pe-2 py-0 align-items-center">
                    <img class="me-1" src="@/assets/svg/person.svg" alt="person">
                    Login
                </button>
            </template>
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
