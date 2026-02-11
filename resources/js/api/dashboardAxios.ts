import axios, { AxiosError, InternalAxiosRequestConfig } from 'axios';
import router from '../router/router';
import { useAuthStore } from '@/stores/auth';
import { useToastStore } from "@/stores/toastStore";

// Create axios instance for authenticated dashboard requests
const dashboardAxios = axios.create({
    baseURL: import.meta.env.VITE_APP_URL + '/api/',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});

dashboardAxios.interceptors.request.use(function (config: InternalAxiosRequestConfig) {
    const authStore = useAuthStore();
    if (authStore.user && authStore.user.token) {
        config.headers.Authorization = `Bearer ${authStore.user.token}`;
        config.headers['X-Member'] = authStore.user.id.toString();
    }
    config.headers.Accept = 'application/json';

    return config;
});

dashboardAxios.interceptors.response.use(
    function (response) {
        return response.data;
    },
    function (error: AxiosError<{ message?: string }>) {
        const toastStore = useToastStore();

        if (error.response?.status === 401) {
            toastStore.addToast(error.response.data?.message || 'Unauthorized', 'error', 3000);
            router.push({ name: 'auth', params: {} });
        } else if (error.response?.status === 400) {
            toastStore.addToast(error.response.data?.message || 'Bad Request', 'error', 3000);
        } else if (error.response?.status === 403) {
            toastStore.addToast(error.response.data?.message || 'Forbidden', 'error', 3000);
        } else if (error.response?.status === 405) {
            toastStore.addToast(error.response.data?.message || 'Method Not Allowed', 'error', 3000);
        } else if (error.response?.status === 422) {
            toastStore.addToast(error.response.data?.message || 'Validation Error', 'error', 3000);
        } else if (error.response?.status === 500) {
            toastStore.addToast(error.response.data?.message || 'Gagal', 'error', 3000);
        } else if (error.response?.status === 419) {
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        return Promise.reject(error);
    }
);

export default dashboardAxios;
