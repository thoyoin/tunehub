import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api'
import { useDebounceFn } from '@vueuse/core'

export const useAdminPanelStore = defineStore('adminPanel', () => {
    const users = ref([]);
    const viewUser = ref(null);
    const isLoading = ref(false);

    const fetchUsers = useDebounceFn(async (page, search = null) => {
        try {
            const response = await api.get(`/api/admin/users`, {
                params: { page: page, search: search },
            });

            users.value = response.data;
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }, 300)

    const setLoading = async () => {
        isLoading.value = true;
    }

    const setViewUser = (newView) => {
        viewUser.value = newView;
    }

    const deleteUser = async (id) => {
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
