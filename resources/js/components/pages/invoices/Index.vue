<template>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-xl">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Invoices</h1>
            </div>

            <div class="mb-4">
                 <select
                    v-model="filters.status"
                    @change="load"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                </select>
            </div>

            <table-molecule
                :columns="columns"
                :data="invoices"
                :loading="loading"
                :server-side="true"
                :total-items="pagination.total"
                :per-page="pagination.per_page"
                :current-page="pagination.current_page"
                :show-add-button="true"
                :with-checkbox="false"
                @page-change="changePage"
                @search="handleSearch"
                @onAdd="navigateToAdd"
                @detail-clicked="navigateToDetail"
                @edit-clicked="navigateToEdit"
                @delete-clicked="confirmDelete"
            >
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

                 <!-- Custom Column: Actions -->
                 <!-- Note: TableMolecule actions prop handles standard actions (edit, delete, view) via dropdown. 
                      However, we have a custom PDF download action. 
                      We can add it to 'additional_action' in the row data. -->

            </table-molecule>
        </div>
    </div>
</template>

<script>
import dashboardAxios from '@/api/dashboardAxios';

export default {
    name: 'InvoiceIndex',
    data() {
        return {
            invoices: [],
            loading: false,
            loadingPdfId: null,
            filters: {
                search: '',
                status: ''
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: 0,
                from: 0,
                to: 0
            },
            columns: [
                {
                    name: 'Invoice #',
                    prop: 'invoice_number',
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
    mounted() {
        this.load();
    },
    methods: {
        async load() {
            this.loading = true;
            try {
                const params = {
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    ...this.filters
                };
                
                const response = await dashboardAxios.get('/invoices', { params });
                const result = response?.data || [];
                
                this.invoices = result.map(inv => ({
                    ...inv,
                    date: inv.invoice_date, // Map for sort/display if needed, though we use slot
                    actions: {
                        canUpdate: true,
                        canDelete: true,
                    },
                    additional_action: [
                        {
                            name: 'Download PDF',
                            action: (row) => this.downloadPdf(row.id)
                        }
                    ]
                }));

                this.pagination = {
                    current_page: response?.current_page || 1,
                    last_page: response?.last_page || 1,
                    per_page: response?.per_page || 10,
                    total: response?.total || 0,
                    from: response?.from || 0,
                    to: response?.to || 0
                };
            } catch (error) {
                console.error('Failed to load invoices:', error);
                this.$emit('showToast', 'Failed to load invoices', 'error');
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
            this.$router.push('/app/invoices/invoices/create');
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
            const map = {
                draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                paid: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                overdue: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                cancelled: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
            };
            return map[status] || 'bg-gray-100 text-gray-800';
        },
        async downloadPdf(id) {
             try {
                this.loadingPdfId = id; // Note: TableMolecule might not show loading state for dropdown items
                this.$emit('showToast', 'Generating PDF...', 'info');
                
                const blob = await dashboardAxios.get(`invoices/${id}/pdf`, {
                    responseType: 'blob',
                    headers: { Accept: 'application/pdf' },
                });

                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.target = '_blank';
                a.download = `invoice-${id}.pdf`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                this.$emit('showToast', 'PDF Downloaded', 'success');
            } catch (e) {
                console.error('PDF download failed', e);
                this.$emit('showToast', 'Failed to generate PDF', 'error');
            } finally {
                this.loadingPdfId = null;
            }
        },
        async confirmDelete(row) {
            if (!confirm(`Are you sure you want to delete invoice "${row.invoice_number}"?`)) {
                return;
            }
            try {
                await dashboardAxios.delete(`/invoices/${row.id}`);
                this.$emit('showToast', 'Invoice deleted successfully', 'success');
                this.load();
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to delete invoice';
                this.$emit('showToast', msg, 'error');
            }
        }
    }
};
</script>
