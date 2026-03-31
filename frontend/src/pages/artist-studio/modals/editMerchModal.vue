<script setup lang="ts">
import { useMerchManagementStore } from "@/stores/merchManagement";
import { useToast } from "vue-toastification";
import { ref, watch } from "vue";
import api from "@/lib/api";
import draggable from "vuedraggable";

const merchStore = useMerchManagementStore();
const toast = useToast();

const itemTitle = ref("");
const itemDescription = ref("");

const fileInputRef = ref<HTMLInputElement | null>(null);

const openFilePicker = () => {
    fileInputRef.value?.click();
};

watch(
    () => merchStore.editingMerch,
    (newItem) => {
        if (!newItem) return;

        itemTitle.value = newItem.title;
        itemDescription.value = newItem.description;
    },
    { immediate: true, deep: true },
);

const handleMerchItemUpdate = async () => {
    try {
        const formData = new FormData();

        formData.append("item_title", itemTitle.value);
        formData.append("item_description", itemDescription.value ?? "");
        formData.append(
            "merch_variants",
            JSON.stringify(merchStore.editingMerch?.product_variants ?? []),
        );

        formData.append(
            "existing_images",
            JSON.stringify(merchStore.editingMerch?.product_images ?? []),
        );

        if (merchStore.editingMerchFiles.length) {
            merchStore.editingMerchFiles.forEach((file) => {
                if (file.file) {
                    formData.append("new_images[]", file.file);
                }
            });
        }

        await api.put(`/api/artists/merch/${merchStore.editingMerch?.id}/update`, formData);

        toast.success("Successfully updated");

        await merchStore.fetchArtistMerch();
    } catch (error) {
        console.log(error);

        toast.error("Something went wrong");
    }
};

const handleMerchPublication = async () => {
    await merchStore.handleMerchPublication();

    toast.success('Merch was successfully published')
}
</script>

<template>
    <div
        class="modal fade"
        id="editMerchModal"
        tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog modal-lg p-5">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ merchStore.editingMerch?.title }}</h5>
                </div>
                <div class="modal-body d-flex justify-content-center flex-column">
                    <span class="opacity-50" style="font-size: 13px">
                        Edit information for this merch item.
                    </span>
                    <div>
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
                                    v-model="itemTitle"
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
                                    v-model="itemDescription"
                                    class="mb-2 form-control bg-minor rounded-4 w-75"
                                    placeholder="Enter description"
                                    required
                                />
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-row overflow-x-auto gap-3 mt-4">
                        <input
                            ref="fileInputRef"
                            type="file"
                            accept="image/*"
                            multiple
                            class="d-none"
                            @change="merchStore.handleEditingImagesUpload($event)"
                        />
                        <div class="d-flex flex-row w-100 gap-3">
                            <draggable
                                v-model="merchStore.galleryImages"
                                item-key="galleryKey"
                                class="d-flex flex-row overflow-y-auto gap-3"
                            >
                                <template #item="{ element, index }">
                                    <div class="merch-card">
                                        <img
                                            class="merch-preview rounded-4"
                                            :src="
                                                element.source === 'existing'
                                                    ? element.image_url
                                                    : element.preview
                                            "
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
                                            @click.stop="merchStore.removeImageInEditing(element)"
                                            class="btn remove-btn w-100"
                                        >
                                            <img src="@/assets/svg/reject.svg" alt="remove" />
                                        </button>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                    <div
                        class="w-100 d-flex flex-row align-items-center justify-content-start mt-3"
                    >
                        <button class="btn btn-artists" @click="openFilePicker">
                            Upload images
                        </button>
                    </div>
                    <div class="w-100 text-center mt-2">
                        <span class="badge">{{ merchStore.editingMerch?.status }}</span>
                    </div>
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
                                    v-for="(item, index) in merchStore.editingMerch
                                        ?.product_variants"
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
                                            @click="merchStore.removeVariantInEditing(index)"
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
                                        @click="merchStore.addVariantInEditing()"
                                    >
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-column gap-2 w-100">
                        <div class="d-flex flex-row w-100 justify-content-between">
                            <button
                                data-bs-dismiss="modal"
                                aria-label="Close"
                                class="btn btn-cancel"
                                @click="merchStore.resetEditingMerch()"
                            >
                                Cancel
                            </button>
                            <button
                                class="btn-primary"
                                @click="handleMerchItemUpdate"
                                :disabled="merchStore.isLoading"
                            >
                                Save
                            </button>
                        </div>
                        <div class="d-flex flex-row w-100 justify-content-between">
                            <button
                                class="btn variant-danger-btn"
                                @click="merchStore.handleDeleteMerch()"
                                :disabled="merchStore.isLoading"
                            >
                                Delete
                            </button>
                            <button
                                class="btn-primary"
                                @click="handleMerchPublication"
                                :disabled="
                                merchStore.editingMerch?.status !== 'approved'
                                && merchStore.editingMerch?.status === 'active'
                                || merchStore.isLoading
                                "
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
.loading-overlay {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    transition: 0.4s;
    backdrop-filter: blur(1px);
    z-index: 1;
    pointer-events: none;
}
.variants-table-wrapper {
    width: 100%;
}

.variants-table-header {
    display: grid;
    grid-template-columns: 2.1fr 1.4fr 1fr 0.1fr;
    column-gap: 16px;
    font-size: 13px;
    font-weight: 700;
    color: rgba(228, 228, 228, 0.8);
}

.variant-row {
    display: grid;
    grid-template-columns: 2.1fr 1.4fr 1fr 0.1fr;
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
</style>
