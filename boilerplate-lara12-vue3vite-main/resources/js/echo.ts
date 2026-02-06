import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';
import { useAuthStore } from './stores/auth';

declare global {
    interface Window {
        Pusher: typeof Pusher;
        Echo: Echo<any>;
    }
}

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: parseInt(import.meta.env.VITE_REVERB_PORT) || 8080,
    wssPort: parseInt(import.meta.env.VITE_REVERB_PORT) || 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    cluster: 'mt1', // Required by Pusher client (not used by Reverb)
    disableStats: true,

    // Authorization for private/presence channels
    authorizer: (channel: any) => {
        return {
            authorize: (socketId: string, callback: Function) => {
                console.log('🔐 Authorizing channel:', channel.name, 'socket:', socketId);

                const authStore = useAuthStore();

                // Use /api/broadcasting/auth which is protected by auth:sanctum middleware
                axios.post(`${import.meta.env.VITE_APP_URL}/api/broadcasting/auth`, {
                    socket_id: socketId,
                    channel_name: channel.name
                }, {
                    headers: {
                        'Authorization': `Bearer ${authStore.user?.token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then((response: any) => {
                    console.log('✅ Channel authorized:', channel.name, response.data);
                    callback(null, response.data);
                })
                .catch((error: any) => {
                    console.error('❌ Authorization failed:', error.response?.status, error.response?.data);
                    callback(error);
                });
            }
        };
    },
});

export default window.Echo;
