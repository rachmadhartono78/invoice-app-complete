/**
 * Store State Type Definitions
 *
 * These interfaces represent the state structure of Pinia stores.
 */

import type { User, Notification } from './models';

export interface AuthState {
  user: User | null;
}

export interface ThemeState {
  isDark: boolean;
}

export interface NotificationState {
  notifications: Notification[];
  unreadCount: number;
}

export interface ToastState {
  toasts: Toast[];
}

export interface Toast {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  message: string;
  duration?: number;
}

export interface LoadingState {
  isLoading: boolean;
}
