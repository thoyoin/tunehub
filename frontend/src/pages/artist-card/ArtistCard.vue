<script setup lang="ts">
import Header from "@/pages/home/Header.vue";
import Library from "@/pages/home/Library.vue";
import Content from "@/pages/artist-card/Content.vue";
import { onMounted, watch } from "vue";
import { useAuthStore } from "@/stores/auth.ts";
import { useArtistCardStore } from "@/stores/artistCard.ts";
import { useRoute } from "vue-router";
import SettingsModal from "@/pages/home/modals/settingsModal.vue";
import AuthenticateModal from "@/pages/release/modals/authenticateModal.vue";
import SubscriptionModal from "@/pages/home/modals/SubscriptionModal.vue";

const route = useRoute();
const auth = useAuthStore();
const artistCardStore = useArtistCardStore();

onMounted(async () => {
    if (!auth.isReady) {
        await auth.fetchUser();
    }
})

watch(
    () => route.params.artistId,
    async (id) => {
        if (id) {
            await artistCardStore.fetchArtist(id);
            await artistCardStore.fetchLatestRelease();
            await artistCardStore.fetchTopSongs();
            await artistCardStore.fetchAlbums();
        }
    },
    { immediate: true },
);
</script>

<template>
    <div class="app-wrapper">
        <Header />
        <Library />
        <Content />
        <settings-modal v-if="auth.user" />
        <authenticate-modal />
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
