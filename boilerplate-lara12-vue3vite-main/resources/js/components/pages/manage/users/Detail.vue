<template>
    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <button 
                    @click="$router.go(-1)"
                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </button>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pengguna</h1>
            </div>
            <button 
                v-if="userDetails"
                @click="handleEdit"
                class="flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Pengguna
            </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        </div>

        <!-- Content -->
        <div v-else-if="userDetails" class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Dasar</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ userDetails.name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ userDetails.email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">No. Telepon</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ userDetails.phone || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">No. Registrasi</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ userDetails.registration_number || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Bergabung</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(userDetails.created_at) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diperbarui</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(userDetails.updated_at) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Summary Statistics -->
            <div v-if="userDetails.summary" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg text-center">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ userDetails.summary.total_authorities }}</div>
                    <div class="text-sm text-blue-600 dark:text-blue-400 mt-1">Otoritas</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg text-center">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ userDetails.summary.total_organizations }}</div>
                    <div class="text-sm text-green-600 dark:text-green-400 mt-1">Organisasi</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg text-center">
                    <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ userDetails.summary.total_menus }}</div>
                    <div class="text-sm text-yellow-600 dark:text-yellow-400 mt-1">Menu</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-lg text-center">
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ userDetails.summary.total_actions }}</div>
                    <div class="text-sm text-purple-600 dark:text-purple-400 mt-1">Aksi</div>
                </div>
            </div>

            <!-- Organizations -->
            <div v-if="userDetails.organizations && userDetails.organizations.length > 0" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Organisasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="org in userDetails.organizations" :key="org.id" 
                         class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="font-medium text-gray-900 dark:text-white">{{ org.name }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ org.code }} • {{ org.type }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ org.city }}</div>
                    </div>
                </div>
            </div>

            <!-- Authorities with Menus and Actions -->
            <div v-if="userDetails.authorities && userDetails.authorities.length > 0" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Otoritas & Hak Akses</h3>
                <div class="space-y-6">
                    <div v-for="authority in userDetails.authorities" :key="authority.id" 
                         class="border border-gray-200 dark:border-gray-600 rounded-lg p-6 bg-gray-50 dark:bg-gray-700">
                        <!-- Authority Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ authority.name }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ authority.code }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ authority.application?.name }}</p>
                            </div>
                            <!-- Organizations for this authority -->
                            <div v-if="authority.organizations && authority.organizations.length > 0" class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Organisasi:</p>
                                <div class="space-y-1">
                                    <span v-for="org in authority.organizations" :key="org.id"
                                          class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded-full">
                                        {{ org.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Menus and Actions -->
                        <div v-if="authority.menus_with_actions && authority.menus_with_actions.length > 0" 
                             class="mt-4">
                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Menu & Aksi:</h5>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div v-for="menu in authority.menus_with_actions" :key="menu.id" 
                                     class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-primary-500 rounded-full"></div>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ menu.name }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ menu.url }}</span>
                                    </div>
                                    
                                    <!-- Actions for this menu -->
                                    <div v-if="menu.actions && menu.actions.length > 0" class="flex flex-wrap gap-2">
                                        <span v-for="action in menu.actions" :key="action.id"
                                              :class="action.allowed ? 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-800' : 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900 dark:text-red-300 dark:border-red-800'"
                                              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border">
                                            <svg v-if="action.allowed" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <svg v-else class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ action.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Devices -->
            <div v-if="userDetails.devices && userDetails.devices.length > 0" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Perangkat Aktif</h3>
                <div class="space-y-3">
                    <div v-for="device in userDetails.devices" :key="device.id" 
                         class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ device.device_name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Terakhir digunakan: {{ device.last_used_at }}
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            {{ device.created_at }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
            <div class="text-red-500 dark:text-red-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Error memuat data</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">{{ error }}</p>
            <button @click="loadUserDetails" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Coba Lagi
            </button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: false,
            userDetails: null,
            error: null,
            userId: null,
        }
    },
    created() {
        this.userId = this.$route.params.id;
        this.loadUserDetails();
    },
    methods: {
        async loadUserDetails() {
            if (!this.userId) {
                this.error = 'ID pengguna tidak valid';
                return;
            }

            this.loading = true;
            this.error = null;
            
            try {
                const response = await this.axios.get(`v1/master/user/${this.userId}`);
                this.userDetails = response.data;
            } catch (error) {
                console.error('Error loading user details:', error);
                this.error = error.response?.data?.message || 'Gagal memuat detail pengguna';
                this.$emit('showToast', this.error, 'error');
            } finally {
                this.loading = false;
            }
        },

        handleEdit() {
            this.$router.push({ name: 'update-users', params: { id: this.userId } });
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
};
</script>