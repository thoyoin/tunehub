<script setup lang="ts">
import { useMerchStore } from "@/stores/AdminPanel/merch";
import { useRouter } from "vue-router";

const adminMerchStore = useMerchStore();
const router = useRouter();

</script>

<template>
    <div
        class="modal fade"
        id="merchViewModal"
        tabindex="-1"
        aria-labelledby="merchViewModalLabel"
        aria-hidden="true"
    >
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <h1 class="modal-title fs-5" id="releaseViewModalLabel">Merch Details</h1>
             </div>
             <div class="modal-body">
                 <div class="d-flex flex-row overflow-x-auto gap-3">
                     <template v-for="image in adminMerchStore.viewingMerch?.product_images">
                         <div class="merch-card">
                             <img
                                 class="merch-preview rounded-4"
                                 :src="image.image_url"
                                 alt="cover"
                             />
                         </div>
                     </template>
                 </div>
                 <div class="d-flex flex-column w-100 align-items-center mt-3">
                     <div class="d-flex flex-row align-items-end mb-3">
                         <span class="fw-bold fs-5 me-2">
                             {{ adminMerchStore.viewingMerch?.title }}
                         </span>
                         <span
                             data-bs-dismiss="modal"
                             @click="router.push({
                                name: 'artist',
                                params: {
                                    artistId: adminMerchStore.viewingMerch?.user_id
                                }
                            })"
                             style="font-size: 14px" class="opacity-50 link-btn"
                         >
                             by {{ adminMerchStore.viewingMerch?.user?.username }}
                         </span>
                     </div>
                     <div class="variants-table-wrapper mt-4">
                         <div class="variants-table-header px-1 py-2">
                             <div>Variant</div>
                             <div>Price</div>
                             <div>Stock</div>
                             <div></div>
                         </div>
                         <template v-if="!adminMerchStore.viewingMerch?.product_variants.length">
                             <div class="d-flex w-100 justify-content-center">
                                 <span class="opacity-50" style="font-size: 14px">
                                     No merch variants...
                                 </span>
                             </div>
                         </template>
                         <div class="d-flex flex-column gap-3 mt-3">
                             <div
                                 v-for="(item, index) in adminMerchStore.viewingMerch
                                        ?.product_variants"
                                 :key="item.id"
                                 class="variant-row rounded-4"
                             >
                                 <div class="variant-cell variant-main">
                                     <span
                                         class="fw-bold ps-3"
                                     >
                                         {{ item.variant_name }}
                                     </span>
                                 </div>
                                 <div class="variant-cell">
                                     <span
                                         class="fw-bold"
                                     >
                                         ${{ item.price }}
                                     </span>
                                 </div>
                                 <div class="variant-cell">
                                     <span
                                         class="fw-bold"
                                     >
                                         {{ item.stock }}
                                     </span>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button
                     @click="adminMerchStore.updateMerchStatus('rejected')"
                     class="btn btn-cancel"
                 >
                     Reject
                 </button>
                 <button
                     @click="adminMerchStore.updateMerchStatus('approved')"
                     class="btn btn-primary"
                 >
                     Approve
                 </button>
             </div>
         </div>
     </div>
    </div>
</template>

<style scoped>
.merch-card {
    border: 1px solid rgba(228, 228, 228, 0.15);
    padding: 10px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: row;
    position: relative;
}

.merch-preview {
    width: 200px;
    height: 200px;
    object-fit: cover;
    display: block;
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

.variants-footer {
    min-height: 48px;
}

.link-btn {
    &:hover {
        text-decoration: underline;
    }
}
</style>
