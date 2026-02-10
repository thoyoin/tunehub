import { ref } from "vue";


export function useImageUpload() {
    const previewUrl = ref(null);
    const fileToUpload = ref(null);

    const handleImageUpload = (elem) => {
        const file = elem.target.files[0];
        if (!file) return

        if (previewUrl.value) {
            previewUrl.value = null
        }

        fileToUpload.value = file;
        previewUrl.value = URL.createObjectURL(file);

    }

    return { previewUrl, fileToUpload, handleImageUpload };
}
