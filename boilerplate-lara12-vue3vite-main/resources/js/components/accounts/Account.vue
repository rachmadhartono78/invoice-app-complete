<template>
    <div class="grid grid-cols-1 px-4 pt-6 xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">

        <confirmation-dialog :isVisible="dialogConfirmation" title="Konfirmasi" :loading="loading"
            :message="messageDialog" @onClose="handleOnClose($event)" />

        <div class="mb-4 col-span-full xl:mb-2">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Pengaturan Akun</h1>
        </div>
        <!-- Right Content -->
        <div class="col-span-full xl:col-auto">
            <div
                class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="items-center sm:flex xl:block 2xl:flex sm:space-x-4 xl:space-x-0 2xl:space-x-4">
                    <img v-if="data?.avatar" class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0"
                        :src="data?.avatar" alt="Profile Picture">
                    <div v-else class="relative w-28 h-28 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                        <svg class="absolute w-30 h-30 text-gray-400 top-3" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <!-- <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">Foto profil</h3> -->
                        <!-- <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                            JPG, GIF or PNG. Max size of 800K
                        </div> -->
                        <div class="flex items-center space-x-4" v-if="false">
                            <button type="button"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
                                    </path>
                                    <path d="M9 13h2v5a1 1 0 11-2 0v-5z"></path>
                                </svg>
                                Upload picture
                            </button>
                            <button type="button"
                                class="py-2 px-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div v-if="data"
                class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold dark:text-white">Informasi Umum</h3>
                <form @submit.prevent="handlePhoneChange">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <label for="first-name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                                Lengkap</label>
                            <input type="text" name="first-name" id="first-name" :value="data?.name" disabled readonly
                                class="shadow-sm bg-gray-100 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 cursor-not-allowed dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Nama lengkap" required>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="organization"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor Induk</label>
                            <input type="text" name="organization" id="organization" disabled readonly
                                :value="data?.registration_number"
                                class="shadow-sm bg-gray-100 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 cursor-not-allowed dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="email"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <input type="email" name="email" id="email" disabled readonly :value="data?.email"
                                class="shadow-sm bg-gray-100 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 cursor-not-allowed dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Email" required>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="phone-number"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. HP</label>
                            <input type="number" name="phone-number" id="phone-number" v-model="data.phone"
                                class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required>
                        </div>

                        <!-- Email Identifiers (View Only) -->
                        <div class="col-span-6" v-if="emailIdentifiers.length > 0">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email Tambahan</label>
                            <ul>
                                <li class="text-sm mb-1" v-for="email in emailIdentifiers" :key="email.id">
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">
                                        • {{ email.value }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <!-- Phone Identifiers (View Only) -->
                        <div class="col-span-6" v-if="phoneIdentifiers.length > 0">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. HP Tambahan</label>
                            <ul>
                                <li class="text-sm mb-1" v-for="phone in phoneIdentifiers" :key="phone.id">
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">
                                        • {{ phone.value }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <!-- Username Identifiers (View Only) -->
                        <div class="col-span-6" v-if="usernameIdentifiers.length > 0">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username Tambahan</label>
                            <ul>
                                <li class="text-sm mb-1" v-for="username in usernameIdentifiers" :key="username.id">
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">
                                        • {{ username.value }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <!-- <div class="col-span-6 sm:col-span-3">
                            <label for="birthday"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Birthday</label>
                            <input type="number" name="birthday" id="birthday" v-model="data"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required>
                        </div> -->
                        <div class="col-span-6 sm:col-span-6">
                            <label for="organization"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Organisasi</label>
                            <ul>
                                <li class="text-sm" v-for="org in data?.organizations"><span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">•
                                        {{ org.name }}</span></li>
                            </ul>
                        </div>
                        <div class="col-span-6 sm:col-span-6">
                            <label for="organization"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Otoritas</label>
                            <ul>
                                <li class="text-sm" v-for="org in data?.authorities"><span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">•
                                        {{ org.name }}</span></li>
                            </ul>
                        </div>
                        <div class="col-span-6 sm:col-span-6">
                            <button :disabled="!data?.phone"
                                :class="['text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center',
                                    !data?.phone ? 'bg-gray-400 cursor-not-allowed' : 'bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800']"
                                type="submit">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
            <div
                class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold dark:text-white">Reset Password</h3>
                <form @submit.prevent="handlePasswordChange">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <label for="current-password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password
                                baru</label>
                            <input type="password" name="current-password" id="current-password" v-model="newPassword"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="••••••••" required>
                            <span v-if="newPassword">
                                <small v-if="newPassword.length < 8" class="text-red-600 flex items-start mt-2">
                                    <svg class="w-6 h-6 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v5a1 1 0 1 0 2 0V8Zm-1 7a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H12Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Password harus memiliki minimal 8 karakter, termasuk huruf besar, huruf kecil,
                                    angka, dan simbol.</small>
                            </span>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="confirm-password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi password
                                baru</label>
                            <input type="password" name="confirm-password" id="confirm-password"
                                v-model="confirmPassword"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="••••••••" required>
                            <span v-if="newPassword">
                                <small v-if="!confirmPassword" class="text-gray-500 mt-2">Silakan masukkan konfirmasi
                                    password</small>
                                <small v-else-if="newPassword !== confirmPassword"
                                    class="text-red-600 flex items-center mt-2">
                                    <svg class="w-4 h-4 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v5a1 1 0 1 0 2 0V8Zm-1 7a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H12Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Password tidak
                                    cocok</small>
                                <small v-else-if="newPassword === confirmPassword"
                                    class="text-green-600 flex items-center mt-2">
                                    <svg class="w-3 h-3 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Password sudah sesuai</small>
                            </span>
                        </div>
                        <div class="col-span-6 sm:col-full">
                            <button
                                :disabled="!newPassword || !confirmPassword || newPassword !== confirmPassword || newPassword.length < 8"
                                :class="['text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center',
                                    (!newPassword || !confirmPassword || newPassword !== confirmPassword || newPassword.length < 8) ? 'bg-gray-400 cursor-not-allowed' : 'bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800']"
                                type="submit">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
            <div
                class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flow-root">
                    <h3 class="text-xl font-semibold dark:text-white">Sesi</h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li class="py-4" v-for="session in devices" :key="session.id">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <svg :class="['w-7 h-7', session.expires_at ? 'text-gray-400 dark:text-gray-600' : 'dark:text-white']" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M9 16H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v1M9 12H4m8 8V9h8v11h-8Zm0 0H9m8-4a1 1 0 1 0-2 0 1 1 0 0 0 2 0Z" />
                                    </svg>

                                </div>
                                <div class="flex-1 min-w-0">
                                    <p :class="['text-base font-semibold truncate', session.expires_at ? 'text-gray-400 dark:text-gray-400' : 'text-gray-900 dark:text-white']">
                                        {{ session.device_name }}

                                    </p>
                                    <p v-if="session.expires_at"
                                        class="text-sm font-normal text-gray-500 truncate dark:text-gray-400">
                                        Kedaluwarsa {{ session.expires_at }}
                                    </p>
                                    <p v-else-if="session.last_used_at !== 'Online'"
                                        class="text-sm font-normal text-gray-500 truncate dark:text-gray-400">
                                        {{ session.last_used_at }}
                                    </p>
                                    <p v-else-if="session.last_used_at === 'Online'"
                                        class="flex items-center text-sm font-medium text-green-600 truncate dark:text-green-400">
                                        <svg class="w-3 h-3 text-green-500 dark:text-green-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="6" />
                                        </svg>
                                        Online
                                    </p>
                                    <p class="text-sm font-normal text-gray-500 truncate dark:text-gray-400">
                                        <small>Login pertama: {{ $functions.formatTanggalIndonesia(session.created_at,
                                            true)
                                        }}</small>
                                    </p>
                                </div>
                                <div class="inline-flex items-center">
                                    <a href="#" @click.prevent="handleDialogConfirmation(session.id, 'revoke')"
                                        :class="['px-3 py-2 mb-3 mr-3 text-sm font-medium text-center text-gray-800 border rounded-lg focus:ring-4 focus:ring-primary-300 dark:text-white dark:border-gray-600', 
                                            session.expires_at ? 'bg-red-600 text-white hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700' : 'bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700']">
                                        {{ session.expires_at ? 'Hapus' : 'Revoke' }}
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <!-- <div>
                        <button
                            class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">See
                            more</button>
                    </div> -->
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import { useAuthStore } from '@/stores/auth';
export default {
    setup() {
        const authStore = useAuthStore();
        return { authStore };
    },
    data: () => ({
        data: null,
        devices: [],
        dialogConfirmation: false,
        messageDialog: "Apakah anda yakin dengan aksi ini?",
        titleDialog: "Konfirmasi",
        typeModal: null,
        selectedId: null,
        loading: false,
        newPassword: '',
        confirmPassword: '',
        emailIdentifiers: [],
        phoneIdentifiers: [],
        usernameIdentifiers: [],
    }),
    created() {

    },
    mounted() {
        this.getUser();
    },
    watch: {

    },
    methods: {
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

                // Load identifiers (view only)
                if (res.data?.identifiers) {
                    this.emailIdentifiers = res.data.identifiers
                        .filter(i => i.type === 'email' && i.value !== res.data.email && !i.deleted_at)
                        .map(i => ({ id: i.id, type: i.type, value: i.value, verified_at: i.verified_at }));

                    this.phoneIdentifiers = res.data.identifiers
                        .filter(i => i.type === 'phone' && i.value !== res.data.phone && !i.deleted_at)
                        .map(i => ({ id: i.id, type: i.type, value: i.value, verified_at: i.verified_at }));

                    this.usernameIdentifiers = res.data.identifiers
                        .filter(i => i.type === 'username' && !i.deleted_at)
                        .map(i => ({ id: i.id, type: i.type, value: i.value, verified_at: i.verified_at }));
                }

                this.loading = false;
            }).catch(error => {
                this.loading = false;
                console.error('Error fetching user data:', error);

            }).finally(() => { })
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
        handlePasswordChange() {
            if (this.newPassword !== this.confirmPassword) {
                this.$emit('showToast', 'Password baru dan konfirmasi password tidak cocok!', 'error');
                return;
            }
            this.loading = true;
            const body = {
                new_password: this.newPassword,
                new_password_confirmation: this.confirmPassword,

            };
            this.axios.put(`v1/master/user/${this.authStore.user.id}/password`, body).then(res => {
                this.newPassword = '';
                this.confirmPassword = '';
                this.$emit('showToast', res.message || 'Password berhasil diubah!', 'success');
            }).catch(error => {
                console.error('Error changing password:', error);
                this.$emit('showToast', 'Gagal mengubah password!', 'error');
            }).finally(() => {
                this.loading = false;
            });
        },
        handlePhoneChange() {
            this.loading = true;
            const body = {
                new_phone: `${this.data.phone}`,
            };
            this.axios.put(`v1/master/user/${this.authStore.user.id}/phone`, body).then(res => {
                this.getUser();
                this.$emit('showToast', res.message || 'Nomor telepon berhasil diubah!', 'success');
            }).catch(error => {
                console.error('Error updating phone number:', error);
                this.$emit('showToast', 'Gagal mengubah nomor telepon!', 'error');
            }).finally(() => {
                this.loading = false;
            });
        },
        revokeToken(id) {
            this.axios.delete(`v1/master/user/${this.authStore.user.id}/revoke/${id}`).then(res => {
                this.dialogConfirmation = false;
                this.getUser();
                this.loading = false;
                this.$emit('showToast', res.message || 'Berhasil menghapus sesi!', 'success');
            }).catch(error => {
                console.error('Error revoking token:', error);
                this.loading = false;
            });
        },

    },
}
</script>