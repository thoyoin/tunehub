<script setup lang="ts">
import { onMounted, watch } from "vue";
import { useRoute } from "vue-router";

import Header from "@/pages/home/Header.vue";
import Library from "@/pages/home/Library.vue";
import Content from "@/pages/artist-card/Content.vue";
import SettingsModal from "@/pages/home/modals/settingsModal.vue";
import AuthenticateModal from "@/pages/release/modals/authenticateModal.vue";
import SubscriptionModal from "@/pages/home/modals/SubscriptionModal.vue";
import { useLibraryStore } from "@/stores/library";
import { useAuthStore } from "@/stores/auth";
import { useArtistCardStore } from "@/stores/artistCard";

const route = useRoute();
const auth = useAuthStore();
const artistCardStore = useArtistCardStore();
const libraryStore = useLibraryStore();

onMounted(async () => {
    if (!auth.isReady) {
        await auth.fetchUser();
    }
    await libraryStore.fetchItems();
})

watch(
    () => route.params.artistId,
    async (id) => {
        if (id && typeof (id) === "string") {
            await artistCardStore.ensureDataIsLoaded(Number(id));
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
</style>
