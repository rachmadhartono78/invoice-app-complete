<template>
    <div class="p-6">
        <!-- Welcome Section -->
        <div class="flex flex-col md:flex-row md:justify-between gap-4 mb-6">
            <div class="flex-1">
                <span class="text-gray-900 dark:text-white">Selamat Datang kembali!</span>
                <h2 class="text-2xl font-bold text-primary-600 dark:text-primary-500">
                    {{ authStore.user?.name || 'User' }}
                </h2>
            </div>
            <div class="flex flex-col items-end gap-2">
                <div class="text-gray-600 dark:text-gray-400 font-medium">
                    {{ formattedDate }}
                </div>
                <button 
                    @click="downloadSummaryReport" 
                    :disabled="exportLoading"
                    class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors shadow-sm text-sm"
                >
                    <svg v-if="exportLoading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    {{ exportLoading ? 'Sedang Mencetak...' : 'Cetak Laporan Ringkasan' }}
                </button>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li class="mr-2">
                    <button 
                        @click="activeTab = 'business'"
                        :class="activeTab === 'business' ? 'text-primary-600 border-primary-600 dark:text-primary-500 dark:border-primary-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'"
                        class="inline-block p-4 border-b-2 rounded-t-lg transition-colors"
                    >
                        Overview Bisnis
                    </button>
                </li>
                <li class="mr-2">
                    <button 
                        @click="activeTab = 'system'"
                        :class="activeTab === 'system' ? 'text-primary-600 border-primary-600 dark:text-primary-500 dark:border-primary-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'"
                        class="inline-block p-4 border-b-2 rounded-t-lg transition-colors"
                    >
                        Overview Sistem
                    </button>
                </li>
            </ul>
        </div>

        <!-- Business Tab Content -->
        <div v-if="activeTab === 'business'">
            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Piutang</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ formatCurrency(stats.kpis.total_ar) }}</p>
                        </div>
                        <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-lg text-red-600 dark:text-red-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bayar Bulan Ini</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ formatCurrency(stats.kpis.paid_this_month) }}</p>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Penawaran Aktif</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.kpis.quotation_count }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jatuh Tempo</p>
                            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">{{ formatCurrency(stats.kpis.total_overdue) }}</p>
                        </div>
                        <div class="p-3 bg-orange-50 dark:bg-orange-900/30 rounded-lg text-orange-600 dark:text-orange-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Status Breakdown Chart -->
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Status Invoice</h3>
                    <div class="h-64">
                        <apexchart type="donut" height="100%" :options="chartOptions" :series="chartSeries"></apexchart>
                    </div>
                </div>

                <!-- Recent Invoices -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Invoice Terbaru</h3>
                        <router-link :to="{ name: 'invoices-index' }" class="text-sm text-primary-600 font-semibold hover:underline">Semua Invoice</router-link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">No. Invoice</th>
                                    <th class="px-4 py-3">Pelanggan</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="inv in stats.recent_invoices" :key="inv.id" class="border-bottom dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                    <td class="px-4 py-3 font-medium text-blue-600 hover:underline cursor-pointer" @click="router.push({ name: 'invoices-view', params: { id: inv.id } })">
                                        {{ inv.invoice_number }}
                                    </td>
                                    <td class="px-4 py-3">{{ inv.customer_name }}</td>
                                    <td class="px-4 py-3 font-bold">{{ formatCurrency(inv.total) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span :class="getStatusClass(inv.status)" class="px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ inv.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!stats.recent_invoices.length">
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-400 italic">Belum ada invoice</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Payments -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <button @click="router.push({ name: 'quotations-index' })" class="flex flex-col items-center justify-center p-4 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-xl hover:bg-blue-100 transition-colors">
                            <svg class="h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            <span class="text-sm font-bold">Buat Penawaran</span>
                        </button>
                        <button @click="router.push({ name: 'invoices-create' })" class="flex flex-col items-center justify-center p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded-xl hover:bg-emerald-100 transition-colors">
                            <svg class="h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span class="text-sm font-bold">Buat Invoice</span>
                        </button>
                        <button @click="router.push({ name: 'customers-create' })" class="flex flex-col items-center justify-center p-4 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-xl hover:bg-purple-100 transition-colors">
                            <svg class="h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                            <span class="text-sm font-bold">Tambah Pelanggan</span>
                        </button>
                        <button @click="router.push({ name: 'items-create' })" class="flex flex-col items-center justify-center p-4 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 rounded-xl hover:bg-orange-100 transition-colors">
                            <svg class="h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            <span class="text-sm font-bold">Tambah Produk</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Pembayaran Terakhir</h3>
                    <div class="space-y-4">
                        <div v-for="pay in stats.recent_payments" :key="pay.id" class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 flex items-center justify-center rounded-full">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ pay.invoice?.invoice_number }}</p>
                                <p class="text-xs text-gray-500">{{ pay.payment_date }} - {{ pay.payment_method }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-emerald-600 font-mono">+{{ formatCurrency(pay.amount) }}</p>
                            </div>
                        </div>
                        <div v-if="!stats.recent_payments.length" class="py-8 text-center text-gray-400 italic">Belum ada pembayaran</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Tab Content -->
        <div v-if="activeTab === 'system'">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Total Users Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ systemStats.users }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Organizations Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Organizations</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ systemStats.organizations }}</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Items Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Produk/Item</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ systemStats.items }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Links -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Akses Pengaturan Cepat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button @click="router.push({ name: 'index-users' })" class="flex items-center gap-3 p-4 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-white">User Management</span>
                    </button>
                    <button @click="router.push({ name: 'index-organizations' })" class="flex items-center gap-3 p-4 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg text-green-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-white">Organizations</span>
                    </button>
                    <button @click="router.push({ name: 'index-authorities' })" class="flex items-center gap-3 p-4 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg text-yellow-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-white">Access Control</span>
                    </button>
                    <button @click="router.push({ name: 'index-menus' })" class="flex items-center gap-3 p-4 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg text-indigo-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-white">Menu Settings</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import dashboardAxios from '@/api/dashboardAxios';

const router = useRouter();
const authStore = useAuthStore();
const exportLoading = ref(false);
const activeTab = ref('business');

const stats = ref({
    kpis: {
        total_ar: 0,
        paid_this_month: 0,
        quotation_count: 0,
        total_overdue: 0,
    },
    status_breakdown: [],
    recent_invoices: [],
    recent_payments: [],
});

const systemStats = ref({
    users: 0,
    organizations: 0,
    items: 0,
});

const formattedDate = computed(() => {
    return new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
});

const chartSeries = computed(() => {
    return stats.value.status_breakdown.map(item => item.count);
});

const chartOptions = computed(() => {
    return {
        chart: {
            type: 'donut',
        },
        labels: stats.value.status_breakdown.map(item => item.status),
        colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#6B7280', '#8B5CF6'],
        legend: {
            position: 'bottom'
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: () => stats.value.status_breakdown.reduce((a, b) => a + b.count, 0)
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        }
    };
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

const getStatusClass = (status: string) => {
    const classes: any = {
        'DRAFT': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'QUOTED': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'INVOICED': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'PARTIAL_PAID': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        'PAID': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
        'VOID': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const fetchStats = async () => {
    try {
        const res = await dashboardAxios.get('/dashboard/stats');
        stats.value = res.data;
    } catch (error) {
        console.error('Gagal mengambil data statistik:', error);
    }
};

const fetchSystemStats = async () => {
    try {
        const [usersRes, orgsRes, itemsRes] = await Promise.allSettled([
            dashboardAxios.get('/v1/master/user'),
            dashboardAxios.get('/v1/settings/organizations'),
            dashboardAxios.get('/items?per_page=1'), // Just to get total
        ]);

        if (usersRes.status === 'fulfilled') {
            systemStats.value.users = usersRes.value?.data?.length || usersRes.value?.total || 0;
            // Handle if it's wrapped in data.data
            if (usersRes.value?.data?.data) systemStats.value.users = usersRes.value.data.total;
        }
        if (orgsRes.status === 'fulfilled') {
            systemStats.value.organizations = orgsRes.value?.data?.length || orgsRes.value?.total || 0;
            if (orgsRes.value?.data?.data) systemStats.value.organizations = orgsRes.value.data.total;
        }
        if (itemsRes.status === 'fulfilled') {
            systemStats.value.items = itemsRes.value?.data?.total || itemsRes.value?.total || 0;
        }
    } catch (error) {
        console.error('Failed to fetch system stats:', error);
    }
};

const downloadSummaryReport = async () => {
    exportLoading.value = true;
    try {
        const response = await dashboardAxios.get('/dashboard/report/export', {
            responseType: 'blob'
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `Laporan_Ringkasan_Invoice_${new Date().toISOString().split('T')[0]}.pdf`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error('Gagal mendownload laporan:', error);
        alert('Gagal mendownload laporan. Silakan coba lagi.');
    } finally {
        exportLoading.value = false;
    }
};

onMounted(() => {
    fetchStats();
    fetchSystemStats();
});
</script>
