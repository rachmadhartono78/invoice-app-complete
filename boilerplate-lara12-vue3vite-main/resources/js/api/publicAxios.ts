import axios, { AxiosError } from 'axios';

// Create axios instance for public API requests (no authentication required)
const publicAxios = axios.create({
    baseURL: import.meta.env.VITE_APP_URL + '/api/',
    withCredentials: false, // No credentials for public requests
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});

publicAxios.interceptors.response.use(
    function (response) {
        return response.data;
    },
    function (error: AxiosError<{ message?: string }>) {
        // Simple error handling for public requests
        if (error.response?.status === 500) {
            console.error('Server error:', error.response.data?.message);
        } else if (error.response?.status === 422) {
            console.error('Validation error:', error.response.data?.message);
        }

        return Promise.reject(error);
    }
);

export default publicAxios;
