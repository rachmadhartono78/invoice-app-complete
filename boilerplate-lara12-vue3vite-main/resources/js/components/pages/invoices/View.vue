<template>
    <div class="p-6" v-if="inv">
        <!-- Header Actions -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="flex items-center gap-3">
                <button @click="$router.back()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </button>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail {{ inv.status === 'QUOTED' ? 'Penawaran' : 'Invoice' }}</h2>
                <span :class="getStatusClass(inv.status)" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ inv.status }}
                </span>
            </div>
            <div class="flex gap-2">
                <button v-if="inv.status === 'QUOTED'" @click="convertToInvoice" :disabled="loadingAction" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Jadikan Invoice
                </button>
                <button @click="downloadPdf(inv.id)" :disabled="loadingPdf" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors shadow-sm disabled:opacity-50">
                    <svg v-if="loadingPdf" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cetak PDF
                </button>
                <button @click="$router.push(`/app/invoices/invoices/${inv.id}/edit`)" class="flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-semibold transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <!-- Standard Invoice Header -->
                    <div class="flex justify-between items-start mb-10 pb-6 border-b dark:border-gray-700">
                        <div>
                            <h2 class="text-2xl font-bold text-primary-600 dark:text-primary-500">PT. INDONESIA JAYA</h2>
                            <div class="text-gray-500 text-sm mt-1">Jl. Contoh Alamat No. 123</div>
                            <div class="text-gray-500 text-sm">Jakarta, Indonesia</div>
                        </div>
                        <div class="text-right">
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ inv.status === 'QUOTED' ? 'PENAWARAN' : 'FAKTUR' }}</h1>
                            <div class="text-lg font-mono text-gray-600 dark:text-gray-400 mt-1">{{ inv.invoice_number }}</div>
                        </div>
                    </div>

                    <!-- Client & Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ditagihkan Ke:</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ inv.customer_name }}</div>
                            <div class="text-gray-500 text-sm mt-1 whitespace-pre-wrap">{{ inv.customer_address }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-y-4 text-sm">
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tanggal</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ formatDate(inv.invoice_date) }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tempo</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ inv.payment_terms || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">PO No.</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ inv.po_number || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Mata Uang</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ inv.currency_name || 'IDR' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto mb-8">
                        <table class="w-full text-left">
                            <thead class="border-b-2 border-gray-900 dark:border-gray-600">
                                <tr>
                                    <th class="py-4 font-bold text-gray-900 dark:text-white">Deskripsi Barang</th>
                                    <th class="py-4 font-bold text-gray-900 dark:text-white text-center">Kts</th>
                                    <th class="py-4 font-bold text-gray-900 dark:text-white text-right">Harga</th>
                                    <th class="py-4 font-bold text-gray-900 dark:text-white text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700">
                                <tr v-for="(it, i) in inv.items" :key="i">
                                    <td class="py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ it.item_name }}</div>
                                        <div class="text-xs text-gray-500">{{ it.item_code }}</div>
                                    </td>
                                    <td class="py-4 text-center text-gray-900 dark:text-white">{{ it.quantity }}</td>
                                    <td class="py-4 text-right text-gray-900 dark:text-white">{{ fmt(it.unit_price) }}</td>
                                    <td class="py-4 text-right font-bold text-gray-900 dark:text-white">{{ fmt((it.quantity||0)*(it.unit_price||0) - (it.discount||0)) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Summary -->
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="flex-1">
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg">
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Terbilang:</div>
                                <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 italic">"{{ inv.terbilang || terbilangIndo(Math.round(inv.total||0)) }}"</div>
                            </div>
                            <div class="mt-4" v-if="inv.notes">
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Catatan:</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ inv.notes }}</div>
                            </div>
                        </div>
                        <div class="w-full md:w-64 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ fmt(inv.subtotal) }}</span>
                            </div>
                            <div v-if="inv.discount" class="flex justify-between text-sm">
                                <span class="text-gray-500">Diskon</span>
                                <span class="font-semibold text-red-600">-{{ fmt(inv.discount) }}</span>
                            </div>
                            <div v-if="inv.ppn_amount" class="flex justify-between text-sm">
                                <span class="text-gray-500">PPN ({{ inv.ppn_percent }}%)</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ fmt(inv.ppn_amount) }}</span>
                            </div>
                            <div v-if="inv.other_charges" class="flex justify-between text-sm">
                                <span class="text-gray-500">Biaya Lain</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ fmt(inv.other_charges) }}</span>
                            </div>
                            <div class="flex justify-between text-xl border-t-2 border-gray-900 dark:border-gray-600 pt-3">
                                <span class="font-black text-gray-900 dark:text-white uppercase">Total</span>
                                <span class="font-black text-primary-600 dark:text-primary-500">{{ fmt(inv.total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Informasi Tambahan</h3>
                    <div class="space-y-4 text-sm">
                        <!-- Audit Fields -->
                        <div v-if="inv.created_at">
                            <div class="text-xs font-bold text-gray-400 uppercase">Dibuat Pada</div>
                            <div class="text-gray-700 dark:text-gray-300">{{ formatDateTime(inv.created_at) }}</div>
                        </div>
                        <div v-if="inv.quoted_at">
                            <div class="text-xs font-bold text-blue-400 uppercase">Quoted At</div>
                            <div class="text-gray-700 dark:text-gray-300">{{ formatDateTime(inv.quoted_at) }}</div>
                        </div>
                        <div v-if="inv.invoiced_at">
                            <div class="text-xs font-bold text-emerald-400 uppercase">Invoiced At</div>
                            <div class="text-gray-700 dark:text-gray-300">{{ formatDateTime(inv.invoiced_at) }}</div>
                        </div>
                        <div v-if="inv.paid_at">
                            <div class="text-xs font-bold text-purple-400 uppercase">Lunas Pada</div>
                            <div class="text-gray-700 dark:text-gray-300">{{ formatDateTime(inv.paid_at) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Internal Notes -->
                <div v-if="inv.internal_notes" class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-xl border border-yellow-100 dark:border-yellow-700/50">
                    <h3 class="font-bold text-yellow-800 dark:text-yellow-400 mb-2">Catatan Internal</h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 whitespace-pre-wrap">{{ inv.internal_notes }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '@/api/dashboardAxios';

const route = useRoute();
const router = useRouter();
const inv = ref(null);
const loadingPdf = ref(false);
const loadingAction = ref(false);

const fmt = n => new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(n);

const fetchData = async () => {
    try {
        const data = await axios.get(`invoices/${route.params.id}`);
        inv.value = data;
    } catch (e) {
        console.error('Failed to fetch invoice:', e);
    }
};

onMounted(fetchData);

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const formatDateTime = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { 
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
};

const getStatusClass = (status) => {
    const classes = {
        'DRAFT': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'QUOTED': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'INVOICED': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'PARTIAL_PAID': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        'PAID': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
        'VOID': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const convertToInvoice = async () => {
    if (!confirm('Ubah penawaran ini menjadi invoice?')) return;
    
    loadingAction.value = true;
    try {
        await axios.post(`invoices/${inv.value.id}/mark-as-invoiced`);
        await fetchData();
        alert('✅ Penawaran berhasil dikonversi menjadi Invoice!');
    } catch (e) {
        console.error('Conversion failed', e);
        alert('Gagal mengkonversi penawaran.');
    } finally {
        loadingAction.value = false;
    }
};

const downloadPdf = async (id) => {
    try {
        loadingPdf.value = true;
        const response = await axios.get(`invoices/${id}/pdf`, { 
            responseType: 'blob', 
            headers: { Accept: 'application/pdf' } 
        });
        
        const url = window.URL.createObjectURL(new Blob([response]));
        const a = document.createElement('a');
        a.href = url;
        a.target = '_blank';
        a.download = `${inv.value.status}-${inv.value.invoice_number}.pdf`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
    } catch (e) {
        console.error('PDF download failed', e);
        alert('Gagal membuat PDF.');
    } finally {
        loadingPdf.value = false;
    }
};

function terbilangIndo(n) {
    const angka = ["","satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan","sepuluh","sebelas"];
    function inWords(n){
        n = Math.floor(n);
        if (n < 12) return angka[n];
        if (n < 20) return inWords(n-10) + ' belas';
        if (n < 100) return inWords(Math.floor(n/10)) + ' puluh' + (n%10 ? ' ' + inWords(n%10) : '');
        if (n < 200) return 'seratus' + (n-100 ? ' ' + inWords(n-100) : '');
        if (n < 1000) return inWords(Math.floor(n/100)) + ' ratus' + (n%100 ? ' ' + inWords(n%100) : '');
        if (n < 2000) return 'seribu' + (n-1000 ? ' ' + inWords(n-1000) : '');
        if (n < 1000000) return inWords(Math.floor(n/1000)) + ' ribu' + (n%1000 ? ' ' + inWords(n%1000) : '');
        if (n < 1000000000) return inWords(Math.floor(n/1000000)) + ' juta' + (n%1000000 ? ' ' + inWords(n%1000000) : '');
        return inWords(Math.floor(n/1000000000)) + ' milyar' + (n%1000000000 ? ' ' + inWords(n%1000000000) : '');
    }
    if (!n && n !== 0) return '';
    if (n === 0) return 'nol';
    return inWords(n) + ' rupiah';
}
</script>
