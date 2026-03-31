<script setup lang="ts">
import { useRouter } from "vue-router";
import { useLibraryStore } from "@/stores/library";
import { useToast } from "vue-toastification";
import { useAuthStore } from "@/stores/auth";

const libraryStore = useLibraryStore();
const router = useRouter();
const toast = useToast();
const auth = useAuthStore();

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
                    class="d-flex btn btn-home rounded-5 ms-3 me-1 align-items-center"
                >
                    <img src="@/assets/svg/logo.svg" alt="logo" />
                    <span
                        style="color: rgba(228,228,228,.8)"
                        class="fw-bold px-2"
                    >
                    TuneHub
                </span>
                </button>
            </div>
            <div
                style="height: 60px; border-bottom: 1px solid rgba(228, 228, 228, 0.15);
                border-top: 1px solid rgba(228, 228, 228, 0.15);"
                class="bg-minor px-3 d-flex rounded-5 align-items-center justify-content-center"
            >
                <div class="me-3 ms-1">
                    <button
                        data-bs-target="#cartModal"
                        data-bs-toggle="modal"
                        class="btn btn-cart"
                    >
                        <img src="@/assets/svg/cart.svg" alt="cart">
                    </button>
                </div>
                <div class="dropdown">
                    <a
                        class="btn btn-settings me-2 p-0"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <img
                            :src="auth.user?.profile_picture"
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
.btn-cart {
    border: none;
    cursor: default;
    transition: .2s;

    img {
        transition: .1s;
    }

    &:hover {
        img {
            transform: scale(1.1);
        }
    }

    &:active {
        img {
            transform: scale(.95);
        }
    }
}
</style>
