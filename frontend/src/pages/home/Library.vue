<script setup>
    import { useAuthStore } from "@/stores/auth.js";
    import { useLibraryStore } from "@/stores/library.js";
    import {onMounted} from "vue";
    import Popover from "bootstrap/js/dist/popover";

    const auth = useAuthStore();
    const libraryStore = useLibraryStore();

    onMounted(() => {
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        [...popoverTriggerList].forEach(el => {
            new Popover(el);
        });
    });

</script>

<template>
    <div class="position-relative">
        <div
            style="color: rgb(228,228,228); max-width: 280px; max-height: 645px;
                margin: 90px 0 15px 0; flex: 0 0 auto"
            class="d-flex h-100 z-1 border position-fixed w-100 rounded-5 ms-3 flex-column bg-minor"
        >
            <div class="d-flex justify-content-between">
                <div class="fw-bold p-4" >
                    My library
                </div>
                <div class="p-4">
                    <template v-if="auth.user">
                        <form method="POST" @submit.prevent="libraryStore.createPlaylist()">
                            <button @click.prevent="libraryStore.createPlaylist()"
                                    class="btn btn-add bg-major rounded-5 px-2 py-0">
                                <img src="@/assets/svg/add.svg" alt="add">
                            </button>
                        </form>
                    </template>
                </div>
            </div>
            <div class="d-flex flex-row mt-3 p-2 overflow-auto">
                <div v-if="libraryStore.isLoading" class="d-flex justify-content-center w-100">
                    <div class="fw-bold ">Loading...</div>
                </div>
                <template v-if="auth.user">
                    <div
                        class="btn-group d-flex flex-column p-1 mb-2 w-100"
                        role="group"
                    >
                        <div v-for="libraryItem in libraryStore.items" :key="libraryItem.id">
                            <div
                                @click="libraryStore.selectLibraryItem(libraryItem.id)"
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
                                        <template v-else>
                                        <span
                                            style="font-size: 13px; opacity: 50%"
                                            v-text="libraryItem.user?.username"
                                        >
                                        </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-if="!auth.user && !libraryStore.isLoading">
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
</style>
