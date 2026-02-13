import { defineStore } from 'pinia';
import {computed, ref} from 'vue';
import api from "@/lib/api.js";
import { useAuthStore } from "@/stores/auth.js";
import { useToast } from "vue-toastification";
import { useArtistStore } from "@/stores/artistStudio.js";

export const useUploadReleaseStore = defineStore('uploadRelease', () => {
    const auth = useAuthStore();
    const toast = useToast();
    const artistStore = useArtistStore();

    const editor = ref(false)
    const isCoverUploaded = ref(false)
    const coverPreview = ref(null)
    const uploadedTracks = ref([])
    const releaseType = ref(null)
    const cover_url = ref(null)
    const releaseTitle = ref(null)
    const release_date = ref(null)
    const artist = ref(auth.user.username)
    const processing = ref(false)

    const onFilesUploaded = (e) => {
        editor.value = true

        const selectedFiles = Array.from(e.target.files)

        uploadedTracks.value = selectedFiles.map((file, index) => ({
            originalIndex: index,
            file: file,
            title: file.name
        }));
    }

    const handleReleaseUpload = async () => {
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

    const setPreview = (e) => {
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
    }
})
