import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useDebounceFn } from '@vueuse/core'

import api from '@/lib/api'

import type { PaginatedResponse } from '@/types/PaginatedResponse'
import type {User, UserDetailed} from '@/types/User'

export const useUsersStore = defineStore('users', () => {
    const users = ref<PaginatedResponse<User> | null>(null);
    const search = ref<string | null>(null)
    const viewUser = ref<User | null>(null);
    const viewUserDetailed = ref<UserDetailed | null>(null);
    const isLoading = ref<boolean>(false);

    const fetchUsers = useDebounceFn(
        async (page: number = 1, search: string | null = null): Promise<void> => {

        try {
            const response = await api.get<PaginatedResponse<User>>(`/api/admin/users`, {
                params: { page: page, search: search },
            });

            users.value = response.data;
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }, 300)

    const fetchUserDetails = async () => {
        try {
            isLoading.value = true;

            const response = await api.get(`/api/admin/users/${viewUser.value?.id}`);

            viewUserDetailed.value = response.data;
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }

    const setLoading = (): void => {
        isLoading.value = true;
    }

    const setViewUser = async (newView: User | null) => {
        viewUser.value = newView;

        if (!viewUserDetailed || viewUserDetailed.value?.id !== viewUser.value?.id) {
            await fetchUserDetails();
        }
    }

    const deleteUser = async (id: number): Promise<void> => {
        try {
            isLoading.value = true;

            await api.delete(`/api/admin/users/${id}/delete`);

            await fetchUsers();
        } catch (e) {
            console.log(e);
        } finally {
            isLoading.value = false;
        }
    }

    return {
        users, fetchUsers, setViewUser, viewUser, deleteUser, isLoading, setLoading, search,
        fetchUserDetails, viewUserDetailed
    };
});
