<script setup lang="ts">
import {onMounted} from "vue";

import Header from "@/pages/artist-studio/Header.vue";
import Content from "@/pages/artist-studio/Content.vue";
import UploadReleaseModal from "@/pages/artist-studio/modals/UploadReleaseModal.vue";
import EditTrackModal from "@/pages/artist-studio/modals/editTrackModal.vue";
import EditReleaseModal from "@/pages/artist-studio/modals/editReleaseModal.vue";
import SubscriptionModal from "@/pages/home/modals/SubscriptionModal.vue";
import EditMerchModal from "@/pages/artist-studio/modals/editMerchModal.vue";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();

onMounted(async () => {
    if (!auth.isReady) {
        await auth.fetchUser()
    }
})

</script>

<template>
    <div class="app-wrapper">
        <Header/>
        <Content/>
        <upload-release-modal v-if="auth.user"/>
        <edit-release-modal v-if="auth.user"/>
        <edit-track-modal v-if="auth.user"/>
        <subscription-modal v-if="auth.user && auth.user?.is_subscribed"/>
        <edit-merch-modal/>
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
