<script setup lang="ts">
import Header from "@/pages/artist-merch/Header.vue";
import { onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();

onMounted(() => {
    if (!auth.isReady) {
        auth.fetchUser();
    }
});


</script>

<template>
    <div class="app-wrapper">
        <Header/>
        <div style="color: rgb(228, 228, 228)" class="flex-grow-1 content position-relative">
            <router-view/>
        </div>
    </div>
</template>

<style lang="scss">
.content {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 0 0 90px 0 !important;
    min-height: 0 !important;
}
.loading-overlay {
    position: fixed !important;
    inset: 0 !important;
    background: rgba(32, 32, 32, 0.5) !important;
    backdrop-filter: blur(4px) !important;
    z-index: 1000 !important;
    pointer-events: auto !important;
    user-select: none !important;
}
.search-spinner {
    width: 18px !important;
    height: 18px !important;
    border: 2px solid rgba(228, 228, 228, 0.2) !important;
    border-top: 2px solid rgb(158, 23, 63) !important;
    border-radius: 50% !important;
    animation: spin 0.4s linear infinite !important;
}

@keyframes spin {
    from {
        transform: rotate(0deg) !important;
    }
    to {
        transform: rotate(360deg) !important;
    }
}
</style>
