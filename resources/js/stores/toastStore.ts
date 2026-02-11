import { defineStore } from "pinia";
import { ref } from "vue";

type ToastType = 'success' | 'error' | 'warning' | 'info';

interface Toast {
    id: number;
    message: string;
    type: ToastType;
    duration: number;
    title: string;
}

export const useToastStore = defineStore("toast", () => {
    const toasts = ref<Toast[]>([]);

    const addToast = (
        message: string,
        type: ToastType = "success",
        duration: number = 3000,
        title: string = ""
    ) => {
        console.log("Toast message:", message);

        const id = Date.now();
        toasts.value.push({ id, message, type, duration, title });

        setTimeout(() => {
            removeToast(id);
        }, duration);
    };

    const removeToast = (id: number) => {
        toasts.value = toasts.value.filter((toast) => toast.id !== id);
    };

    return { toasts, addToast, removeToast };
});
