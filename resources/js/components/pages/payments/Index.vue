<template>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-xl">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Payments</h1>
            </div>

            <!-- Date Filters -->
            <div class="sm:flex sm:gap-4 mb-4">
                <input
                    v-model="filters.date_from"
                    type="date"
                    @change="load"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full sm:w-auto p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-2 sm:mb-0"
                    placeholder="From Date"
                />
                <input
                    v-model="filters.date_to"
                    type="date"
                    @change="load"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full sm:w-auto p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="To Date"
                />
            </div>

            <table-molecule
                :columns="columns"
                :data="payments"
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
                @edit-clicked="navigateToEdit"
                @delete-clicked="confirmDelete"
            >
                <!-- Custom Column: Date -->
                <template v-slot:column-date="{ row }">
                    {{ formatDate(row.payment_date) }}
                </template>

                <!-- Custom Column: Invoice -->
                <template v-slot:column-invoice="{ row }">
                    <span class="font-medium text-gray-900 dark:text-white">{{ row.invoice_number }}</span>
                </template>

                <!-- Custom Column: Amount -->
                <template v-slot:column-amount="{ row }">
                    <div class="font-medium text-right">{{ formatCurrency(row.amount) }}</div>
                </template>

                <!-- Custom Column: Method -->
                <template v-slot:column-method="{ row }">
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                        {{ formatMethod(row.payment_method) }}
                    </span>
                </template>
            </table-molecule>
        </div>
    </div>
</template>

<script>
import dashboardAxios from '@/api/dashboardAxios';

export default {
    name: 'PaymentIndex',
    data() {
        return {
            payments: [],
            loading: false,
            filters: {
                search: '',
                date_from: '',
                date_to: ''
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
                { name: 'Payment #', prop: 'payment_number' },
                { name: 'Date', prop: 'date' },
                { name: 'Invoice #', prop: 'invoice' },
                { name: 'Amount', prop: 'amount' },
                { name: 'Method', prop: 'method' },
                { name: 'Action', prop: 'actions' },
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
                const response = await dashboardAxios.get('/payments', { params });
                const result = response?.data || [];

                this.payments = result.map(payment => ({
                    ...payment,
                    invoice_number: payment.invoice?.invoice_number || '-',
                    actions: {
                        canUpdate: true,
                        canDelete: true,
                    }
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
                console.error('Failed to load payments:', error);
                this.payments = [];
                this.$emit('showToast', 'Failed to load payments', 'error');
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
            this.$router.push({ name: 'payments-create' });
        },
        navigateToEdit(row) {
            this.$router.push({ name: 'payments-edit', params: { id: row.id } });
        },
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        },
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
        },
        formatMethod(method) {
            const map = {
                cash: 'Cash',
                bank_transfer: 'Bank Transfer',
                check: 'Check',
                giro: 'Giro',
                credit_card: 'Credit Card',
                other: 'Other'
            };
            return map[method] || method;
        },
        async confirmDelete(row) {
            if (!confirm('Are you sure you want to delete this payment?')) return;
            try {
                await dashboardAxios.delete(`/payments/${row.id}`);
                this.$emit('showToast', 'Payment deleted', 'success');
                this.load();
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to delete payment';
                this.$emit('showToast', msg, 'error');
            }
        }
    }
};
</script>
