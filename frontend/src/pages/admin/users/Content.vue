<script setup>
import { useAdminPanelStore } from '@/stores/adminPanel.ts'
import { useUserSearch } from '@/stores/userSearch.ts'
import { onMounted, computed, ref, watch } from 'vue'

const adminPanelStore = useAdminPanelStore()
const userSearchStore = useUserSearch()

const currentPage = ref(1)

const users = computed(() => {
    return adminPanelStore.users.data ?? []
})

const fetchPage = async (page) => {
    currentPage.value = page
    await adminPanelStore.setLoading()
    await adminPanelStore.fetchUsers(page)
}

watch(() => userSearchStore.search, async () => {
    currentPage.value = 1
    await adminPanelStore.setLoading();
    await adminPanelStore.fetchUsers(1, userSearchStore.search)
})

onMounted(async () => {
    await fetchPage(1)
})

</script>

<template>
    <div
        style="
            padding: 20px 30px 200px 300px;
            color: rgb(228, 228, 228);
            flex: 1 1 auto;
            overflow-y: auto;
            min-height: 0;
        "
        class="w-100 home-content"
    >
        <div class="fs-3 fw-bold">Users</div>
        <div class="mt-4" style="max-width: 250px">
            <div class="stat-card bg-minor d-flex flex-column">
                <span class="opacity-50">Total users</span>
                <template v-if="adminPanelStore.isLoading">
                    <div class="search-spinner mt-2 ms-1"></div>
                </template>
                <template v-else>
                    <span class="fs-4 mt-2" v-text="adminPanelStore.users?.total"></span>
                </template>
            </div>
        </div>
        <div
            class="d-flex flex-row mt-5 position-relative"
            style="max-width: 300px; max-height: 46px"
        >
            <img
                style="top: 12px; left: 15px"
                class="position-absolute z-2"
                src="@/assets/svg/search.svg"
                alt="search"
            />
            <input
                style="
                    border: 1px solid rgba(228, 228, 228, 0.15);
                    padding-left: 40px;
                    color: rgb(228, 228, 228);
                "
                class="w-100 form-control rounded-4 bg-minor"
                type="text"
                v-model="userSearchStore.search"
                placeholder="Search by username or email..."
                data-bs-toggle="dropdown"
                aria-expanded="false"
            />
        </div>
        <div class="mt-4">
            <div class="position-relative">
                <transition name="fade">
                    <div
                        v-if="adminPanelStore.isLoading"
                        class="loading-overlay d-flex flex-column align-items-center justify-content-center"
                    >
                        <div class="search-spinner mb-2"></div>
                    </div>
                </transition>
                <table class="table table-borderless align-middle" style="padding: 25px 0 0 295px">
                    <thead style="border-bottom: 1px solid rgba(228, 228, 228, 0.15)">
                        <tr>
                            <th scope="col" style="font-weight: lighter; opacity: 60%">User</th>
                            <th scope="col" style="font-weight: lighter; opacity: 60%">Email</th>
                            <th scope="col" style="font-weight: lighter; opacity: 60%">Joined</th>
                            <th scope="col" style="font-weight: lighter; opacity: 60%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                            <template v-for="user in users" :key="user.id">
                                <tr style="border-bottom: 1px solid rgba(228, 228, 228, 0.05)">
                                    <td style="font-size: 15px">
                                        <img
                                            class="rounded-circle me-2"
                                            style="
                                                width: 35px;
                                                height: 35px;
                                                border: 1px solid rgba(228, 228, 228, 0.1);
                                            "
                                            :src="user.profile_picture"
                                            alt="cover"
                                        />
                                        {{ user.username }}
                                    </td>
                                    <td style="font-size: 15px; opacity: 50%">
                                        {{ user.email }}
                                    </td>
                                    <td style="font-size: 15px">
                                        {{ user.joined_at }}
                                    </td>
                                    <td style="width: 100px; padding-left: 20px">
                                        <img
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            style="cursor: pointer"
                                            src="@/assets/svg/horizontalSettingsWhite.svg"
                                            alt="settings"
                                            class="options"
                                        />
                                        <ul class="dropdown-menu">
                                            <li
                                                class="dropdown-item d-flex align-items-center"
                                                data-bs-toggle="modal"
                                                data-bs-target="#userProfileModal"
                                                @click="adminPanelStore.setViewUser(user)"
                                            >
                                                <img
                                                    class="me-2"
                                                    src="@/assets/svg/view.svg"
                                                    alt="view"
                                                />
                                                View Profile
                                            </li>
                                            <li
                                                class="dropdown-item d-flex align-items-center"
                                                data-bs-toggle="modal"
                                                data-bs-target="#userDeletionModal"
                                                @click="adminPanelStore.setViewUser(user)"
                                            >
                                                <img
                                                    class="me-2"
                                                    src="@/assets/svg/delete.svg"
                                                    alt="delete"
                                                />
                                                Delete
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </template>
                    </tbody>
                </table>
                <template v-if="users.length === 0">
                    <div class="d-flex align-items-center justify-content-center opacity-50">
                        No users found...
                    </div>
                </template>
                <div class="opacity-50 w-100">
                    <span>
                        Showing {{adminPanelStore.users.from}}-{{adminPanelStore.users.to}}
                        of {{adminPanelStore.users.total}}
                    </span>
                </div>
                <div
                    class="d-flex justify-content-end align-items-center mt-3"
                    style="gap: 10px"
                >
                    <button
                        class="btn btn-pagination"
                        @click="fetchPage(currentPage - 1)"
                        :disabled="currentPage === 1"
                    >
                        <img src="@/assets/svg/arrowLeft.svg" alt="prev">
                    </button>
                    <button
                        class="btn btn-pagination"
                        @click="fetchPage(currentPage + 1)"
                        :disabled="currentPage === adminPanelStore.users.last_page"
                    >
                        <img src="@/assets/svg/arrowRight.svg" alt="next">
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.stat-card {
    padding: 15px;
    border: 1px solid rgba(228, 228, 228, 0.15) !important;
    border-radius: 15px;
}
.options {
    transition: 0.2s;

    &:hover {
        opacity: 0.5;
    }
}
.form-control {
    &:focus {
        box-shadow: none;
        border-color: rgb(158, 23, 63) !important;
    }
}
.loading-overlay {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    transition: .4s;
    backdrop-filter: blur(1px);
    z-index: 1;
    pointer-events: none;
}
.btn-pagination {
    border: 1px solid rgba(179, 27, 71,.5) !important;
    border-radius: 15px !important;
    color: rgb(228,228,228) !important;
    height: 30px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 10px !important;

    &:hover {
        background-color: rgba(179, 27, 71, 0.59) !important;
    }

    &:active {
        background-color: #c11c4c !important;
        border-color: #c11c4c !important;
    }
}
.search-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(228, 228, 228, 0.2);
    border-top: 2px solid rgb(158, 23, 63);
    border-radius: 50%;
    animation: spin 0.4s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
