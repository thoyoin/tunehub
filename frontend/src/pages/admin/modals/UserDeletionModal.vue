<script setup>
import { useToast } from 'vue-toastification'

import { useUsersStore } from '@/stores/AdminPanel/users.ts'

const adminPanelStore = useUsersStore()
const toast = useToast()

const handleUserDeletion = async (id) => {
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
        id="userDeletionModal"
        tabindex="-1"
        aria-labelledby="userDeletionModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="userDeletionModalLabel">Delete User</h1>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column align-items-center">
                        <div
                            style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)"
                            class="d-flex w-100 pb-3 flex-column"
                        >
                            <span class="opacity-50">Are you sure you want to delete</span>
                            <span>{{adminPanelStore.viewUser?.username}}?</span>
                            <span class="opacity-50">
                                This action cannot be undone.
                                All associated data including tracks, playlists,
                                and activity will be permanently
                                removed.
                            </span>
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
                    <button
                        class="btn btn-primary"
                        :disabled="adminPanelStore.isLoading"
                        data-bs-dismiss="modal"
                        @click="handleUserDeletion(adminPanelStore.viewUser?.id)"
                    >
                        Delete
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
    .modal-footer {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        border-color: rgb(75, 75, 75);
    }
}
</style>
