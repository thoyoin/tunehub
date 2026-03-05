<script setup lang="ts">
import SettingsModal from '@/pages/home/modals/settingsModal.vue'
import Sidebar from '@/pages/admin/Sidebar.vue'

</script>

<template>
    <div class="app-wrapper">
        <Sidebar/>
        <settings-modal/>
    </div>
</template>

<style>
.app-wrapper {
    display: flex;
    flex-direction: column;
    flex: 1 1 100%;
    min-height: 0;
}
.stat-card {
    padding: 15px !important;
    border: 1px solid rgba(228, 228, 228, 0.15) !important;
    border-radius: 15px !important;
    max-width: 200px !important;
    min-height: 100px !important;
    width: 100% !important;
}
</style>
