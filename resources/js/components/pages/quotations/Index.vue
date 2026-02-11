<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold dark:text-white">Quotations</h1>
            <button
                @click="$router.push('/app/invoices/invoices/create')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Quotation
            </button>
        </div>

        <!-- Filters -->
        <div class="flex gap-4 mb-4">
            <input
                v-model="search"
                @input="load"
                placeholder="Search..."
                class="border dark:border-gray-600 px-3 py-2 rounded flex-1 dark:bg-gray-700 dark:text-white"
            />
            <select
                v-model="status"
                @change="load"
                class="border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
            >
                <option value="">All Status</option>
                <option value="DRAFT">Draft</option>
                <option value="QUOTED">Quoted</option>
            </select>
        </div>

        <!-- Bulk Actions Bar -->
        <div v-if="selectedQuotations.length > 0" class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded p-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium dark:text-blue-400">{{ selectedQuotations.length }} item(s) selected</span>
                <div class="flex gap-2">
                    <button
                        @click="bulkMarkAsQuoted"
                        :disabled="!hasOnlyDrafts"
                        class="bg-purple-600 hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-3 py-2 rounded text-sm"
                    >
                        ✅ Mark as Quoted
                    </button>
                    <button
                        @click="bulkConvertToInvoice"
                        :disabled="!hasOnlyQuoted"
                        class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-3 py-2 rounded text-sm"
                    >
                        💰 Convert to Invoice
                    </button>
                    <button
                        @click="bulkDelete"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm"
                    >
                        🗑 Delete
                    </button>
                    <button
                        @click="clearSelection"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-3 py-2 rounded text-sm"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded shadow overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-3 text-left">
                            <input
                                type="checkbox"
                                v-model="selectAll"
                                @change="toggleSelectAll"
                                class="w-4 h-4 cursor-pointer"
                            />
                        </th>
                        <th class="p-3 text-left dark:text-gray-200">Invoice/Quote#</th>
                        <th class="p-3 text-left dark:text-gray-200">Date</th>
                        <th class="p-3 text-left dark:text-gray-200">Customer</th>
                        <th class="p-3 text-right dark:text-gray-200">Total</th>
                        <th class="p-3 text-center dark:text-gray-200">Status</th>
                        <th class="p-3 text-center dark:text-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading" class="border-t dark:border-gray-700">
                        <td colspan="7" class="p-4 text-center dark:text-gray-300">Loading...</td>
                    </tr>
                    <tr v-else-if="!quotations.length" class="border-t dark:border-gray-700">
                        <td colspan="7" class="p-4 text-center text-gray-500 dark:text-gray-400">No quotations found</td>
                    </tr>
                    <tr
                        v-else
                        v-for="quot in quotations"
                        :key="quot.id"
                        :class="`border-t dark:border-gray-700 ${isSelected(quot.id) ? 'bg-blue-50 dark:bg-blue-900/30' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}`"
                    >
                        <td class="p-3">
                            <input
                                type="checkbox"
                                :value="quot.id"
                                v-model="selectedQuotations"
                                class="w-4 h-4 cursor-pointer"
                            />
                        </td>
                        <td class="p-3 dark:text-gray-300">
                            <div>{{ quot.quotation_number || quot.invoice_number }}</div>
                            <div v-if="quot.project_name" class="text-xs text-gray-500 dark:text-gray-400">
                                {{ quot.project_name }}
                            </div>
                        </td>
                        <td class="p-3 dark:text-gray-300">
                            {{ new Date(quot.invoice_date).toLocaleDateString('id-ID') }}
                        </td>
                        <td class="p-3 dark:text-gray-300">{{ quot.customer_name }}</td>
                        <td class="p-3 text-right dark:text-gray-300">{{ formatCurrency(quot.total) }}</td>
                        <td class="p-3 text-center">
                            <span
                                :class="getBadgeClass(quot.status)"
                                class="px-2 py-1 rounded text-xs"
                            >
                                {{ quot.status }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="flex gap-2 justify-center">
                                <button
                                    @click="$router.push(`/app/invoices/invoices/${quot.id}`)"
                                    class="text-blue-600 dark:text-blue-400"
                                    title="View"
                                >
                                    👁
                                </button>
                                <button
                                    @click="$router.push(`/app/invoices/invoices/${quot.id}/edit`)"
                                    class="text-green-600 dark:text-green-400"
                                    title="Edit"
                                >
                                    ✏️
                                </button>
                                <button
                                    v-if="quot.status === 'DRAFT'"
                                    @click="markAsQuoted(quot.id)"
                                    class="text-purple-600 dark:text-purple-400"
                                    title="Mark as Quoted"
                                >
                                    ✅
                                </button>
                                <button
                                    v-if="quot.status === 'QUOTED'"
                                    @click="convertToInvoice(quot.id)"
                                    class="text-indigo-600 dark:text-indigo-400"
                                    title="Convert to Invoice"
                                >
                                    💰
                                </button>
                                <button
                                    @click="downloadPdf(quot.id)"
                                    :disabled="loadingPdfId === quot.id"
                                    :class="loadingPdfId === quot.id ? 'text-gray-400 opacity-50 cursor-wait' : 'text-red-600 dark:text-red-400'"
                                    title="Export PDF"
                                >
                                    {{ loadingPdfId === quot.id ? '⏳' : '📄' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="mt-4 flex justify-between items-center dark:text-gray-300">
            <div>
                Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
            </div>
            <div class="flex gap-2">
                <button
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    class="px-3 py-1 border dark:border-gray-600 rounded disabled:opacity-50 dark:bg-gray-700"
                >
                    Previous
                </button>
                <button
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="px-3 py-1 border dark:border-gray-600 rounded disabled:opacity-50 dark:bg-gray-700"
                >
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import dashboardAxios from '@/api/dashboardAxios';

export default {
    name: 'QuotationIndex',
    data() {
        return {
            quotations: [],
            loading: false,
            loadingPdfId: null,
            search: '',
            status: 'DRAFT',
            selectedQuotations: [],
            selectAll: false,
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0,
                from: 0,
                to: 0
            }
        };
    },
    computed: {
        hasOnlyDrafts() {
            if (this.selectedQuotations.length === 0) return false;
            return this.quotations
                .filter(q => this.selectedQuotations.includes(q.id))
                .every(q => q.status === 'DRAFT');
        },
        hasOnlyQuoted() {
            if (this.selectedQuotations.length === 0) return false;
            return this.quotations
                .filter(q => this.selectedQuotations.includes(q.id))
                .every(q => q.status === 'QUOTED');
        }
    },
    mounted() {
        this.load();
    },
    methods: {
        async load() {
            this.loading = true;
            this.selectedQuotations = [];
            this.selectAll = false;
            try {
                const params = {
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    search: this.search,
                    status: this.status || 'DRAFT,QUOTED'
                };
                const { data } = await dashboardAxios.get('/invoices', { params });
                this.quotations = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    per_page: data.per_page,
                    total: data.total,
                    from: data.from,
                    to: data.to
                };
            } catch (error) {
                console.error('Failed to load quotations:', error);
                this.$emit('showToast', 'Failed to load quotations', 'error');
            } finally {
                this.loading = false;
            }
        },
        isSelected(id) {
            return this.selectedQuotations.includes(id);
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedQuotations = this.quotations.map(q => q.id);
            } else {
                this.selectedQuotations = [];
            }
        },
        clearSelection() {
            this.selectedQuotations = [];
            this.selectAll = false;
        },
        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.load();
            }
        },
        formatCurrency(amount) {
            return 'Rp ' + Number(amount).toLocaleString('id-ID');
        },
        getBadgeClass(status) {
            const classes = {
                'DRAFT': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                'QUOTED': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        async bulkMarkAsQuoted() {
            if (!confirm(`Mark ${this.selectedQuotations.length} quotation(s) as quoted?`)) return;
            
            let successCount = 0;
            let failureCount = 0;
            
            for (const id of this.selectedQuotations) {
                try {
                    await dashboardAxios.post(`/invoices/${id}/mark-as-quoted`);
                    successCount++;
                } catch (error) {
                    console.error(`Failed to mark quotation ${id} as quoted:`, error);
                    failureCount++;
                }
            }
            
            if (successCount > 0) {
                this.$emit('showToast', `✅ ${successCount} quotation(s) marked as quoted`, 'success');
            }
            if (failureCount > 0) {
                this.$emit('showToast', `❌ ${failureCount} quotation(s) failed`, 'error');
            }
            
            this.clearSelection();
            this.load();
        },
        async bulkConvertToInvoice() {
            if (!confirm(`Convert ${this.selectedQuotations.length} quotation(s) to invoice(s)?`)) return;
            
            let successCount = 0;
            let failureCount = 0;
            
            for (const id of this.selectedQuotations) {
                try {
                    await dashboardAxios.post(`/invoices/${id}/mark-as-invoiced`);
                    successCount++;
                } catch (error) {
                    console.error(`Failed to convert quotation ${id}:`, error);
                    failureCount++;
                }
            }
            
            if (successCount > 0) {
                this.$emit('showToast', `✅ ${successCount} quotation(s) converted to invoice(s)`, 'success');
            }
            if (failureCount > 0) {
                this.$emit('showToast', `❌ ${failureCount} quotation(s) failed`, 'error');
            }
            
            this.clearSelection();
            this.load();
        },
        async bulkDelete() {
            if (!confirm(`Delete ${this.selectedQuotations.length} quotation(s)? This cannot be undone.`)) return;
            
            let successCount = 0;
            let failureCount = 0;
            
            for (const id of this.selectedQuotations) {
                try {
                    await dashboardAxios.delete(`/invoices/${id}`);
                    successCount++;
                } catch (error) {
                    console.error(`Failed to delete quotation ${id}:`, error);
                    failureCount++;
                }
            }
            
            if (successCount > 0) {
                this.$emit('showToast', `✅ ${successCount} quotation(s) deleted`, 'success');
            }
            if (failureCount > 0) {
                this.$emit('showToast', `❌ ${failureCount} quotation(s) failed to delete`, 'error');
            }
            
            this.clearSelection();
            this.load();
        },
        async markAsQuoted(id) {
            if (!confirm('Mark this quotation as quoted?')) return;
            
            try {
                await dashboardAxios.post(`/invoices/${id}/mark-as-quoted`);
                this.$emit('showToast', '✅ Quotation marked as quoted', 'success');
                this.load();
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to mark as quoted';
                this.$emit('showToast', msg, 'error');
            }
        },
        async convertToInvoice(id) {
            if (!confirm('Convert this quotation to invoice?')) return;
            
            try {
                await dashboardAxios.post(`/invoices/${id}/mark-as-invoiced`);
                this.$emit('showToast', '✅ Converted to invoice successfully', 'success');
                this.load();
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to convert to invoice';
                this.$emit('showToast', msg, 'error');
            }
        },
        async downloadPdf(id) {
            this.loadingPdfId = id;
            try {
                const { data } = await dashboardAxios.get(`/invoices/${id}/pdf`, {
                    responseType: 'blob'
                });
                const url = window.URL.createObjectURL(new Blob([data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `quotation-${id}.pdf`);
                document.body.appendChild(link);
                link.click();
                link.remove();
            } catch (error) {
                this.$emit('showToast', 'Failed to download PDF', 'error');
            } finally {
                this.loadingPdfId = null;
            }
        }
    }
};
</script>
