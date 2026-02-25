import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api'
import type { User } from '../types/User.js'

export const useAuthStore = defineStore('auth', () => {
    const isReady = ref<boolean>(false);
    const user = ref<User | null>(null);
    const loading = ref<boolean>(false);

    const errors = ref({});

    const fetchUser = async (): Promise<void> => {
        try {
            const { data } = await api.get<User>('/api/user');

            user.value = data.user;
        } catch (error) {
            if (error.response?.status === 401) {
                user.value = null
                return
            }

            throw error;
        } finally {
            isReady.value = true;
        }
    }

    const logout = async (): Promise<void> => {
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

    const login = async (credentials: object): Promise<void> => {
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

    const register = async (credentials: object): Promise<void> => {
        try {
            loading.value = true;

            await api.get('/sanctum/csrf-cookie')
            await api.post('/api/register', credentials)
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
