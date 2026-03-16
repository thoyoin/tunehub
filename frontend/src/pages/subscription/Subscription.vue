<script setup lang="ts">
import Header from "@/pages/subscription/Header.vue";
import SettingsModal from "@/pages/home/modals/settingsModal.vue";
import Content from "@/pages/subscription/Content.vue";
import SubscribeModal from "@/pages/subscription/modals/SubscribeModal.vue";

</script>

<template>
    <div class="app-wrapper">
        <Header/>
        <settings-modal/>
        <subscribe-modal/>
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
