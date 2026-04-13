<script setup lang="ts">
import { onMounted } from "vue";

import Header from "@/pages/subscription/Header.vue";
import SettingsModal from "@/pages/home/modals/settingsModal.vue";
import Content from "@/pages/subscription/Content.vue";

import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();

onMounted(async () => {
    if (!auth.isReady) {
        await auth.fetchUser();
    }
})

</script>

<template>
    <div class="app-wrapper">
        <Header/>
        <settings-modal/>
        <Content/>
    </div>
</template>

<style scoped lang="scss">
.app-wrapper {
    display: flex;
    flex-direction: column;
    flex: 1 1 100%;
    min-height: 0;
}
</style>
