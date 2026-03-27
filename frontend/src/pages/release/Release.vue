<script setup lang="ts">
import Header from '@/pages/home/Header.vue'
import Library from '@/pages/home/Library.vue'
import Content from '@/pages/release/Content.vue'
import { useRoute } from 'vue-router'
import { useReleaseStore } from '@/stores/release'
import { onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AuthenticateModal from '@/pages/release/modals/authenticateModal.vue'
import SettingsModal from '@/pages/home/modals/settingsModal.vue'
import SubscriptionModal from "@/pages/home/modals/SubscriptionModal.vue";

const route = useRoute()
const releaseStore = useReleaseStore()
const auth = useAuthStore()

watch(
    () => route.params.releaseId,
    async (id) => {
        if (typeof id === 'string') {
            await releaseStore.getRelease(id)
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
        <authenticate-modal />
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
