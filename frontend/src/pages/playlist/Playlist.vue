<script setup lang="ts">
import { useRoute } from 'vue-router'
import { onMounted, watch } from 'vue'

import Header from '@/pages/home/Header.vue'
import Library from '@/pages/home/Library.vue'
import Content from '@/pages/playlist/Content.vue'
import EditPlaylistModal from '@/pages/playlist/modals/editPlaylistModal.vue'
import SettingsModal from '@/pages/home/modals/settingsModal.vue'
import SubscriptionModal from "@/pages/home/modals/SubscriptionModal.vue";
import { useLibraryStore } from '@/stores/library'
import { useAuthStore} from '@/stores/auth'
import { usePlaylistStore } from "@/stores/playlist";

const route = useRoute()
const libraryStore = useLibraryStore()
const playlistStore = usePlaylistStore()
const auth = useAuthStore()

watch(
    () => route.params.playlistId,
    async (id) => {
        if (typeof id === 'string') {
            await playlistStore.getPlaylist(id)
        }
    },
    { immediate: true },
)

onMounted(async () => {
    if (!auth.isReady) {
        await auth.fetchUser()
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
        <subscription-modal v-if="auth.user && auth.user?.is_subscribed"/>
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
