import { defineStore } from 'pinia';
import { ref } from 'vue';

import api from '@/lib/api'

import type { User } from "@/types/User"
import type { AxiosError } from "axios"

let fetchUserPromise: Promise<void> | null = null;

export const useAuthStore = defineStore('auth', () => {
    const isReady = ref<boolean>(false);
    const user = ref<User | null>(null);
    const loading = ref<boolean>(false);
    const bootstrapError = ref<unknown | null>(null);

    const errors = ref({});

    const fetchUser = (force?: boolean): Promise<void> => {
        if (force) {
            fetchUserPromise = null;
            isReady.value = false;
        }

        if (fetchUserPromise) {
            return fetchUserPromise;
        }

        fetchUserPromise = (async () => {
            try {
                bootstrapError.value = null;

                const response = await api.get<{ user: User }>("/api/user");

                user.value = response.data.user;
            } catch (e) {
                const error = e as AxiosError;

                if (error.response?.status === 401) {
                    user.value = null;
                    return;
                }

                user.value = null;
                bootstrapError.value = error;
            } finally {
                isReady.value = true;
                fetchUserPromise = null;
                errors.value = {};
            }
        })();

        return fetchUserPromise;
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
            const error = e as AxiosError<{ errors: Record<string, string[]> }>;

            console.error(error);

            if (error.response?.status === 422) {
                errors.value = error.response.data.errors
            }

            throw error
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
            const error = e as AxiosError<{ errors: Record<string, string[]> }>;

            console.log(e)

            if (error.response?.status === 422) {
                errors.value = error.response.data.errors
            }

            throw e;
        } finally {
            loading.value = false
        }
    }

    const clearAuthState = () => {
        isReady.value = false;
        user.value = null;
        bootstrapError.value = null;
        fetchUserPromise = null;
    }

    return {
        fetchUser, user, isReady, bootstrapError, logout, login, register, loading, errors,
        clearAuthState
    };
})
