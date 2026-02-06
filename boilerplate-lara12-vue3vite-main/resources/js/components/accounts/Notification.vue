<template>
    <div class="grid grid-cols-1 px-4 pt-6 xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">

        <confirmation-dialog :isVisible="dialogConfirmation" title="Konfirmasi" :loading="loading"
            :message="messageDialog" @onClose="handleOnClose($event)" />

        <DetailNotification v-if="selectedNotifikasi" :modalData="selectedNotifikasi"
            @close="selectedNotifikasi = null"></DetailNotification>

        <div class="mb-4 col-span-full xl:mb-2">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Notifikasi </h1>
        </div>
        <!-- Right Content -->
        <div class="col-span-full xl:col-auto">
            <div
                class=" mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex justify-end ">
                    <button id="dropdownButton" data-dropdown-toggle="dropdown"
                        class="inline-block text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-1.5"
                        type="button">
                        <span class="sr-only">Open dropdown</span>
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 16 3">
                            <path
                                d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
                        </svg>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdown"
                        class="z-10 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
                        <ul class="py-2" aria-labelledby="dropdownButton">
                            <li>
                                <a href="#" @click.prevent="$router.push({ name: 'profile' })"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Edit</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="flex flex-col items-center pb-10">
                    <img v-if="data?.avatar" class="w-24 h-24 mb-3 rounded-full shadow-lg" :src="data?.avatar"
                        alt="Profile Picture">
                    <div v-else class="relative w-28 h-28 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                        <svg class="absolute w-30 h-30 text-gray-400 top-3" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ data?.name }}</h5>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ data?.email }}</span>
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div
                class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">

                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-3">
                        <h3 class="mb-4 text-xl font-semibold dark:text-white">Semua Notifikasi</h3>
                    </div>
                    <div class="self-end col-span-6 sm:col-span-3">
                        <h3 @click="readAll()" v-if="notifications.length > 0"
                            class="mb-4 text-primary-600 font-semibold text-right cursor-pointer hover:underline">Baca
                            semua</h3>
                    </div>
                </div>

                <ul class="" v-if="notifications.length > 0">
                    <li class="p-3 rounded-xl shadow-lg mb-2 sm:pb-4" v-for="nt in notifications"
                        @click.prevent="markAsRead(nt.id)"
                        :class="{ 'bg-primary-700 dark:bg-primary-600 bg-opacity-10 dark:bg-opacity-5': !nt.read_at, 'dark:bg-gray-700 dark:border-2 dark:border-gray-800 bg-opacity-10 dark:bg-opacity-5': nt.read_at }">
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            <div class="shrink-0">
                                <img class="w-11 h-11 rounded-full" v-if="nt?.avatar_sender" :src="nt.avatar_sender"
                                    alt="" />
                                <div v-else
                                    class=" w-11 h-11 right-[50%] overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                    <svg class="w-10 h-10 text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M17.133 12.632v-1.8a5.406 5.406 0 0 0-4.154-5.262.955.955 0 0 0 .021-.106V3.1a1 1 0 0 0-2 0v2.364a.955.955 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C6.867 15.018 5 15.614 5 16.807 5 17.4 5 18 5.538 18h12.924C19 18 19 17.4 19 16.807c0-1.193-1.867-1.789-1.867-4.175ZM8.823 19a3.453 3.453 0 0 0 6.354 0H8.823Z" />
                                    </svg>
                                </div>
                                <div v-if="!nt.read_at"
                                    class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 rounded-full border border-white bg-primary-700 dark:border-gray-700">
                                    <svg aria-hidden="true" class="w-3 h-3 text-white" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 cursor-pointer">
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-white cursor-pointer hover:underline"
                                    @click="goToUrl(nt.url_action)">
                                    {{ nt.title }}
                                </p>
                                <p class="text-sm text-gray-500 truncate dark:text-gray-400"
                                    @click.prevent="selectNotifikasi(nt)">
                                    {{ nt.message }}
                                </p>
                            </div>
                            <div class="inline-flex items-center text-xs font-thin text-gray-900 dark:text-white">
                                {{ nt.created_at }}
                            </div>
                        </div>
                    </li>
                </ul>

                <div v-if="loading" class="pt-3">
                    <div role="status"
                        class="mb-3 rounded-xl p-4 space-y-4 border border-gray-200 divide-y divide-gray-200 rounded-sm shadow-sm animate-pulse dark:divide-gray-700 md:p-6 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                                <div class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                            </div>
                            <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"></div>
                        </div>
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div role="status"
                        class="mb-3 rounded-xl p-4 space-y-4 border border-gray-200 divide-y divide-gray-200 rounded-sm shadow-sm animate-pulse dark:divide-gray-700 md:p-6 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                                <div class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                            </div>
                            <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"></div>
                        </div>
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div role="status"
                        class="rounded-xl p-4 space-y-4 border border-gray-200 divide-y divide-gray-200 rounded-sm shadow-sm animate-pulse dark:divide-gray-700 md:p-6 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                                <div class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                            </div>
                            <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"></div>
                        </div>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>


                <div class="grid grid-cols-6 gap-6 mt-5" v-if="(notifications.length < notificationCount && !loading)">
                    <div class="col-span-6 sm:col-span-6 flex justify-center">
                        <button type="button" @click="handleLoadMore()"
                            class="px-3 py-2 text-xs font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Lihat
                            lainnya..</button>
                    </div>
                </div>
                <div v-if="notifications.length == notificationCount" class="flex justify-center mt-5">
                    <span class="text-xs text-gray-400 py-5">Tidak ada notifikasi baru</span>
                </div>
            </div>

        </div>

    </div>
</template>
<script>
import { useAuthStore } from '@/stores/auth';
import { useNotificationStore } from '../../stores/notificationStore';
import DetailNotification from './DetailNotification.vue';

export default {
    components: {
        DetailNotification
    },
    setup() {
        const notificationStore = useNotificationStore()
        const authStore = useAuthStore();
        return {
            authStore,
            notificationStore,
        };
    },
    data: () => ({
        data: null,
        devices: [],
        dialogConfirmation: false,
        notifications: [],
        notificationCount: 0,
        selectedNotifikasi: null,
        loading: false,
        limit: 5,
        offset: 0
    }),
    created() {

    },
    mounted() {
        this.getUser();
        this.getNotifications();
    },
    watch: {

    },
    methods: {
        getNotifications(limit = this.limit, offset = this.offset) {
            this.loading = true;
            axios.get('notification', { params: { limit, offset } })
                .then(response => {
                    if (Array.isArray(response.data)) {
                        response.data.forEach(data => {
                            this.notifications.push(data);
                        });
                    } else {
                        console.error('Unexpected response format:', response.data);
                    }
                    this.notificationCount = response.count || 0;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Failed to fetch notifications:', error);
                    this.loading = false;
                });
        },
        getUser() {
            this.loading = true;
            const body = {
                email: this.email,
                password: this.password,
                device_name: this.device_name,
            };
            this.axios.get(`v1/master/user/${this.authStore.user.id}`).then(res => {
                this.data = res.data;
                this.devices = res.devices
                this.loading = false;
            }).catch(error => {
                this.loading = false;
                console.error('Error fetching user data:', error);

            }).finally(() => { })
        },
        handleLoadMore() {
            this.offset += 5;
            this.getNotifications();
        },
        selectNotifikasi(nt) {
            this.selectedNotifikasi = nt;
        },
        readAll() {
            this.notifications = [];
            const limit = this.offset + 5;
            const offset = 0;
            this.$emit('showToast', 'Berhasil tandai baca semua notifikasi', 'success');
            this.notificationStore.markAsReadAll();
            this.getNotifications(limit, offset);
        },
        markAsRead(id) {
            if (!this.notifications.find(nt => nt.id == id).read_at) {
                this.notificationStore.markAsRead(id);
                this.notifications.find(nt => nt.id == id).read_at = new Date();
            }
        },
        handleDialogConfirmation(id, type) {
            this.typeModal = type;
            switch (type) {
                case 'revoke':
                    this.messageDialog = 'Apakah anda yakin ingin menghapus sesi ini?'
                    this.titleDialog = "Konfirmasi hapus sesi"
                    break;

                default:
                    break;
            }
            this.selectedId = id;
            this.dialogConfirmation = true;
        },
        goToUrl(url) {
            this.$router.push(url);
        },
        handleOnClose(event) {
            this.loading = true;
            if (event === true) {
                switch (this.typeModal) {
                    case 'revoke':
                        this.revokeToken(this.selectedId);
                        break;

                    default:
                        break;
                }
            } else {
                this.dialogConfirmation = false;
            }
        },
    },
}
</script>