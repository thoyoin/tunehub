<script setup>
    import { useAuthStore } from "@/stores/auth.js";
    import { useImageUpload } from "@/composables/useImageUpload.js";
    import api from "@/lib/api.js";
    import {ref, watch} from "vue";
    import { useToast } from "vue-toastification";

    const toast = useToast();
    const auth = useAuthStore();
    const {previewUrl, fileToUpload, handleImageUpload} = useImageUpload();

    const username = ref('');
    const email = ref('');

    watch(() => auth.user, (user) => {
        if (user) {
            username.value = user.username;
            email.value = user.email
        }
    }, { immediate: true })

    const handleUserUpdate = async () => {
        const formData = new FormData();

        formData.append("username", username.value);
        formData.append("email", email.value);

        if (fileToUpload.value) {
            formData.append("profile_picture", fileToUpload.value);
        }

        try {
            await api.post('/api/user/update', formData);

            await auth.fetchUser()

            toast.success('Profile updated successfully!');
        } catch (error) {
            console.log(error);
            toast.error('Something went wrong.');
        }
    }
</script>

<template>
    <div
        class="modal fade"
        id="settingsModal"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="handleUserUpdate" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Change your data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex flex-row align-items-center">
                            <div class="d-flex flex-column justify-content-start align-items-center w-50">
                                <img
                                    :src="previewUrl ?? auth.user.profile_picture"
                                    style="width: 150px; height: 150px;"
                                    class="rounded-2"
                                    alt="profile"
                                >
                                <label class="btn btn-new mt-2" for="profilePicture">New photo</label>
                                <input
                                    id="profilePicture"
                                    type="file"
                                    @change="handleImageUpload"
                                    class="d-none"
                                    accept="image/*"
                                >
                            </div>
                            <div class="d-flex flex-column">
                                <label class="my-1" for="username" >Username</label>
                                <input
                                    id="username"
                                    type="text"
                                    class="form-control rounded-4 bg-minor"
                                    v-model="username"
                                />
                                <label class="my-1" for="email">Email</label>
                                <input
                                    id="email"
                                    type="text"
                                    class="form-control rounded-4 bg-minor"
                                    v-model="email"
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
