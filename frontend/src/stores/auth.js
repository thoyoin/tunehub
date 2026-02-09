import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
    const isReady = ref(false);
    const user = ref(null);
    const router = useRouter();
    const loading = ref(false);

    const errors = ref({});

    const fetchUser = async () => {
        try {
            const { data } = await api.get('/api/user');
            user.value = data.user;
            isReady.value = true;
        } catch (error) {
            console.log(error);
        }
    }

    const logout = async () => {
        try {
            await api.delete('/api/logout');

            user.value = null;
        } catch (error) {
            console.error(error);
        }
    }

    const login = async (credentials) => {
        try {
            await api.get('/sanctum/csrf-cookie')
            await api.post('/api/login', credentials)

            await router.push('/')
        } catch (e) {
            console.error(e);
        }
    }

    const register = async (credentials) => {
        loading.value = true;
        try {
            await api.get('/sanctum/csrf-cookie')
            await api.post('/api/register', credentials)

            await fetchUser()
            await router.push('/')
        } catch (e) {
            if (e.response?.status === 422) {
                errors.value = e.response.data.errors
            }
        } finally {
            loading.value = false
        }
    }

    return {fetchUser, user, isReady, logout, login, register, loading, errors};
})
