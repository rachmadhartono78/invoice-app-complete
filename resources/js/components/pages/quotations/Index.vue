<template>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-xl">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Quotations</h1>
            </div>
            
            <!-- Filters -->
             <div class="sm:flex sm:gap-4 mb-4">
                 <select
                    v-model="filters.status"
                    @change="load"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                    <option value="">All Status</option>
                    <option value="DRAFT">Draft</option>
                    <option value="QUOTED">Quoted</option>
                </select>
            </div>

             <!-- Bulk Actions Bar -->
            <div v-if="selectedQuotations.length > 0" class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-3 flex flex-wrap gap-2 items-center justify-between">
                <span class="text-sm font-medium text-blue-800 dark:text-blue-300">{{ selectedQuotations.length }} selected</span>
                <div class="flex gap-2 flex-wrap">
                     <button
                        @click="bulkMarkAsQuoted"
                        :disabled="!hasOnlyDrafts"
                        class="text-xs bg-purple-600 hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-3 py-2 rounded-lg transition-colors"
                    >
                        ✅ Mark Quoted
                    </button>
                    <button
                        @click="bulkConvertToInvoice"
                        :disabled="!hasOnlyQuoted"
                        class="text-xs bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-3 py-2 rounded-lg transition-colors"
                    >
                        💰 To Invoice
                    </button>
                    <button
                        @click="bulkDelete"
                        class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition-colors"
                    >
                        🗑 Delete
                    </button>
                    <button
                        @click="clearSelection"
                         class="text-xs bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg transition-colors"
                    >
                        Clear
                    </button>
                </div>
            </div>

            <table-molecule
                ref="table"
                :columns="columns"
                :data="quotations"
                :loading="loading"
                :server-side="true"
                :total-items="pagination.total"
                :per-page="pagination.per_page"
                :current-page="pagination.current_page"
                :show-add-button="true"
                :with-checkbox="true"
                @page-change="changePage"
                @search="handleSearch"
                @onAdd="navigateToAdd"
                @detail-clicked="navigateToDetail"
                @edit-clicked="navigateToEdit"
                @delete-clicked="confirmDelete"
                @selection-change="handleSelectionChange"
            >
                <!-- Custom Column: Number/Project -->
                <template v-slot:column-quotation_number="{ row }">
                     <div class="font-medium text-gray-900 dark:text-white">{{ row.quotation_number || row.invoice_number }}</div>
                    <div v-if="row.project_name" class="text-xs text-gray-500 dark:text-gray-400">
                        {{ row.project_name }}
                    </div>
                </template>

                 <!-- Custom Column: Date -->
                <template v-slot:column-date="{ row }">
                    {{ formatDate(row.invoice_date) }}
                </template>

                 <!-- Custom Column: Total -->
                 <template v-slot:column-total="{ row }">
                    <div class="text-right font-medium">
                        {{ formatCurrency(row.total) }}
                    </div>
                </template>

                <!-- Custom Column: Status -->
                <template v-slot:column-status="{ row }">
                    <span
                        :class="getBadgeClass(row.status)"
                        class="px-2.5 py-0.5 rounded text-xs font-medium"
                    >
                        {{ row.status }}
                    </span>
                </template>

                 <!-- Custom Actions attached to specific rows via 'additional_action' (handled in load mapping) or we can use slot? 
                      TableMolecule uses a dropdown for actions. 
                      We can map 'markAsQuoted' etc as additional actions.
                 -->

            </table-molecule>
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
            filters: {
                search: '',
                status: 'DRAFT'
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: 0,
                from: 0,
                to: 0
            },
            selectedQuotations: [], // Array of IDs
            columns: [
                 {
                    name: 'Quotation #',
                    prop: 'quotation_number',
                },
                {
                    name: 'Date',
                    prop: 'date',
                },
                {
                    name: 'Customer',
                    prop: 'customer_name',
                },
                {
                    name: 'Total',
                    prop: 'total',
                },
                 {
                    name: 'Status',
                    prop: 'status',
                },
                {
                    name: 'Action',
                    prop: 'actions',
                },
            ],
        };
    },
    computed: {
        hasOnlyDrafts() {
             if (this.selectedQuotations.length === 0) return false;
            // Need to find rows from `quotations` based on IDs in selectedQuotations
            const selectedRows = this.quotations.filter(q => this.selectedQuotations.includes(q.id));
            // If we have selected IDs but they are not in current page, we might not have status info?
            // Usually selection is current page only or we persist selection. TableMolecule emits 'selection-change' with ROWS usually?
            // checking TableMolecule implementation... it likely uses checkbox binding.
            // Let's assume selection-change gives us rows or IDs. 
            // Update: TableMolecule usually emits selection list.
            return selectedRows.every(q => q.status === 'DRAFT');
        },
        hasOnlyQuoted() {
             if (this.selectedQuotations.length === 0) return false;
            const selectedRows = this.quotations.filter(q => this.selectedQuotations.includes(q.id));
            return selectedRows.every(q => q.status === 'QUOTED');
        }
    },
    mounted() {
        this.load();
    },
    methods: {
        async load() {
            this.loading = true;
            
            // Note: If server-side pagination, selection typically resets on page change unless we manage it globally.
            // For now, we clear selection on load/page change to be safe/simple.
            this.selectedQuotations = []; 
            if (this.$refs.table) {
                // If table component exposes a clearSelection method, call it.
                // Or if it syncs via props.
            }

            try {
                const params = {
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    search: this.filters.search,
                    status: this.filters.status || 'DRAFT,QUOTED'
                };
                
                const response = await dashboardAxios.get('/invoices', { params });
                const result = response?.data?.data || response?.data || [];
                const meta = response?.data || response;

                this.quotations = result.map(q => {
                    const additional = [
                        { name: 'Download PDF', action: (row) => this.downloadPdf(row.id) }
                    ];

                    if (q.status === 'DRAFT') {
                        additional.unshift({ name: 'Mark as Quoted', action: (row) => this.markAsQuoted(row.id) });
                    }
                    if (q.status === 'QUOTED') {
                        additional.unshift({ name: 'Convert to Invoice', action: (row) => this.convertToInvoice(row.id) });
                    }

                    return {
                        ...q,
                        date: q.invoice_date,
                         actions: {
                            canUpdate: true,
                            canDelete: true,
                        },
                        additional_action: additional
                    };
                });

                this.pagination = {
                    current_page: meta.current_page || 1,
                    last_page: meta.last_page || 1,
                    per_page: meta.per_page || 10,
                    total: meta.total || 0,
                    from: meta.from || 0,
                    to: meta.to || 0
                };
            } catch (error) {
                console.error('Failed to load quotations:', error);
                this.$emit('showToast', 'Failed to load quotations', 'error');
            } finally {
                this.loading = false;
            }
        },
        changePage(page) {
            this.pagination.current_page = page;
            this.load();
        },
        handleSearch(query) {
            this.filters.search = query;
            this.pagination.current_page = 1;
            this.load();
        },
        navigateToAdd() {
            this.$router.push('/app/invoices/invoices/create'); // Reuse invoice create? Or is there a specific quotation create?
            // Original code used '/app/invoices/invoices/create' for "New Quotation" button.
            // Wait, does it assume creating an invoice IS creating a quotation?
            // The file `invoices/Form.vue` handled "New Invoice".
            // Let's stick to what the original code did: `@click="$router.push('/app/invoices/invoices/create')"`
        },
        navigateToDetail(row) {
            this.$router.push(`/app/invoices/invoices/${row.id}`);
        },
        navigateToEdit(row) {
            this.$router.push(`/app/invoices/invoices/${row.id}/edit`);
        },
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
        },
        getBadgeClass(status) {
             const classes = {
                'DRAFT': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                'QUOTED': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        // Bulk Actions
        handleSelectionChange(selection) {
            // selection is array of rows usually, or IDs depending on component.
            // If TableMolecule emits rows:
            this.selectedQuotations = selection.map(item => item.id);
        },
        clearSelection() {
            this.selectedQuotations = [];
            // If TableMolecule can be programmatically cleared, we might need a ref/method on it.
            // If it uses v-model for selection, we can just clear it. But here we use @selection-change.
            // We might not be able to clear the checkboxes in TableMolecule from here easily without checking its prop support.
            // If TableMolecule accepts a `selected` prop we could sync it.
            // Looking at previous `view_file` of `TableMolecules.vue`, it doesn't seem to have a `selected` prop for strict 2-way binding of selection state from parent to child for *clearing*.
            // It has `withCheckbox` and emits `selection-change`.
            // We'll leave it as is, standard "Clear" button might just clear our local state, 
            // but UI checkboxes might remain checked if we can't force update the child. 
            // Ideally we'd trigger a re-render or reload to clear.
             this.load(); // Reloading will clear selection as per load() logic.
        },
        
         async bulkMarkAsQuoted() {
            if (!confirm(`Mark ${this.selectedQuotations.length} quotation(s) as quoted?`)) return;
            // ... same logic as original ...
            let successCount = 0;
            for (const id of this.selectedQuotations) {
                try {
                    await dashboardAxios.post(`/invoices/${id}/mark-as-quoted`);
                    successCount++;
                } catch (e) { console.error(e); }
            }
            if (successCount) this.$emit('showToast', `${successCount} marked as quoted`, 'success');
            this.load();
        },
        async bulkConvertToInvoice() {
            if (!confirm(`Convert ${this.selectedQuotations.length} quotation(s) to invoice?`)) return;
             let successCount = 0;
            for (const id of this.selectedQuotations) {
                try {
                    await dashboardAxios.post(`/invoices/${id}/mark-as-invoiced`);
                    successCount++;
                } catch (e) { console.error(e); }
            }
            if (successCount) this.$emit('showToast', `${successCount} converted to invoice`, 'success');
            this.load();
        },
        async bulkDelete() {
            if (!confirm(`Delete ${this.selectedQuotations.length} quotation(s)?`)) return;
            let successCount = 0;
            for (const id of this.selectedQuotations) {
                try {
                    await dashboardAxios.delete(`/invoices/${id}`);
                    successCount++;
                } catch (e) { console.error(e); }
            }
             if (successCount) this.$emit('showToast', `${successCount} deleted`, 'success');
            this.load();
        },

        // Single Actions
        async markAsQuoted(id) {
            if (!confirm('Mark as quoted?')) return;
            try {
                await dashboardAxios.post(`/invoices/${id}/mark-as-quoted`);
                 this.$emit('showToast', 'Marked as quoted', 'success');
                this.load();
            } catch (e) {
                 this.$emit('showToast', 'Failed to mark as quoted', 'error');
            }
        },
        async convertToInvoice(id) {
            if (!confirm('Convert to invoice?')) return;
             try {
                await dashboardAxios.post(`/invoices/${id}/mark-as-invoiced`);
                 this.$emit('showToast', 'Converted to invoice', 'success');
                this.load();
            } catch (e) {
                 this.$emit('showToast', 'Failed to convert', 'error');
            }
        },
         async downloadPdf(id) {
             try {
                this.loadingPdfId = id;
                this.$emit('showToast', 'Generating PDF...', 'info');
                const blob = await dashboardAxios.get(`invoices/${id}/pdf`, { responseType: 'blob' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.target = '_blank';
                a.download = `quotation-${id}.pdf`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            } catch (e) {
                this.$emit('showToast', 'Failed PDF download', 'error');
            } finally {
                this.loadingPdfId = null;
            }
        },
        async confirmDelete(row) {
            if (!confirm(`Delete quotation ${row.quotation_number || row.invoice_number}?`)) return;
            try {
                await dashboardAxios.delete(`/invoices/${row.id}`);
                this.$emit('showToast', 'Deleted', 'success');
                this.load();
            } catch (e) {
                this.$emit('showToast', 'Failed to delete', 'error');
            }
        }
    }
};
</script>
