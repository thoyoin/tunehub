<script setup lang="ts">
import Sidebar from "@/pages/admin/Sidebar.vue";
import Content from "@/pages/admin/playlists/Content.vue";
import PlaylistViewModal from "@/pages/admin/modals/PlaylistViewModal.vue";
</script>

<template>
    <Sidebar/>
    <Content/>
    <playlist-view-modal/>
</template>

<style scoped></style>
