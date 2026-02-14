<script setup>
import Header from '@/pages/home/Header.vue'
import Library from '@/pages/home/Library.vue'
import Content from '@/pages/release/Content.vue'
import { useRoute } from 'vue-router'
import { useReleaseStore } from '@/stores/release.js'
import { onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useLibraryStore } from '@/stores/library.js'
import AuthenticateModal from '@/pages/release/modals/authenticateModal.vue'

const route = useRoute()
const releaseStore = useReleaseStore()
const auth = useAuthStore()
const libraryStore = useLibraryStore()

watch(
    () => route.params.releaseId,
    async (id) => {
        await releaseStore.getRelease(id)
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
        <authenticate-modal />
    </div>
</template>

<style scoped>
</style>
