<script setup lang="ts">
import { useLibraryStore } from '@/stores/library'
import { useImageUpload } from '@/composables/useImageUpload'
import { useToast } from 'vue-toastification'
import { ref, watch } from 'vue'
import api from '@/lib/api'

const libraryStore = useLibraryStore()
const toast = useToast()

const { previewUrl, fileToUpload, handleImageUpload } = useImageUpload()

const title = ref('')
const description = ref('')

watch(
    () => libraryStore.libraryItem?.item,
    (item) => {
        if (item) {
            title.value = item.title
            description.value = item.description
        }
    },
    { immediate: true },
)

const handleVisibilityUpdate = async (visibility: string) => {
    try {
        await libraryStore.setVisibility(visibility)

        await libraryStore.updateVisibility()
    } catch (error) {
        toast.error('Something went wrong')
    }
}

const handlePlaylistUpdate = async () => {
    const formData = new FormData()

    formData.append('title', title.value)
    formData.append('description', description.value ?? '')

    if (fileToUpload.value) {
        formData.append('cover_url', fileToUpload.value)
    }

    try {
        if (!libraryStore.libraryItem) return

        await api.put(`/api/playlist/${libraryStore.libraryItem.id}`, formData)

        await libraryStore.fetchItems()
        await libraryStore.getPlaylist(libraryStore.libraryItem.id)

        toast.success('playlist updated successfully!')
    } catch (error) {
        console.log(error)

        toast.error('Something went wrong.')
    }
}
</script>

<template>
    <div
        class="modal fade"
        id="editModal"
        tabindex="-1"
        aria-labelledby="editModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="handlePlaylistUpdate" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h1 class="modal-title" id="editModalLabel">Change playlist</h1>
                    </div>
                    <div class="modal-body ">
                        <div class="d-flex flex-column align-items-center">
                            <div class="d-flex flex-column align-items-center">
                                <img
                                    id="cover_url"
                                    class="rounded-3"
                                    style="
                                        width: 150px;
                                        height: 150px;
                                        border: 1px solid rgba(228, 228, 228, 0.15);
                                    "
                                    :src="previewUrl ?? libraryStore.libraryItem?.cover_url"
                                    alt="cover"
                                />
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
                                />
                            </div>
                            <div class="d-flex flex-column align-items-center">
                                <input
                                    class="form-control my-2 rounded-4 bg-minor"
                                    style="box-shadow: none"
                                    v-model="title"
                                    name="title"
                                    placeholder="Title"
                                />
                                <input
                                    class="form-control my-2 rounded-4 bg-minor"
                                    style="box-shadow: none"
                                    v-model="description"
                                    name="description"
                                    placeholder="Description"
                                />
                            </div>
                            <div class="mt-3">
                                <template v-if="libraryStore.libraryItem?.visibility === 'public'">
                                    <button
                                        @click="handleVisibilityUpdate('private')"
                                        type="button"
                                        class="btn btn-cancel d-flex align-items-center"
                                    >
                                        <img class="me-2" src="@/assets/svg/hidden.svg" alt="hidden">
                                        Make Private
                                    </button>
                                </template>
                                <template v-else>
                                    <button
                                        @click="handleVisibilityUpdate('public')"
                                        type="button"
                                        class="btn btn-cancel d-flex align-items-center"
                                    >
                                        <img class="me-2" src="@/assets/svg/globe.svg" alt="globe">
                                        Make Public
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            aria-label="Close"
                            data-bs-dismiss="modal"
                            class="btn btn-cancel"
                        >
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.form-control {
    border-color: rgb(75, 75, 75) !important;
    color: rgb(228, 228, 228) !important;
    max-width: 600px !important;

    &:focus {
        box-shadow: none;
        border-color: #ff2667 !important;
    }
}
</style>
