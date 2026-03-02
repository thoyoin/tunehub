<script setup lang="ts">
import Sidebar from '@/pages/admin/Sidebar.vue'
import Content from '@/pages/admin/users/Content.vue'
import UserProfileModal from '@/pages/admin/modals/UserProfileModal.vue'
import UserDeletionModal from '@/pages/admin/modals/UserDeletionModal.vue'
import SettingsModal from '@/pages/home/modals/settingsModal.vue'

</script>

<template>
    <div class="app-wrapper">
        <Sidebar/>
        <Content/>
        <user-profile-modal/>
        <user-deletion-modal/>
        <settings-modal/>
    </div>
</template>

<style scoped>
.app-wrapper {
    display: flex;
    flex-direction: column;
    flex: 1 1 100%;
    min-height: 0;
}
</style>
