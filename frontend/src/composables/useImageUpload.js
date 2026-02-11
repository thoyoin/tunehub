import {onUnmounted, ref} from "vue";

export function useImageUpload() {
    const previewUrl = ref(null);
    const fileToUpload = ref(null);

    const handleImageUpload = (elem) => {
        const file = elem.target.files[0];
        if (!file) return

        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
            previewUrl.value = null
        }

        fileToUpload.value = file;
        previewUrl.value = URL.createObjectURL(file);

    }

    onUnmounted(() => {
        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
            previewUrl.value = null
        }
    })

    return { previewUrl, fileToUpload, handleImageUpload };
}
