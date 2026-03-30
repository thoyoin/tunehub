<script setup lang="ts">

defineProps<{
    show: boolean | null,
    title: string | null,
    message: string | null,
}>()

defineEmits<{
    goBack: [],
    close: [],
}>()

</script>

<template>
    <div
        v-if="show"
        style="left: 0;right: 0; bottom:0;"
        class="modal-dialog p-5 h-100 position-fixed custom-modal d-flex align-items-center justify-content-center"
    >
        <div style="width: 400px">
            <div class="modal-content rounded-5 p-3">
                <div class="modal-header fw-bold">
                    <span class="modal-title">{{ title }}</span>
                </div>
                <div class="mt-3 modal-body">
                    <div>{{ message }}</div>
                </div>
                <div class="d-flex align-items-center justify-content-end w-100 mt-3 gap-2">
                    <button @click="$emit('goBack'); $emit('close')" class="btn btn-artists">My Products</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-modal {
    z-index: 100;
    background-color: rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(4px);
}
</style>
