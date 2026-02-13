<script setup>
import { useImageUpload } from "@/composables/useImageUpload.js";
import { useArtistStore } from "@/stores/artistStudio.js";
import { useToast } from "vue-toastification";
import {ref, watch} from "vue";
import api from "@/lib/api.js";

const {previewUrl, fileToUpload, handleImageUpload} = useImageUpload();
const artistStore = useArtistStore();
const toast = useToast();

const releaseTitle = ref('')
const artist = ref('')

watch(() => artistStore.editingItem, (newItem) => {
    if (newItem) {
        releaseTitle.value = newItem.title;
        artist.value = newItem.artist;
    }
}, { immediate: true });

const handleReleaseUpdate = async () => {
    const formData = new FormData();

    formData.append("releaseTitle", releaseTitle.value);
    formData.append("artist", artist.value);

    if (fileToUpload.value) {
        formData.append("cover_url", fileToUpload.value);
    }

    try {
        await api.put(`/api/releases/${artistStore.editingItem.id}`, formData);

        await artistStore.fetchReleases()
        await artistStore.fetchTracks()

        toast.success('release has been successfully updated!');
    } catch (error) {
        console.log(error);
        toast.error('Something went wrong.');
    }
}

</script>

<template>
    <form @submit.prevent="handleReleaseUpdate" enctype="multipart/form-data">
        <div class="modal fade" id="editReleaseModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-scrollable modal-fullscreen p-5">
                <div class="modal-content rounded-4">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Change release data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center">
                        <div
                            style="max-width: 1600px; max-height: 1000px"
                            class="d-flex flex-row w-100 h-100 align-items-center justify-content-between"
                        >
                            <div style="margin-right: 150px;margin-left: 150px"
                                 class="d-flex flex-column w-100 h-75">
                                <label
                                    for="uploadCover"
                                    class="btn btn-uploadedCover w-100 p-2 mb-3 mt-3 h-100 d-flex flex-column align-items-center justify-content-center position-relative"
                                    style="max-width: 600px; min-height: 400px"
                                >
                                    <div class="w-100 h-100 position-relative">
                                        <img
                                            id="cover_url"
                                            :src="previewUrl ?? artistStore.editingItem?.cover_url"
                                            class="position-absolute w-100 h-100"
                                            style="
                                                top: 50%;
                                                left: 50%;
                                                transform: translate(-50%, -50%);
                                                max-width: none;
                                            "
                                            alt="cover"
                                        >
                                        <span class="position-absolute bottom-0 start-50 translate-middle-x mb-2">
                                        Change cover
                                    </span>
                                    </div>
                                </label>
                                <input
                                    id="uploadCover"
                                    name="cover_url"
                                    type="file"
                                    class="d-none"
                                    accept="image/*"
                                    @change="handleImageUpload"
                                >
                            </div>
                            <div class="d-flex flex-column w-100 me-4">
                                <label for="releaseTitle" style="font-size: 13px" class="fw-bold mb-2 mt-3">
                                    Release title
                                </label>
                                <input
                                    name="releaseTitle"
                                    id="releaseTitle"
                                    class="mb-2 form-control rounded-4 bg-minor"
                                    v-model="releaseTitle"
                                    required
                                >
                                <label for="artist" style="font-size: 13px" class="fw-bold my-2">Main artist</label>
                                <input
                                    id="artist"
                                    name="artist"
                                    class="mb-2 form-control rounded-4 bg-minor"
                                    v-model="artist"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</template>

<style scoped>
.form-control {
    border-color: rgb(75,75,75) !important;
    color: rgb(228,228,228) !important;
    max-width: 600px !important;

    &:focus {
        box-shadow: none;
        border-color: #ff2667 !important;
    }
}
</style>
