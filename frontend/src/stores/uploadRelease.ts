import { defineStore } from 'pinia';
import { ref} from 'vue';
import { useToast } from "vue-toastification";

import api from "@/lib/api.js";
import { useAuthStore } from "@/stores/auth.js";
import { useArtistStore } from "@/stores/artistStudio.js";

export const useUploadReleaseStore = defineStore('uploadRelease', () => {
    const auth = useAuthStore();
    const toast = useToast();
    const artistStore = useArtistStore();

    const editor = ref<boolean>(false)
    const isCoverUploaded = ref<boolean>(false)
    const coverPreview = ref<string | null>(null)
    const uploadedTracks = ref<{
        originalIndex: number; file: File; title: string
    }[]>([])
    const releaseType = ref<string | null>(null)
    const cover_url = ref<File | null>(null)
    const releaseTitle = ref<string | null>(null)
    const release_date = ref<string | null>(null)
    const label_name = ref<string>('')
    const artist = ref<string | undefined>(auth.user?.username)
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
        artist.value = auth.user?.username
        processing.value = false
    }

    const onFilesUploaded = (e: Event): void => {
        editor.value = true

        const input = e.target as HTMLInputElement
        if (!input.files) return

        uploadedTracks.value = []

        const selectedFiles = Array.from(input.files)

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

            if (!releaseTitle.value || !release_date.value || !artist.value || !releaseType.value) {
                toast.error('Missing required release fields')
                return
            }
            formData.append('label_name', label_name.value)
            formData.append('releaseTitle', releaseTitle.value)
            formData.append('type', releaseType.value)
            formData.append('release_date', release_date.value)
            formData.append('artist', artist.value)

            if (cover_url.value) {
                formData.append('cover_url', cover_url.value, cover_url.value.name)
            }

            await api.post('/api/track', formData)

            await artistStore.fetchReleases()
            await artistStore.fetchTracks()

            toast.success('Release uploaded successfully!')
        } catch (e) {
            console.error(e)

            toast.error('Something went wrong.')
        } finally {
            processing.value = false
        }
    }

    const setPreview = (e: Event): void => {
        const input = e.target as HTMLInputElement
        if (!input.files) return

        const file = input.files[0];

        if (file) {
            coverPreview.value = URL.createObjectURL(file);
            cover_url.value = file
        }
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
        label_name,
        $reset
    }
})
