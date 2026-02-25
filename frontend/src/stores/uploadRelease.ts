import { defineStore } from 'pinia';
import { ref} from 'vue';
import api from "@/lib/api.js";
import { useAuthStore } from "@/stores/auth.ts";
import { useToast } from "vue-toastification";
import { useArtistStore } from "@/stores/artistStudio.ts";

export const useUploadReleaseStore = defineStore('uploadRelease', () => {
    const auth = useAuthStore();
    const toast = useToast();
    const artistStore = useArtistStore();

    const editor = ref<boolean>(false)
    const isCoverUploaded = ref<boolean>(false)
    const coverPreview = ref<File | null>(null)
    const uploadedTracks = ref<File[]>([])
    const releaseType = ref<string | null>(null)
    const cover_url = ref<File | null>(null)
    const releaseTitle = ref<string | null>(null)
    const release_date = ref<string | null>(null)
    const artist = ref<string>(auth.user.username)
    const processing = ref<boolean>(false)

    function $reset(): void {
        editor.value = false
        isCoverUploaded.value = false
        coverPreview.value = null
        uploadedTracks.value = []
        releaseType.value = null
        cover_url.value = null
        releaseTitle.value = null
        release_date.value = null
        artist.value = auth.user.username
        processing.value = false
    }

    const onFilesUploaded = (e: Element): void => {
        editor.value = true

        const selectedFiles = Array.from(e.target.files)

        uploadedTracks.value = selectedFiles.map((file: File, index: number) => ({
            originalIndex: index,
            file: file,
            title: file.name
        }));
    }

    const handleReleaseUpload = async (): Promise<void> => {
        try {
            processing.value = true;

            const formData = new FormData()

            releaseType.value = uploadedTracks.value.length === 1 ? 'single' : 'album'

            uploadedTracks.value.forEach(track => {
                formData.append('audio_url[]', track.file)
                formData.append('title[]', track.title)
            })

            formData.append('releaseTitle', releaseTitle.value)
            formData.append('cover_url', cover_url.value)
            formData.append('type', releaseType.value)
            formData.append('release_date', release_date.value)
            formData.append('artist', artist.value)

            await api.post('/api/track', formData)

            await artistStore.fetchReleases()
            await artistStore.fetchTracks()

            toast.success('release upload successfully!')
        } catch (e) {
            console.error(e)

            toast.error('Something went wrong.')
        } finally {
            processing.value = false

        }
    }

    const setPreview = (e): void => {
        const file = e.target.files[0];

        coverPreview.value = URL.createObjectURL(file);
        cover_url.value = file
    }

    return {
        editor,
        uploadedTracks,
        releaseType,
        handleReleaseUpload,
        isCoverUploaded,
        coverPreview,
        releaseTitle,
        onFilesUploaded,
        setPreview,
        artist,
        release_date,
        processing,
        $reset
    }
})
