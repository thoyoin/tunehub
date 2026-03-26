<script setup lang="ts">
import { useMerchManagementStore } from "@/stores/merchManagement";
import { onBeforeUnmount, ref } from "vue";
import draggable from "vuedraggable";

const merchManagementStore = useMerchManagementStore();

const fileInputRef = ref<HTMLInputElement | null>(null);

const openFilePicker = () => {
    fileInputRef.value?.click();
};

onBeforeUnmount(() => {
    merchManagementStore.clear();
});
</script>

<template>
    <div class="flex-grow-1 release-content position-relative">
        <div
            style="margin: 0 150px 0 150px; padding: 20px 20px 0 20px; color: rgb(228, 228, 228)"
            class="d-flex flex-column"
        >
            <div class="mb-5 fs-3 fw-bold">Merch Upload</div>
            <div style="border: 1px solid rgba(228, 228, 228, 0.15)" class="mt-5 rounded-5 p-3">
                <div class="fw-bold fs-5">Product Details</div>
                <span class="opacity-50" style="font-size: 13px">
                    Enter the basic information for this merch item.
                </span>
                <div class="d-flex flex-column">
                    <div class="d-flex flex-row">
                        <div class="col-6">
                            <label
                                for="itemTitle"
                                style="font-size: 13px"
                                class="fw-bold mb-2 mt-3"
                            >
                                Item title
                            </label>
                            <input
                                style="color: rgb(228, 228, 228)"
                                name="itemTitle"
                                id="itemTitle"
                                v-model="merchManagementStore.itemTitle"
                                class="mb-2 form-control bg-minor rounded-4 w-75"
                                placeholder="Enter title"
                                required
                            />
                        </div>
                        <div class="col-6">
                            <label
                                for="itemDescription"
                                style="font-size: 13px"
                                class="fw-bold mb-2 mt-3"
                            >
                                Item description
                            </label>
                            <input
                                style="color: rgb(228, 228, 228)"
                                name="itemDescription"
                                type="text"
                                id="itemDescription"
                                v-model="merchManagementStore.itemDescription"
                                class="mb-2 form-control bg-minor rounded-4 w-75"
                                placeholder="Enter description"
                                required
                            />
                        </div>
                    </div>
                    <div class="d-flex flex-row">
                        <div class="col-6">
                            <label
                                for="itemArtist"
                                style="font-size: 13px"
                                class="fw-bold mb-2 mt-3"
                            >
                                Artist
                            </label>
                            <input
                                style="color: rgb(228, 228, 228)"
                                name="itemArtist"
                                id="itemArtist"
                                v-model="merchManagementStore.itemArtist"
                                class="mb-2 form-control bg-minor rounded-4 w-75"
                                disabled
                                required
                            />
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="fw-bold fs-5">Media uploads</div>
                        <span class="opacity-50" style="font-size: 13px">
                            Upload product photos and artwork. Recommended: 2000x2000 PNG/JPEG.
                        </span>
                        <div class="mt-3">
                            <div class="d-flex align-items-center w-100">
                                <input
                                    ref="fileInputRef"
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    class="d-none"
                                    @change="merchManagementStore.handleImagesUpload($event)"
                                />
                                <template v-if="!merchManagementStore.merchFiles.length">
                                    <span
                                        class="opacity-50 fw-bold mt-4"
                                        style="color: rgb(228, 228, 228)"
                                    >
                                        No images uploaded yet...
                                    </span>
                                </template>
                                <template v-else>
                                    <div class="d-flex flex-row w-100 gap-3">
                                        <draggable
                                            v-model="merchManagementStore.merchFiles"
                                            item-key="preview"
                                            class="d-flex flex-row flex-wrap gap-3"
                                        >
                                            <template #item="{ element, index }">
                                                <div class="merch-card">
                                                    <img
                                                        class="merch-preview rounded-4"
                                                        :src="element.preview"
                                                        :alt="`Merch preview ${index + 1}`"
                                                    />
                                                    <span
                                                        v-if="index === 0"
                                                        class="badge mt-2 rounded-5 position-absolute"
                                                        style="
                                                            background-color: rgb(75, 75, 75);
                                                            padding: 1px 7px;
                                                            bottom: 17px;
                                                        "
                                                    >
                                                        Main
                                                    </span>
                                                    <button
                                                        @click.stop="
                                                            merchManagementStore.removeImage(index)
                                                        "
                                                        class="btn remove-btn w-100"
                                                    >
                                                        <img
                                                            src="@/assets/svg/reject.svg"
                                                            alt="remove"
                                                        />
                                                    </button>
                                                </div>
                                            </template>
                                        </draggable>
                                    </div>
                                </template>
                            </div>
                            <div
                                class="w-100 d-flex flex-row align-items-center justify-content-end"
                            >
                                <button class="btn btn-artists" @click="openFilePicker">
                                    Upload images
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="fw-bold fs-5">Variants & Pricing</div>
                        <span class="opacity-50" style="font-size: 13px">
                            Add different sizes, colors and prices for each variant.
                        </span>
                        <div>
                            <div class="variants-table-wrapper mt-4">
                                <div class="variants-table-header px-1 py-2">
                                    <div>Variant</div>
                                    <div>Price</div>
                                    <div>Stock</div>
                                    <div></div>
                                </div>

                                <div class="d-flex flex-column gap-3 mt-3">
                                    <div
                                        v-for="(item, index) in merchManagementStore.merchVariants"
                                        :key="item.id"
                                        class="variant-row rounded-5 px-3"
                                    >
                                        <div class="variant-cell variant-main">
                                            <input
                                                v-model="item.variant_name"
                                                type="text"
                                                name="variantTitle"
                                                class="form-control bg-transparent border-0 shadow-none rounded-4 fw-bold"
                                                placeholder="Title"
                                            />
                                        </div>

                                        <div class="variant-cell">
                                            <input
                                                v-model.number="item.price"
                                                min="0"
                                                name="variantPrice"
                                                class="form-control bg-transparent border-0 rounded-4"
                                                placeholder="Price"
                                            />
                                        </div>

                                        <div class="variant-cell">
                                            <input
                                                v-model.number="item.stock"
                                                min="0"
                                                name="variantStock"
                                                class="form-control bg-transparent border-0 rounded-4"
                                                placeholder="Stock"
                                            />
                                        </div>

                                        <div class="variant-cell w-100 variant-actions">
                                            <button
                                                type="button"
                                                class="btn variant-danger-btn d-flex align-items-center
                                                justify-content-center"
                                                @click="merchManagementStore.removeVariant(index)"
                                            >
                                                <img src="@/assets/svg/reject.svg" alt="delete">
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center w-100 mt-3 variants-footer">
                                    <div class="d-flex align-items-center justify-content-center w-100">
                                        <button
                                            type="button"
                                            class="btn btn-artists px-3"
                                            @click="merchManagementStore.addVariant"
                                        >
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="mt-3 rounded-5 p-3"
                        style="border-top: 1px solid rgba(228, 228, 228, 0.15)"
                    >
                        <div class="w-100 d-flex flex-row justify-content-end">
                            <button
                                @click="merchManagementStore.handleMerchItemUpload"
                                class="btn btn-primary"
                                :disabled="merchManagementStore.isLoading"
                            >
                                Publish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.form-control {
    border-color: rgb(75, 75, 75) !important;
    color: rgb(228, 228, 228) !important;
    max-width: 600px !important;

    &:focus {
        box-shadow: none;
        border-color: #ff2667 !important;
    }
}

.merch-card {
    border: 1px solid rgba(228, 228, 228, 0.15);
    padding: 10px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: start;
    flex-direction: column;
    position: relative;
}

.merch-preview {
    width: 200px;
    height: 200px;
    object-fit: cover;
    display: block;
}

.remove-btn {
    position: absolute;
    border: none;
    border-radius: 15px;
    color: rgb(228, 228, 228);
    line-height: 1;
    width: 200px !important;
    height: 200px !important;
    opacity: 0;
    transition: 0.2s;

    &:hover {
        background-color: rgba(0, 0, 0, 0.2);
        opacity: 1;
    }
}

.variants-table-wrapper {
    width: 100%;
}

.variants-table-header {
    display: grid;
    grid-template-columns: 2.1fr 1.4fr 1fr .1fr;
    column-gap: 16px;
    font-size: 13px;
    font-weight: 700;
    color: rgba(228, 228, 228, 0.8);
}

.variant-row {
    display: grid;
    grid-template-columns: 2.1fr 1.4fr 1fr .1fr;
    column-gap: 16px;
    align-items: center;
    border: 1px solid rgba(228, 228, 228, 0.15);
    background: rgba(255, 255, 255, 0.02);
    height: 60px;
}

.variant-cell {
    min-width: 0;
}

.variant-main :deep(input),
.variant-cell :deep(input) {
    color: rgb(228, 228, 228) !important;
}

.variant-main :deep(input::placeholder),
.variant-cell :deep(input::placeholder) {
    color: rgba(228, 228, 228, 0.35) !important;
}

.variant-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.variant-danger-btn {
    border: 1px solid rgb(158, 23, 63);
    color: rgb(228, 228, 228);
    background: rgb(32,32,32);
    border-radius: 15px;
    height: 30px;
    padding: 4px 12px;
    font-size: 15px;
    display: flex;
    justify-content: center;
    align-items: center;

    &:hover {
        background: rgba(158, 23, 63, 0.5);
        border-color: rgba(158, 23, 63, 0);
    }

    &:active {
        color: rgb(158, 23, 63) !important;
    }
}

.variants-footer {
    min-height: 48px;
}

</style>
