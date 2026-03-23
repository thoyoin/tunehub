<script setup lang="ts">
import Header from "@/pages/artist-studio-merch/Header.vue";
import Content from "@/pages/artist-studio-merch/Content.vue";
</script>

<template>
    <div class="app-wrapper">
        <Header/>
        <Content/>
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
