<script setup lang="ts">
import { useToast } from 'vue-toastification'

import { useUsersStore } from '@/stores/AdminPanel/users'

const adminPanelStore = useUsersStore()
const toast = useToast()

const handleUserDeletion = async (id: number) => {
    try {
        await adminPanelStore.deleteUser(id)

        toast.success('User deleted successfully.')
    } catch (e) {
        console.error(e)

        toast.error('Something went wrong.')
    }
}
</script>

<template>
    <div
        class="modal fade"
        id="userProfileModal"
        tabindex="-1"
        aria-labelledby="userProfileModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="userProfileModalLabel">User Profile</h1>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column align-items-center">
                        <div
                            style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)"
                            class="d-flex w-100 pb-3 flex-column justify-content-start align-items-center w-50"
                        >
                            <img
                                :src="adminPanelStore.viewUser?.profile_picture"
                                style="width: 150px; height: 150px"
                                class="rounded-4"
                                alt="profile"
                            />
                            <span class="mt-3">{{ adminPanelStore.viewUser?.username }}</span>
                            <span
                                style="
                                               border: 1px solid rgba(228, 228, 228, 0.05);
                                               border-radius: 15px !important;
                                               padding: 2px 5px;
                                               opacity: 60%;
                                               font-size: 13px;
                                               text-align: center;
                                               margin: 0
                                           "
                                class="mt-2 opacity-75"
                            >
                                {{ adminPanelStore.viewUser?.roles[0]?.name }}
                            </span>
                        </div>
                        <div
                            style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)"
                            class="d-flex flex-column pt-2 align-items-center w-100"
                        >
                            <div
                                class="d-flex justify-content-between pb-2 w-100"
                                style="font-size: 15px"
                            >
                                <div class="d-flex opacity-50">
                                    <img class="me-2" src="@/assets/svg/mail.svg" alt="email" />
                                    Email:
                                </div>
                                <div class="d-flex" style="overflow: clip">
                                    {{ adminPanelStore.viewUser?.email }}
                                </div>
                            </div>
                            <div
                                class="d-flex justify-content-between pb-2 w-100"
                                style="font-size: 15px"
                            >
                                <div class="d-flex opacity-50">
                                    <img
                                        class="me-2"
                                        src="@/assets/svg/calendar.svg"
                                        alt="calendar"
                                    />
                                    Joined:
                                </div>
                                <div class="d-flex">
                                    {{ adminPanelStore.viewUser?.joined_at }}
                                </div>
                            </div>
                            <div
                                class="d-flex justify-content-between pb-2 w-100"
                                style="font-size: 15px"
                            >
                                <div class="d-flex opacity-50">
                                    <img
                                        class="me-2"
                                        src="@/assets/svg/note.svg"
                                        alt="calendar"
                                    />
                                    Tracks:
                                </div>
                                <div class="d-flex">
                                    {{ adminPanelStore.viewUser?.tracks?.length }}
                                </div>
                            </div>
                        </div>
                        <div
                            class="d-flex flex-row w-100 px-4 pt-3 justify-content-center"
                            style="font-size: 15px"
                        >
                            <div class="d-flex flex-column align-items-center">
                                <span style="font-size: 25px">
                                    {{ adminPanelStore.viewUser?.playlists?.length }}
                                </span>
                                <span style="font-size: 15px" class="opacity-50">Playlists</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <button
                        @click="handleUserDeletion(adminPanelStore.viewUser?.id)"
                        class="btn btn-primary w-50"
                        :disabled="adminPanelStore.isLoading"
                    >
                        Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal-content {
    background: rgb(40, 40, 41);
    color: rgb(228, 228, 228);
    .modal-header {
        border-color: rgb(75, 75, 75);
    }
    .footer {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100% !important;
        border-color: rgb(75, 75, 75);
        padding: 10px;
    }
}

</style>
