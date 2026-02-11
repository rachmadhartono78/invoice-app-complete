<template>
    <div class="container mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notification Testing</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Test real-time notifications using Laravel Reverb private channels
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Send Notification Form -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    Send Test Notification
                </h2>

                <form @submit.prevent="sendNotification" class="space-y-4">
                    <div>
                        <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            User ID
                        </label>
                        <input
                            type="text"
                            id="user_id"
                            v-model="form.user_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Enter user ID or leave empty for current user"
                        />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Current user: {{ currentUser?.id }} ({{ currentUser?.name }})
                        </p>
                    </div>

                    <div>
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Title
                        </label>
                        <input
                            type="text"
                            id="title"
                            v-model="form.title"
                            required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Notification title"
                        />
                    </div>

                    <div>
                        <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Message
                        </label>
                        <textarea
                            id="message"
                            v-model="form.message"
                            required
                            rows="4"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Notification message"
                        ></textarea>
                    </div>

                    <div>
                        <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Type
                        </label>
                        <select
                            id="type"
                            v-model="form.type"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="notification">Notification</option>
                            <option value="toast">Toast</option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="!loading">Send Notification</span>
                        <span v-else>Sending...</span>
                    </button>
                </form>
            </div>

            <!-- Channel Status -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    WebSocket Status
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Connection Status
                        </label>
                        <div class="flex items-center gap-2">
                            <div :class="[
                                'w-3 h-3 rounded-full',
                                connectionStatus === 'connected' ? 'bg-green-500' :
                                connectionStatus === 'connecting' ? 'bg-yellow-500' : 'bg-red-500'
                            ]"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 capitalize">
                                {{ connectionStatus }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Private Channel
                        </label>
                        <code class="block px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 rounded border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200">
                            App.Models.User.{{ currentUser?.id }}
                        </code>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Reverb Config
                        </label>
                        <div class="text-sm space-y-1">
                            <p class="text-gray-700 dark:text-gray-300">
                                <span class="font-medium">Host:</span> {{ reverbConfig.host }}
                            </p>
                            <p class="text-gray-700 dark:text-gray-300">
                                <span class="font-medium">Port:</span> {{ reverbConfig.port }}
                            </p>
                            <p class="text-gray-700 dark:text-gray-300">
                                <span class="font-medium">TLS:</span> {{ reverbConfig.forceTLS ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    </div>

                    <div v-if="lastNotification" class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Last Received
                        </label>
                        <div class="text-sm space-y-1 bg-green-50 dark:bg-green-900/20 p-3 rounded border border-green-200 dark:border-green-800">
                            <p class="font-medium text-green-800 dark:text-green-300">
                                {{ lastNotification.title }}
                            </p>
                            <p class="text-green-700 dark:text-green-400">
                                {{ lastNotification.message }}
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-500">
                                {{ formatTime(lastNotification.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications Log -->
        <div class="mt-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Recent Notifications
            </h2>

            <div v-if="recentNotifications.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                No notifications received yet. Send a test notification to see it appear here.
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="notification in recentNotifications"
                    :key="notification.id"
                    class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600"
                >
                    <div :class="[
                        'w-2 h-2 mt-2 rounded-full flex-shrink-0',
                        notification.type === 'success' ? 'bg-green-500' :
                        notification.type === 'error' ? 'bg-red-500' :
                        notification.type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
                    ]"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ notification.title }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ notification.message }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                            {{ formatTime(notification.created_at) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useNotificationStore } from '@/stores/notificationStore';
import { useToastStore } from '@/stores/toastStore';
import dashboardAxios from '@/api/dashboardAxios';

interface TestNotification {
    id: string | number;
    title: string;
    message: string;
    type: string;
    created_at: Date;
}

const authStore = useAuthStore();
const notificationStore = useNotificationStore();
const toastStore = useToastStore();

const form = ref({
    user_id: '',
    title: 'Test Notification',
    message: 'This is a test notification from the notification testing page.',
    type: 'notification'
});

const loading = ref(false);
const connectionStatus = ref('connecting');
const lastNotification = ref<TestNotification | null>(null);
const recentNotifications = ref<TestNotification[]>([]);

const currentUser = computed(() => authStore.user);

const reverbConfig = computed(() => ({
    host: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    port: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https'
}));

const sendNotification = async () => {
    loading.value = true;

    try {
        const payload = {
            user_id: form.value.user_id || currentUser.value?.id,
            title: form.value.title,
            message: form.value.message,
            type: form.value.type
        };

        await dashboardAxios.post('/v1/settings/notifications/test', payload);

        toastStore.addToast('Test notification sent successfully!', 'success');

        // Reset form except user_id
        form.value.title = 'Test Notification';
        form.value.message = 'This is a test notification from the notification testing page.';
        form.value.type = 'notification';
    } catch (error: any) {
        console.error('Error sending notification:', error);
        toastStore.addToast(error.response?.data?.message || 'Failed to send notification', 'error');
    } finally {
        loading.value = false;
    }
};

const formatTime = (date: Date | string | undefined) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleString();
};

// Listen for notifications
let echoChannel: any = null;

onMounted(() => {
    if (currentUser.value?.id && window.Echo) {
        const channelName = `App.Models.User.${currentUser.value.id}`;

        // Check Echo connection status
        window.Echo.connector.pusher.connection.bind('state_change', (states: any) => {
            connectionStatus.value = states.current;
        });

        connectionStatus.value = window.Echo.connector.pusher.connection.state;

        // // Listen to private channel
        // echoChannel = window.Echo.private(channelName)
        //     .notification((notification: any) => {
        //         const newNotification: TestNotification = {
        //             id: notification.id || Date.now(),
        //             title: notification.title || 'New Notification',
        //             message: notification.message || notification.body || '',
        //             type: notification.type || 'info',
        //             created_at: new Date()
        //         };

        //         lastNotification.value = newNotification;
        //         recentNotifications.value.unshift(newNotification);

        //         // Keep only last 10 notifications
        //         if (recentNotifications.value.length > 10) {
        //             recentNotifications.value = recentNotifications.value.slice(0, 10);
        //         }

        //         console.log('Test page received notification:', newNotification);
        //     });

        console.log(`Notification test page listening on: ${channelName}`);
    }
});

onUnmounted(() => {
    if (echoChannel) {
        window.Echo.leave(`App.Models.User.${currentUser.value?.id}`);
    }
});
</script>
