<script setup lang="ts">
import Sidebar from "@/pages/admin/Sidebar.vue";
import Content from "@/pages/admin/merch/Content.vue";
import SettingsModal from "@/pages/home/modals/settingsModal.vue";
import MerchViewModal from "@/pages/admin/modals/MerchViewModal.vue";
</script>

<template>
    <div class="app-wrapper">
        <Sidebar/>
        <Content/>
        <settings-modal/>
        <merch-view-modal/>
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
