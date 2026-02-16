<script setup>
    import { useAuthStore } from "@/stores/auth.js";
    import { useLibraryStore } from "@/stores/library.js";
    import {onMounted} from "vue";
    import Popover from "bootstrap/js/dist/popover";
    import { useRouter } from "vue-router";

    const auth = useAuthStore();
    const libraryStore = useLibraryStore();
    const router = useRouter();

    onMounted(() => {
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        [...popoverTriggerList].forEach(el => {
            new Popover(el);
        });
    });

    const handleItemSelection = async (item) => {
        try {
            libraryStore.selectLibraryItem(item.id)

            const isRelease = item.item_type === 'release'
            const routeName = isRelease ? 'release' : 'playlist'
            const paramKey = routeName + 'Id'

            await router.push({
                name: routeName,
                params: { [paramKey]: item.item.id }
            })
        } catch (e) {
            console.log(e)
        }
    }

</script>

<template>
    <div class="position-relative">
        <div
            style="color: rgb(228,228,228); max-width: 280px; max-height: 645px;
                margin: 90px 0 15px 0; flex: 0 0 auto"
            class="d-flex h-100 z-2 border position-fixed w-100 rounded-5 ms-3 flex-column bg-minor"
        >
            <div class="d-flex justify-content-between">
                <div class="fw-bold p-4" >
                    My library
                </div>
                <div class="p-4">
                    <template v-if="auth.user">
                        <form method="POST" @submit.prevent="libraryStore.createPlaylist()">
                            <button
                                type="submit"
                                class="btn btn-artists rounded-5 px-2 py-0"
                            >
                                <img src="@/assets/svg/add.svg" alt="add">
                            </button>
                        </form>
                    </template>
                </div>
            </div>
            <div class="d-flex flex-row mt-3 p-2 overflow-auto position-relative">
                <transition name="fade">
                    <div
                        v-if="libraryStore.isLibraryLoading"
                        class="loading-overlay d-flex align-items-center justify-content-center"
                    >
                    <span class="fw-bold fs-5 opacity-50">
                        Loading...
                    </span>
                    </div>
                </transition>
                <template v-if="auth.user">
                    <div
                        class="btn-group d-flex flex-column p-1 mb-2 w-100"
                        role="group"
                    >
                        <div v-for="libraryItem in libraryStore.items">
                            <div
                                @click="handleItemSelection(libraryItem)"
                                style="height: 58px"
                                class="d-flex align-items-center btn btn-playlist p-2 mb-2 text-start rounded-3"
                                :class="{ activeLibraryItem: libraryItem.id === libraryStore.selectedLibraryItem }"
                            >
                                <img
                                    :src="libraryItem.item?.cover_url"
                                    class="me-3 playlist-cover rounded-1"
                                    alt="libraryItem"
                                >
                                <div class="d-flex flex-column">
                                    <span v-text="libraryItem.item?.title"></span>
                                    <div class="d-flex flex-row">
                                        <span
                                            style="font-size: 13px; opacity: 50%"
                                            v-text="libraryItem.item?.type ?? libraryItem.item_type"
                                        >
                                        </span>
                                        <span style="font-size: 13px; opacity: 50%; padding: 0 5px">•</span>
                                        <template v-if="libraryItem.item.title === 'Liked tracks'">
                                            <span
                                                style="font-size: 13px; opacity: 50%"
                                                v-text="libraryItem.item.tracks.length + ' tracks'"
                                            >
                                            </span>
                                        </template>
                                        <template v-if="libraryItem.item.title !== 'Liked tracks'">
                                            <span
                                                style="font-size: 13px; opacity: 50%;max-width: 130px"
                                                v-text="libraryItem.item.artist
                                                    ?? libraryItem.user.username"
                                                class="text-truncate"
                                            >
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-if="!auth.user && !libraryStore.isLibraryLoading">
                    <div
                        class="d-flex flex-column p-3 bg-major rounded-3 justify-content-center align-items-center w-100"
                    >
                        <span class="fw-light pb-4">Create your first playlist!</span>
                        <button
                            type="button"
                            data-bs-container="body"
                            data-bs-toggle="popover"
                            data-bs-placement="right"
                            data-bs-trigger="click"
                            data-bs-html="true"
                            data-bs-custom-class="custom-popover"
                            data-bs-content="
                                      <div class='d-flex flex-column'>
                                          <div class='mb-2'>To create a playlist, log in to your account</div>
                                          <a href='/login' class='btn btn-primary px-2 py-0'>Login</a>
                                      </div>
                                  "
                            class="btn btn-add"
                        >
                            Create
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .border {
        border-bottom:1px solid rgba(228, 228, 228, 0.15) !important;
        border-top:1px solid rgba(228, 228, 228, 0.15) !important;
        border-left:1px solid rgba(228, 228, 228, 0.15) !important;
        border-right:1px solid rgba(228, 228, 228, 0.15) !important;
    }
    .loading-overlay {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        background: rgba(32, 32, 32, 0.35);
        backdrop-filter: blur(2px);
        z-index: 1;
        pointer-events: none;
    }
</style>
