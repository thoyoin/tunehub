<script setup>
import Header from "@/pages/artist-studio/Header.vue";
import Content from "@/pages/artist-studio/Content.vue";
import UploadReleaseModal from "@/pages/artist-studio/modals/UploadReleaseModal.vue";
import { useAuthStore } from "@/stores/auth.js";
import {onMounted} from "vue";
import {useArtistStore} from "@/stores/artistStudio.js";
import EditTrackModal from "@/pages/artist-studio/modals/editTrackModal.vue";
import EditReleaseModal from "@/pages/artist-studio/modals/editReleaseModal.vue";

const auth = useAuthStore();

onMounted(async () => {
    if (!auth.isReady) {
        await auth.fetchUser()
    }
})

</script>

<template>
    <Header/>
    <Content/>
    <upload-release-modal v-if="auth.user" />
    <edit-track-modal v-if="auth.user" />
    <edit-release-modal v-if="auth.user" />
</template>

<style scoped>

</style>
