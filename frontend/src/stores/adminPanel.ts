import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api'
import { useDebounceFn } from '@vueuse/core'
import type { PaginatedResponse } from '../types/PaginatedResponse.js'
import type { User } from '../types/User.js'

export const useAdminPanelStore = defineStore('adminPanel', () => {
    const users = ref<User[]>([]);
    const viewUser = ref<User | null>(null);
    const isLoading = ref<boolean>(false);

    const fetchUsers = useDebounceFn(async (page: number = 1, search: string | null): Promise<void> => {
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

    const setLoading = (): void => {
        isLoading.value = true;
    }

    const setViewUser = (newView: User | null): void => {
        viewUser.value = newView;
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

    return { users, fetchUsers, setViewUser, viewUser, deleteUser, isLoading, setLoading };
});
