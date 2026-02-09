<script setup>
import { useUploadReleaseStore} from "@/stores/uploadRelease.js";
import draggable from "vuedraggable";

const uploadStore = useUploadReleaseStore();

</script>

<template>
        <div class="modal fade" id="uploadModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-scrollable modal-fullscreen p-5">
                <div class="modal-content rounded-4">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Upload your audio files</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center">
                        <template v-if="uploadStore.editor === false">
                            <div class="w-100">
                                <div
                                    class="w-100 h-100 d-flex align-items-center justify-content-center"
                                >
                                    <label
                                        for="uploadTrack"
                                        class="btn btn-upload d-flex flex-column align-items-center
                                        justify-content-center w-50 h-100"
                                    >
                                        <img
                                            class="mb-4"
                                            src="@/assets/svg/cloudUpload.svg"
                                            alt="upload"
                                        >
                                        Choose files
                                    </label>
                                    <input
                                        id="uploadTrack"
                                        name="audio_url[]"
                                        type="file"
                                        class="d-none"
                                        accept="audio/*"
                                        multiple
                                        @change="uploadStore.onFilesUploaded"
                                    >
                                </div>
                            </div>
                        </template>
                        <template v-if="uploadStore.editor === true">
                            <div class="w-100">
                                <div class="d-flex py-5 flex-column justify-content-center align-items-center">
                                    <div
                                        style="max-width: 1600px; max-height: 1000px"
                                        class="d-flex flex-row w-100 h-100 align-items-center justify-content-between"
                                    >
                                        <div style="margin-right: 150px;margin-left: 150px"
                                             class="d-flex flex-column w-100 h-75">
                                            <label
                                                for="uploadCover"
                                                class="btn w-100 mb-3 mt-3 h-100 d-flex flex-column
                                                align-items-center justify-content-center position-relative"
                                                :class="uploadStore.coverPreview ? 'btn-uploadedCover' : 'btn-uploadCover'"
                                                style="max-width: 600px; min-height: 400px"
                                            >
                                                <template v-if="!uploadStore.coverPreview">
                                                    <div
                                                        class="d-flex flex-column align-items-center
                                                        justify-content-center h-100"
                                                    >
                                                        <img
                                                            class="mb-4"
                                                            src="@/assets/svg/upload.svg"
                                                            alt="upload"
                                                        >
                                                        <span>Upload cover</span>
                                                    </div>
                                                </template>
                                                <template v-if="uploadStore.coverPreview">
                                                    <div class="w-100 h-100 position-relative">
                                                        <img
                                                            :src="uploadStore.coverPreview"
                                                            class="position-absolute"
                                                            style="
                                                                top: 50%;
                                                                left: 50%;
                                                                transform: translate(-50%, -50%);
                                                                max-width: none;
                                                            "
                                                            alt="cover"
                                                        >
                                                        <span
                                                            class="position-absolute bottom-0 start-50 translate-middle-x mb-2"
                                                        >
                                                        Change cover
                                                    </span>
                                                    </div>
                                                </template>
                                            </label>
                                            <input
                                                id="uploadCover"
                                                name="cover_url"
                                                type="file"
                                                class="d-none"
                                                accept="image/*"
                                                required
                                                @change="uploadStore.setPreview"
                                            >
                                        </div>
                                        <div class="d-flex flex-column w-100 me-4">
                                            <div
                                                style="border-bottom:1px solid rgba(228, 228, 228, 0.15)"
                                                class="d-flex flex-column mt-2 pt-2 align-items-center"
                                            >
                                                <label
                                                    for="uploadTrack"
                                                    class="btn btn-replace-file w-50 mb-3 d-flex
                                                    flex-column align-items-center justify-content-center"
                                                >
                                                    Replace loaded tracks
                                                </label>
                                            </div>
                                            <div v-show="uploadStore.uploadedTracks.length > 1">
                                                <label
                                                    for="albumTitle"
                                                    style="font-size: 13px"
                                                    class="fw-bold mb-2 mt-3"
                                                >
                                                    Album title
                                                </label>
                                                <input
                                                    name="albumTitle"
                                                    v-model="uploadStore.albumTitle"
                                                    id="albumTitle"
                                                    class="mb-2 form-control bg-minor rounded-4"
                                                    required
                                                >
                                            </div>
                                            <label for="artist" style="font-size: 13px" class="fw-bold my-2">
                                                Main artist
                                            </label>
                                            <input
                                                id="artist"
                                                v-model="uploadStore.artist"
                                                name="artist"
                                                class="mb-2 form-control bg-minor rounded-4"
                                                required
                                            >
                                            <label
                                                for="release_date"
                                                style="font-size: 13px"
                                                class="fw-bold my-2"
                                            >
                                                Release date
                                            </label>
                                            <input
                                                class="mb-3 form-control bg-minor rounded-4"
                                                name="release_date"
                                                v-model="uploadStore.release_date"
                                                id="release_date"
                                                type="date"
                                                required
                                            >
                                        </div>
                                    </div>
                                    <draggable
                                        v-model="uploadStore.uploadedTracks"
                                        item-key="originalIndex"
                                        class="w-50 d-flex flex-column justify-content-center align-items-center"
                                    >
                                        <template #item="{ element, index }">
                                            <div
                                                class="d-flex justify-content-start align-items-center mt-2 mb-3 w-100"
                                            >
                                            <span class="drag-handle d-flex">
                                                <img src="@/assets/svg/drag.svg" alt="drag">
                                            </span>
                                                <span class="mx-3">{{ index + 1 }}</span>
                                                <input
                                                    v-model="element.title"
                                                    name="title[]"
                                                    id="title"
                                                    class="me-3 w-50 form-control bg-minor rounded-4"
                                                    required
                                                >
                                                <span style="opacity: 50%; color: white">{{element.file.name}}</span>
                                            </div>
                                        </template>
                                    </draggable>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="uploadStore.handleReleaseUpload"
                            class="btn btn-primary"
                        >
                            Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
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
.btn-upload {
    padding: 0 5px 3px;
    color: #ff2667 !important;
    &:hover {
        border: dashed rgba(228,228,228, 10%) !important;
    }
    &:active {
        border-color: rgba(228,228,228, 50%) !important;
    }
}
.btn-uploadCover,
.btn-uploadedCover {
    padding: 0 5px 3px !important;
    color: #ff2667 !important;
    width: 600px !important;
    height: 600px !important;
    overflow: hidden;
}

.btn-uploadCover {
    border: dashed rgba(228,228,228, 10%) !important;
    &:hover {
        border: dashed rgba(228,228,228, 30%) !important;
    }
    &:active {
        border-color: rgba(228,228,228, 50%) !important;
    }
}
</style>
