import axios from 'axios'

import { useAuthStore } from "@/stores/auth";

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'Accept': 'application/json',
    }
})

api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const status = error?.response?.status;

        const auth = useAuthStore()

        if (status === 401) {
            await auth.clearAuthState();

            return Promise.reject(error);
        }

        if (status === 419 && !error.config?._retry) {
            error.config._retry = true;

            try {
                await api.get('/sanctum/csrf-cookie');
                return api.request(error.config);
            } catch (e) {
                return Promise.reject(e);
            }
        }

        return Promise.reject(error);
    }
)
export default api
