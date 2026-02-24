<script setup>
import { useAdminPanelStore } from '@/stores/adminPanel.js'

const adminPanelStore = useAdminPanelStore()
</script>

<template>
    <div
        class="modal fade"
        id="userProfileModal"
        tabindex="-1"
        aria-labelledby="userProfileModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-sm modal-dialog-centered">
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
                        </div>
                        <div
                            style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)"
                            class="d-flex flex-column pt-2 opacity-50 align-items-center w-100"
                        >
                            <div
                                class="d-flex justify-content-between pb-2 w-100"
                                style="font-size: 15px"
                            >
                                <div class="d-flex">
                                    <img class="me-2" src="@/assets/svg/mail.svg" alt="email" />
                                    Email:
                                </div>
                                <div class="d-flex">
                                    {{ adminPanelStore.viewUser?.email }}
                                </div>
                            </div>
                            <div
                                class="d-flex justify-content-between pb-2 w-100"
                                style="font-size: 15px"
                            >
                                <div class="d-flex">
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
                        </div>
                        <div
                            class="d-flex flex-row w-100 px-4 pt-3 justify-content-between"
                            style="font-size: 15px"
                        >
                            <div class="d-flex flex-column align-items-center">
                                <span>
                                    {{ adminPanelStore.viewUser?.playlists?.length }}
                                </span>
                                <span class="opacity-50">Playlists</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100">Block User</button>
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
    .modal-footer {
        border-color: rgb(75, 75, 75);
    }
}
</style>
