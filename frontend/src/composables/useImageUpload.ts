import {onUnmounted, ref} from "vue";

export function useImageUpload() {
    const previewUrl = ref<string | null>(null);
    const fileToUpload = ref<File | null>(null);

    const handleImageUpload = (event: Event): void => {
        const input = event.target as HTMLInputElement;
        if (!input.files) return

        const file = input.files[0];

        if (!file) return

        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
            previewUrl.value = null
        }

        fileToUpload.value = file;
        previewUrl.value = URL.createObjectURL(file);
        console.log(previewUrl.value);
    }

    onUnmounted((): void => {
        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
            previewUrl.value = null
        }
    })

    return { previewUrl, fileToUpload, handleImageUpload };
}
