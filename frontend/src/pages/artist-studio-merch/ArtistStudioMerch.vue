<script setup lang="ts">
import Header from "@/pages/artist-studio-merch/Header.vue";
import Content from "@/pages/artist-studio-merch/Content.vue";
import { useMerchManagementStore } from "@/stores/merchManagement";
import MerchAfterUploadModal from "@/pages/artist-studio-merch/modals/MerchAfterUploadModal.vue";
import { useRouter } from "vue-router";

const router = useRouter();
const merchStore = useMerchManagementStore();

</script>

<template>
    <div class="app-wrapper">
        <Header/>
        <div class="position-relative">
            <Content />
            <Transition name="fade-scale">
                <merch-after-upload-modal
                    v-if="merchStore.showMerchUploadModal"
                    :show="merchStore.showMerchUploadModal"
                    :title="merchStore.uploadModalData?.title ?? ''"
                    :message="merchStore.uploadModalData?.message ?? ''"
                    @close="merchStore.showMerchUploadModal = false"
                    @goBack="router.push('/artists')"
                />
            </Transition>
        </div>
    </div>
</template>

<style scoped>
.app-wrapper {
    display: flex;
    flex-direction: column;
    flex: 1 1 100%;
    min-height: 0;
}
.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.96);
}

.fade-scale-enter-to,
.fade-scale-leave-from {
    opacity: 1;
    transform: scale(1);
}
</style>

