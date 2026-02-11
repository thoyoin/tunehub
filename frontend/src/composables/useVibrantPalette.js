import { Vibrant } from 'node-vibrant/browser'
import { ref } from 'vue'

export function useVibrantPalette() {
    const palette = ref(null);

    const getCoverPalette = async (imageUrl) => {
        try {
            palette.value = await Vibrant.from(imageUrl).getPalette()
        } catch (error) {
            console.log(error)
        }
    }

    return { palette, getCoverPalette }
}
