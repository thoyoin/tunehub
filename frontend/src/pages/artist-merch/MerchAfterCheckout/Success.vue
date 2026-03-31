<script setup lang="ts">
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { onMounted } from "vue";
import { useToast } from "vue-toastification";

const router = useRouter();
const auth = useAuthStore();
const toast = useToast();

onMounted(async () => {
    toast.success('Payment was successfull!')

    await auth.fetchUser()
    await router.replace({ query: {} })
})

const backToHome = async () => {
    await auth.fetchUser()
    await router.push('/')
}
</script>

<template>
    <div class="app-wrapper">
        <div
            style="color: rgb(228, 228, 228)"
            class="flex-grow-1 content position-relative"
        >
            <div class="d-flex w-100 h-100 align-items-center justify-content-center">
                <div class="d-flex flex-column gap-4 align-items-center">
                    <span class="fw-bold fs-1">You successfully bought merch!</span>
                    <button
                        class="btn btn-primary w-50 fw-bold"
                        @click="backToHome"
                    >
                        Back to home
                    </button>
                </div>
            </div>
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

.content {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 0 30px 150px 30px !important;
    min-height: 0 !important;
}
</style>
