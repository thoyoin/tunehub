import { defineStore } from 'pinia'
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "vue-toastification";
import api from "@/lib/api";

interface Merch {
    id: string;
    variant: string;
    price: number;
    stock: number;
}

interface ProductVariant {
    created_at: string;
    id: number;
    price: number;
    product_id: number;
    stock: number;
    updated_at: string;
    variant_name: string;
}

interface ArtistMerch {
    slug: string,
    title: string,
    description: string,
    user_id: number,
    status: string,
    cover_url: string,
    currency: string,
    product_variants: ProductVariant[],
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
    const merchVariants = ref<Merch[]>([])

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

    const createEmptyVariant = () => ({
        id: crypto.randomUUID(),
        variant: '',
        price: 0,
        stock: 0,
    });

    const addVariant = () => {
        merchVariants.value?.push(createEmptyVariant());
    };

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

    const removeImage = (index: number) => {
        const item = merchFiles.value[index];
        if (item?.preview) {
            URL.revokeObjectURL(item.preview)
        }

        merchFiles.value.splice(index, 1);
    }

    const clear = () => {
        merchFiles.value.forEach(item => URL.revokeObjectURL(item.preview));
    }

    return { merchFiles, removeImage, handleImagesUpload, clear, itemTitle, itemDescription,
         merchVariants, addVariant, removeVariant, itemArtist, handleMerchItemUpload,
        fetchArtistMerch, artistMerch
    }
})
