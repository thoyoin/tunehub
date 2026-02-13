import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api'
import { useToast } from "vue-toastification";

export const useArtistStore = defineStore('artistStudio', () => {
    const tracks = ref([])
    const releases = ref([])
    const selectedView = ref('tracks')
    const editingItem = ref(null)

    const toast = useToast()

    const fetchTracks = async () => {
        try {
            const response = await api.get('/api/artists/tracks')

            tracks.value = response.data
        } catch (e) {
            console.error(e)
        }
    }

    const fetchReleases = async () => {
        try {
            const response = await api.get('/api/artists/releases')

            releases.value = response.data
        } catch (e) {
            console.error(e)
        }
    }

    const deleteTrack = async (id) => {
        try {
            await api.delete(`/api/track/${id}`)

            await fetchTracks()
            await fetchReleases()

            toast.success('Track successfully deleted!')
        } catch (e) {
            console.error(e)

            toast.error('Something went wrong.')
        }
    }

    const deleteRelease = async (id) => {
        try {
            await api.delete(`/api/release/${id}`)

            await fetchReleases()
            await fetchTracks()

            toast.success('release successfully deleted!')
        } catch (e) {
            console.error(e)

            toast.error('Something went wrong.')
        }
    }

    const viewTracks = () => {
        selectedView.value = 'tracks'
    }

    const viewReleases = () => {
        selectedView.value = 'releases'
    }

    const pullEditingItem = (item) => {
        editingItem.value = item
    }

     return {
        tracks,
        releases,
        selectedView,
        fetchReleases,
        fetchTracks,
        viewTracks,
        viewReleases,
        deleteTrack,
        deleteRelease,
        pullEditingItem,
        editingItem,
     };
})
