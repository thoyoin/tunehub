<script setup>
import SettingsModal from '@/pages/home/modals/settingsModal.vue'
import Sidebar from '@/pages/admin/Sidebar.vue'

</script>

<template>
    <div class="app-wrapper">
        <Sidebar/>
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
