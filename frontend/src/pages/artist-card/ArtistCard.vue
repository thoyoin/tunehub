<script setup lang="ts">
import Header from "@/pages/home/Header.vue";
import Library from "@/pages/home/Library.vue";
import Content from "@/pages/artist-card/Content.vue";
import { onMounted, watch } from "vue";
import { useAuthStore } from "@/stores/auth.ts";
import { useArtistCardStore } from "@/stores/artistCard.ts";
import { useRoute } from "vue-router";
import SettingsModal from "@/pages/home/modals/settingsModal.vue";

const route = useRoute();
const auth = useAuthStore();
const artistCardStore = useArtistCardStore();

onMounted(async () => {
    if (!auth.isReady) {
        await auth.fetchUser();
    }
    if (artistCardStore.artist) {
        await artistCardStore.fetchLatestRelease();
        await artistCardStore.fetchTopSongs()
    }
});

watch(
    () => route.params.artistId,
    async (id) => {
        await artistCardStore.fetchArtist(id);
    },
    { immediate: true },
);
</script>

<template>
    <div class="app-wrapper">
        <Content />
        <Header />
        <Library />
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
