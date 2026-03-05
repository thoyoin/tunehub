<script setup lang="ts">
import Header from '@/pages/home/Header.vue'
import Library from '@/pages/home/Library.vue'
import Content from '@/pages/playlist/Content.vue'
import { useRoute } from 'vue-router'
import { useLibraryStore } from '@/stores/library'
import { onMounted, watch } from 'vue'
import { useAuthStore} from '@/stores/auth'
import EditPlaylistModal from '@/pages/playlist/modals/editPlaylistModal.vue'
import SettingsModal from '@/pages/home/modals/settingsModal.vue'

const route = useRoute()
const libraryStore = useLibraryStore()
const auth = useAuthStore()

watch(
    () => route.params.playlistId,
    async (id) => {
        await libraryStore.getPlaylist(id)
    },
    { immediate: true },
)

onMounted(async () => {
    if (!auth.isReady) {
        await auth.fetchUser()
    }

    if (!libraryStore.isReady) {
        await libraryStore.fetchItems()
    }
})

</script>

<template>
    <div class="app-wrapper">
        <Header></Header>
        <Library></Library>
        <Content/>
        <edit-playlist-modal v-if="libraryStore.libraryItem" />
        <settings-modal v-if="auth.user" />
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
