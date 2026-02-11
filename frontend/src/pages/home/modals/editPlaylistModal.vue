<script setup>
    import { useLibraryStore } from "@/stores/library.js";
    import { useImageUpload } from "@/composables/useImageUpload.js";
    import { useToast } from "vue-toastification";
    import {ref, watch} from "vue";
    import api from "@/lib/api.js";

    const libraryStore = useLibraryStore();
    const toast = useToast();

    const { previewUrl, fileToUpload, handleImageUpload} = useImageUpload();

    const title = ref('');
    const description = ref('');

    watch(() => libraryStore.libraryItem.item, (item) => {
        if (item) {
            title.value = item.title;
            description.value = item.description;
        }
    }, { immediate: true })

    const handlePlaylistUpdate = async () => {
        const formData = new FormData();

        formData.append("title", title.value);
        formData.append("description", description.value);

        if (fileToUpload.value) {
            formData.append("cover_url", fileToUpload.value);
        }

        try {
            await api.put(`/api/playlist/${libraryStore.libraryItem.id}`, formData);

            await libraryStore.fetchItems()

            toast.success('Playlist updated successfully!');
        } catch (error) {
            console.log(error);

            toast.error('Something went wrong.');
        }
    }
</script>

<template>
    <div
        class="modal fade"
        id="editModal"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="handlePlaylistUpdate" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Change information</h1>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex flex-row">
                            <div class="d-flex flex-column align-items-center">
                                <img
                                    id="cover_url"
                                    style="width: 150px; height: 150px;"
                                    :src="previewUrl ?? libraryStore.libraryItem.cover_url"
                                    alt="cover"
                                >
                                <div v-if="libraryStore.libraryItem.slug !== 'liked-tracks'">
                                    <label class="btn btn-add mt-2" for="uploadCover">
                                        Upload cover
                                    </label>
                                    <input
                                        id="uploadCover"
                                        type="file"
                                        name="cover_url"
                                        class="d-none"
                                        accept="image/*"
                                        @change="handleImageUpload"
                                    >
                                </div>
                            </div>
                            <div class="d-flex flex-column" style="margin-left: 15px">
                                <div v-if="libraryStore.libraryItem.slug !== 'liked-tracks'">
                                    <label for="playlist-title">Title</label>
                                    <input
                                        class="form-control my-2 rounded-4 bg-minor"
                                        style="box-shadow: none;"
                                        v-model="title"
                                        name="title"
                                    />
                                </div>
                                <label for="playlist-description">Description</label>
                                <input
                                    class="form-control my-2 rounded-4 bg-minor"
                                    style="box-shadow: none;"
                                    v-model="description"
                                    name="description"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal-content {
    background: rgb(40,40,41);
    color: rgb(228,228,228);

    .modal-header {
        border-color: rgb(75,75,75);
    }
    .modal-footer {
        border-color: rgb(75,75,75);
    }
}
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
