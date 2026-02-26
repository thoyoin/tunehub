<script setup lang="ts">
    import Sidebar from "@/pages/admin/Sidebar.vue";
    import SettingsModal from "@/pages/home/modals/settingsModal.vue";
    import Content from "@/pages/admin/moderation/Content.vue";
    import ReleaseViewModal from "@/pages/admin/modals/ReleaseViewModal.vue";

</script>

<template>
    <div class="app-wrapper">
        <Sidebar></Sidebar>
        <Content></Content>
        <settings-modal/>
        <release-view-modal/>
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
