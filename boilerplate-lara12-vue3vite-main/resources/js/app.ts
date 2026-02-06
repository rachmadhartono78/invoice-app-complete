import './bootstrap';
import 'flowbite';
import { createApp, App as VueApp } from 'vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import App from './App.vue';
import router from './router/router.ts';
// Dashboard axios instance with authentication (imported for backward compatibility)
// For new code, prefer: import dashboardAxios from '@/api/dashboardAxios'
import axios from './interceptor.ts';
import VueAxios from 'vue-axios';
import globalFunctions from '@/composables/globalFunctions.ts';
import dialog from '@/components/shared/dialog/ConfirmationDialog.vue';
import Autocomplete from '@/components/molecules/form/Autocomplete.vue';
import Loader from '@/components/atoms/Loader.vue';
import NoData from '@/components/atoms/NoData.vue';
import EmptyData from '@/components/atoms/EmptyData.vue';

// Button components
import BaseButton from '@/components/atoms/BaseButton.vue';
import PrimaryButton from '@/components/atoms/button/PrimaryButton.vue';
import SecondaryButton from '@/components/atoms/button/SecondaryButton.vue';
import ErrorButton from '@/components/atoms/button/ErrorButton.vue';
import Stepper from './components/molecules/form/Stepper.vue';
import Timeline from './components/molecules/timeline/Timeline.vue';

// Third party
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import TableMolecules from './components/molecules/TableMolecules.vue';
import VueApexCharts from "vue3-apexcharts";

// Global component registration
import registerGlobalComponents from "./registerGlobalComponents";

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

const app: VueApp = createApp(App);
registerGlobalComponents(app);

// Register commonly used components globally
app.component('confirmation-dialog', dialog);
app.component("autocomplete", Autocomplete);
app.component("loader", Loader);
app.component("no-data", NoData);
app.component("empty-data", EmptyData);
app.component("custom-button", BaseButton);
app.component("btn-primary", PrimaryButton);
app.component("btn-secondary", SecondaryButton);
app.component("btn-error", ErrorButton);
app.component("stepper", Stepper);
app.component("timeline", Timeline);
app.component("datepicker", VueTailwindDatepicker);
app.component("table-molecule", TableMolecules);

// Register plugins
app.use(globalFunctions);
app.use(router);
app.use(pinia);
app.use(VueAxios, axios);
app.use(VueApexCharts);

app.mount('#app');

// Setup real-time notifications with Reverb private channels
import Echo from './echo.ts';
import { useAuthStore } from './stores/auth';
import { useNotificationStore } from './stores/notificationStore';
import { useToastStore } from './stores/toastStore';

// Wait for app to mount and check auth status
setTimeout(() => {
    const authStore = useAuthStore();
    const notificationStore = useNotificationStore();
    const toastStore = useToastStore();

    if (authStore.user && authStore.user.id) {
        // Listen to private channel for the authenticated user
        Echo.private(`App.Models.User.${authStore.user.id}`)
            .notification((notification: any) => {

                console.log('Received notification:', notification);
                
                // Add to notification store
                notificationStore.addNotification({
                    id: notification.id || Date.now(),
                    title: notification.title || 'New Notification',
                    message: notification.message || notification.body || '',
                    read_at: null,
                    created_at: new Date()
                });

                // Fetch latest notifications from API to sync with database
                notificationStore.fetchNotifications();

                // Show toast notification
                if (notification.show_as === 'toast'){
                toastStore.addToast(
                    notification.message  || 'New notification received',
                     'success',
                    5000,
                    notification.title
                );}
            });

        console.log(`Listening to private channel: App.Models.User.${authStore.user.id}`);
    }
}, 1000);
