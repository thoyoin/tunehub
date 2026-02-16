import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api'

export const useAuthStore = defineStore('auth', () => {
    const isReady = ref(false);
    const user = ref(null);
    const loading = ref(false);

    const errors = ref({});

    const fetchUser = async () => {
        try {
            const { data } = await api.get('/api/user');
            user.value = data.user;
        } catch (error) {
            console.log(error);
        } finally {
            isReady.value = true;
        }
    }

    const logout = async () => {
        try {
            loading.value = true;

            await api.delete('/api/logout');

            user.value = null;
        } catch (e) {
            console.error(e);

            throw e
        } finally {
            loading.value = false;
        }
    }

    const login = async (credentials) => {
        try {
            loading.value = true;

            await api.get('/sanctum/csrf-cookie')
            await api.post('/api/login', credentials)
        } catch (e) {
            console.error(e);

            if (e.response?.status === 422) {
                errors.value = e.response.data.errors
            }

            throw e
        } finally {
            loading.value = false;
        }
    }

    const register = async (credentials) => {
        try {
            loading.value = true;

            await api.get('/sanctum/csrf-cookie')
            await api.post('/api/register', credentials)

            await fetchUser()
        } catch (e) {
            console.log(e)

            if (e.response?.status === 422) {
                errors.value = e.response.data.errors
            }

            throw e;
        } finally {
            loading.value = false
        }
    }

    return {fetchUser, user, isReady, logout, login, register, loading, errors};
})
