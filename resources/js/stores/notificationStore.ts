import { defineStore } from 'pinia';
import dashboardAxios from '@/api/dashboardAxios';

interface Notification {
    id: string | number;
    read_at: Date | null;
    title?: string;
    message?: string;
    created_at?: string | Date;
}

interface NotificationState {
    notifications: Notification[];
}

export const useNotificationStore = defineStore('notification', {
    state: (): NotificationState => ({
        notifications: [],
    }),
    actions: {
        addNotification(notification: Notification) {
            this.notifications.unshift(notification);
        },
        clearNotifications() {
            this.notifications = [];
        },
        async markAsRead(id: string | number) {
            try {
                // Call backend to mark a single notification as read.
                // Assumption: backend exposes POST /notification/:id/mark-as-read
                await dashboardAxios.post(`notification/${id}/mark-as-read`);

                // Update local store on success
                const notification = this.notifications.find((notification) => notification.id === id);
                if (notification) {
                    notification.read_at = new Date();
                }
            } catch (error) {
                console.error(`Failed to mark notification ${id} as read:`, error);
            }
        },
        async markAsReadAll() {
            try {
                // Call backend to mark all notifications as read.
                // Assumption: backend exposes POST /notification/mark-all-read
                await dashboardAxios.post('notification/read-all');

                // Update local store on success
                this.notifications
                    .filter(nt => nt.read_at == null)
                    .forEach(nt => {
                        nt.read_at = new Date();
                    });
            } catch (error) {
                console.error('Failed to mark all notifications as read:', error);
            }
        },
        async fetchNotifications() {
            try {
                const response = await dashboardAxios.get('notification', {
                    params: { status: 'unread' }
                });
                this.notifications = response.data || [];
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            }
        }
    },
});
