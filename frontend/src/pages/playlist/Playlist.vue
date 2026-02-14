<script setup>
import Header from '@/pages/home/Header.vue'
import Library from '@/pages/home/Library.vue'
import Content from '@/pages/playlist/Content.vue'
import { useRoute } from 'vue-router'
import { useLibraryStore } from '@/stores/library.js'
import { onMounted, watch } from 'vue'
import { useAuthStore} from '@/stores/auth.js'
import EditPlaylistModal from '@/pages/release/modals/editPlaylistModal.vue'

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
    <Header></Header>
    <Library></Library>
    <Content></Content>
    <edit-playlist-modal v-if="libraryStore.libraryItem" />
</template>

<style scoped></style>
