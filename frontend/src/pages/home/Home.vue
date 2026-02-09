<script setup>
    import Header from "@/pages/home/Header.vue";
    import { onMounted } from 'vue'
    import { useAuthStore } from '@/stores/auth'
    import { useLibraryStore } from "@/stores/library.js";
    import Library from "@/pages/home/Library.vue";
    import SettingsModal from "@/pages/home/modals/settingsModal.vue";
    import Content from "@/pages/home/Content.vue";
    import EditPlaylistModal from "@/pages/home/modals/editPlaylistModal.vue";
    import AuthenticateModal from "@/pages/home/modals/authenticateModal.vue";

    const auth = useAuthStore()
    const libraryStore = useLibraryStore()

    onMounted(async () => {
        if (!auth.isReady) {
            await auth.fetchUser()
        }
        await libraryStore.fetchItems()
    })

</script>

<template>
    <div class="app-wrapper">
        <Header/>
        <Library/>
        <Content/>
        <settings-modal v-if="auth.isReady"/>
        <edit-playlist-modal v-if="libraryStore.libraryItem"/>
        <authenticate-modal/>
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
