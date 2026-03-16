<script setup lang="ts">
import Content from "@/pages/admin/overview/Content.vue";
import ReleaseViewModal from "@/pages/admin/modals/ReleaseViewModal.vue";
import UserProfileModal from "@/pages/admin/modals/UserProfileModal.vue";
</script>

<template>
    <div class="app-wrapper">
        <Content />
        <release-view-modal/>
        <user-profile-modal/>
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
