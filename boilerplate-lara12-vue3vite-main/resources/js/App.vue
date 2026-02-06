<template>
  <div>
    <Toast />
    <Confirmation />
    <Loading :show="loadingStore.isLoading" message="Sedang memproses..."/>
    <router-view @show-toast="handleShowToast"></router-view>
  </div>

</template>

<script setup>
import { useThemeStore } from "./stores/themeStore";
import Toast from "@/components/interactions/Toast.vue";
import Confirmation from "@/components/interactions/Confirmation.vue";
import Loading from "@/components/interactions/Loading.vue";
import { useToastStore } from "@/stores/toastStore";
import { useNotificationStore } from "@/stores/notificationStore";
import { onMounted } from "vue";
import echo from './echo'
import { useAuthStore } from "@/stores/auth";
import { useLoadingStore } from '@/stores/loadingStore.ts';

const authStore = useAuthStore();
const themeStore = useThemeStore();
const loadingStore = useLoadingStore();

themeStore.initializeTheme();

const notificationStore = useNotificationStore();
const toastStore = useToastStore();

const handleShowToast = ({ message, type, timeout = 3000, title= "" }) => {
  toastStore.addToast(message, type, timeout, title);
};
</script>
<style>
label .required::after {
  color: red;
  content: "*";
  margin-left: 5px;

}
</style>
