import { defineStore } from 'pinia'
import { computed, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "vue-toastification";
import api from "@/lib/api";

interface NewMerchVariant {
    variant_name: string;
    price: number;
    stock: number;
}

interface EditingProductVariant {
    id?: number | string;
    variant_name: string;
    price: number;
    stock: number;
}

interface ProductImage {
    id: number;
    product_id: number;
    image_url: string;
    created_at: string;
    updated_at: string;
}

interface ArtistMerch {
    id: number,
    slug: string,
    title: string,
    description: string,
    user_id: number,
    status: string,
    cover_url: string,
    currency: string,
    product_variants: EditingProductVariant[],
    product_images: ProductImage[],
}

export const useArtistMerchStore = defineStore('artistMerch', () => {
    const auth = useAuthStore();
    const toast = useToast();

    const isLoading = ref(false);
    const artistMerch = ref<ArtistMerch[] | null>(null);
    const merchFiles = ref<{ file: File; preview: string }[]>([]);
    const itemTitle = ref<string>('')
    const itemDescription = ref<string>('')
    const itemArtist = ref<string>(auth.user?.username!)
    const merchVariants = ref<NewMerchVariant[]>([])

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

            toast.success('Merch successfully uploaded!')
        } catch (error) {
            console.log(error)

            toast.error('Something went wrong!')
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
        console.log(galleryImages.value)

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
        editingMerch.value = item
    }

    const clear = () => {
        merchFiles.value.forEach(item => URL.revokeObjectURL(item.preview));
    }

    const resetEditingMerch = () => {
        editingMerch.value = null;
    }

    return { merchFiles, removeImage, handleImagesUpload, clear, itemTitle, itemDescription,
         merchVariants, addVariant, removeVariant, itemArtist, handleMerchItemUpload,
        fetchArtistMerch, artistMerch, editingMerch, setEditingMerch, addVariantInEditing,
        removeVariantInEditing, resetEditingMerch, editingMerchFiles, handleEditingImagesUpload,
        removeImageInEditing, galleryImages
    }
})
