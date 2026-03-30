import { defineStore } from 'pinia'
import { computed, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "vue-toastification";
import api from "@/lib/api";
import type { ArtistMerch } from "@/types/ArtistMerch";

interface NewMerchVariant {
    variant_name: string;
    price: number;
    stock: number;
}

export const useMerchManagementStore = defineStore('merchManagement', () => {
    const auth = useAuthStore();
    const toast = useToast();

    const isLoading = ref(false);
    const artistMerch = ref<ArtistMerch[] | null>(null);
    const merchFiles = ref<{ file: File; preview: string }[]>([]);
    const itemTitle = ref<string>('')
    const itemDescription = ref<string>('')
    const itemArtist = ref<string>(auth.user?.username!)
    const merchVariants = ref<NewMerchVariant[]>([])
    const showMerchUploadModal = ref<boolean>(false)
    const uploadModalData = ref<{
        title: string,
        message: string,
    } | null>(null);

    const editingMerch = ref<ArtistMerch | null>(null)
    const editingMerchFiles = ref<{ file: File | null; preview: string }[]>([]);

    const galleryImages = computed(() => {
        const existingImages = (editingMerch.value?.product_images ?? []).map(image => ({
            ...image,
            galleryKey: `existing-${image.id}`,
            preview: image.image_url,
            source: "existing",
            isExisting: true,
        }));

        const newImages = editingMerchFiles.value.map((image, index) => ({
            ...image,
            galleryKey: `new-${index}-${image.preview}`,
            source: "new",
            isExisting: false,
        }));

        return [...existingImages, ...newImages];
    });

    const fetchArtistMerch = async () => {
        try {
            isLoading.value = true;

            const response = await api.get<{
                merch: ArtistMerch[];
            }>('/api/artists/merch')

            artistMerch.value = response.data.merch

            console.log(response.data)
        } catch (error) {
            console.log(error)
        } finally {
            isLoading.value = false;
        }
    }

    const handleMerchItemUpload = async () => {
        try {
            isLoading.value = true

            const formData = new FormData()

            formData.append("item_title", itemTitle.value)
            formData.append("item_description", itemDescription.value)
            formData.append("merch_variants", JSON.stringify(merchVariants.value))

            merchFiles.value.forEach(file => {
                formData.append("images[]", file.file)
            })

            await api.post('/api/artists/merch/drop', formData)

            await fetchArtistMerch()

            uploadModalData.value = {
                title: 'Merch was successfully uploaded',
                message: 'Your product has been uploaded and submitted for moderation. After verification, ' +
                    'it will be available to users.'
            }
            showMerchUploadModal.value = true
        } catch (error) {
            console.log(error)

            toast.error('Something went wrong!')
        } finally {
            isLoading.value = false
        }
    }

    const handleDeleteMerch = async () => {
        try {
            isLoading.value = true

            const response = await api.delete(
                `/api/artists/merch/${editingMerch.value?.id}/delete`
            )

            toast.success('Merch successfully deleted.')

            await fetchArtistMerch();
        } catch (e) {
            console.log(e)

            toast.error('Something went wrong.')
        } finally {
            isLoading.value = false
        }
    }

    const removeVariant = (index: number) => {
        merchVariants.value?.splice(index, 1);
    };

    const removeVariantInEditing = (index: number) => {
        editingMerch.value?.product_variants.splice(index, 1);
        console.log(editingMerch.value?.product_variants)
    }

    const createEmptyVariant = () => ({
        variant_name: '',
        price: 0,
        stock: 0,
    });

    const addVariant = () => {
        merchVariants.value?.push(createEmptyVariant());
    };

    const addVariantInEditing = () => {
        editingMerch.value?.product_variants.push(createEmptyVariant());
    }

    const handleImagesUpload = (e: Event) => {
        const input = e.target as HTMLInputElement;
        const files = Array.from(input.files ?? [])

        if (!files.length) return

        const remainingSlots = Math.max(0, 5 - merchFiles.value.length)
        const filesToAdd = files.slice(0, remainingSlots)

        filesToAdd.forEach(file => {
            merchFiles.value.push({
                file,
                preview: URL.createObjectURL(file),
            })
        });

        input.value = "";
    }

    const handleEditingImagesUpload = (e: Event) => {
        const input = e.target as HTMLInputElement;
        const files = Array.from(input.files ?? [])

        if (!files.length) return

        const remainingSlots = Math.max(0, 5 - editingMerchFiles.value.length)
        const filesToAdd = files.slice(0, remainingSlots)

        filesToAdd.forEach(file => {
            editingMerchFiles.value.push({
                file,
                preview: URL.createObjectURL(file),
            })
        });
        input.value = "";
    }

    const removeImage = (index: number) => {
        const item = merchFiles.value[index];
        if (item?.preview) {
            URL.revokeObjectURL(item.preview)
        }

        merchFiles.value.splice(index, 1);
    }

    const removeImageInEditing = (imageToRemove: any) => {
        if (imageToRemove.source === 'existing') {
            editingMerch.value!.product_images = editingMerch.value!.product_images.filter(
                image => image.id !== imageToRemove.id
            );
            return;
        }

        const index = editingMerchFiles.value.findIndex(
            image => image.preview === imageToRemove.preview
        );

        if (index === -1) return;

        const item = editingMerchFiles.value[index];

        if (item?.preview.startsWith("blob:")) {
            URL.revokeObjectURL(item?.preview);
        }

        editingMerchFiles.value.splice(index, 1);
    }

    const setEditingMerch = (item: ArtistMerch) => {
        editingMerch.value = {
            ...item,
            product_variants: item.product_variants.map(variant => ({
                ...variant
            })),
            product_images: item.product_images.map(image => ({
                ...image
            })),
        };
    }

    const clear = () => {
        merchFiles.value.forEach(item => URL.revokeObjectURL(item.preview));
    }

    const resetEditingMerch = () => {
        editingMerchFiles.value.forEach(item => {
            if (item.preview.startsWith('blob:')) {
                URL.revokeObjectURL(item.preview);
            }
        });

        editingMerchFiles.value = [];
        editingMerch.value = null;
    }

    return { merchFiles, removeImage, handleImagesUpload, clear, itemTitle, itemDescription,
         merchVariants, addVariant, removeVariant, itemArtist, handleMerchItemUpload,
        fetchArtistMerch, artistMerch, editingMerch, setEditingMerch, addVariantInEditing,
        removeVariantInEditing, resetEditingMerch, editingMerchFiles, handleEditingImagesUpload,
        removeImageInEditing, galleryImages, handleDeleteMerch, isLoading, showMerchUploadModal,
        uploadModalData,
    }
})
