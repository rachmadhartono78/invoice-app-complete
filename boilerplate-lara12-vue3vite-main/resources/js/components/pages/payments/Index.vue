<template>
    <div class="p-6">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold dark:text-white">Payments</h1>
            <button
                @click="$router.push('/app/invoices/payments/create')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Record Payment
            </button>
        </div>

        <!-- Filters -->
        <div class="flex gap-4 mb-4">
            <input
                v-model="filters.search"
                @input="load"
                placeholder="Search by payment number, invoice..."
                class="border dark:border-gray-600 px-3 py-2 rounded flex-1 dark:bg-gray-700 dark:text-white"
            />
            <input
                v-model="filters.date_from"
                type="date"
                @change="load"
                class="border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
            />
            <input
                v-model="filters.date_to"
                type="date"
                @change="load"
                class="border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
            />
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded shadow overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-3 text-left dark:text-gray-200">Payment #</th>
                        <th class="p-3 text-left dark:text-gray-200">Date</th>
                        <th class="p-3 text-left dark:text-gray-200">Invoice #</th>
                        <th class="p-3 text-right dark:text-gray-200">Amount</th>
                        <th class="p-3 text-left dark:text-gray-200">Method</th>
                        <th class="p-3 text-center dark:text-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading" class="border-t dark:border-gray-700">
                        <td colspan="6" class="p-4 text-center dark:text-gray-300">Loading...</td>
                    </tr>
                    <tr v-else-if="!payments.length" class="border-t dark:border-gray-700">
                        <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-400">No payments found</td>
                    </tr>
                    <tr
                        v-else
                        v-for="payment in payments"
                        :key="payment.id"
                        class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <td class="p-3 dark:text-gray-300">{{ payment.payment_number }}</td>
                        <td class="p-3 dark:text-gray-300">{{ formatDate(payment.payment_date) }}</td>
                        <td class="p-3 dark:text-gray-300">{{ payment.invoice?.invoice_number }}</td>
                        <td class="p-3 text-right dark:text-gray-300">{{ formatCurrency(payment.amount) }}</td>
                        <td class="p-3 dark:text-gray-300">{{ formatMethod(payment.payment_method) }}</td>
                        <td class="p-3">
                            <div class="flex gap-2 justify-center">
                                <button
                                    @click="$router.push(`/app/invoices/payments/${payment.id}`)"
                                    class="text-blue-600 dark:text-blue-400"
                                    title="View"
                                >
                                    👁
                                </button>
                                <button
                                    @click="$router.push(`/app/invoices/payments/${payment.id}/edit`)"
                                    class="text-green-600 dark:text-green-400"
                                    title="Edit"
                                >
                                    ✏️
                                </button>
                                <button
                                    @click="confirmDelete(payment)"
                                    class="text-red-600 dark:text-red-400"
                                    title="Delete"
                                >
                                    🗑
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
                per_page: 15,
                total: 0,
                from: 0,
                to: 0
            }
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
                const { data } = await dashboardAxios.get('/payments', { params });
                this.payments = data.data || [];
                this.pagination = {
                    current_page: data.current_page || 1,
                    last_page: data.last_page || 1,
                    per_page: data.per_page || 15,
                    total: data.total || 0,
                    from: data.from || 0,
                    to: data.to || 0
                };
            } catch (error) {
                console.error('Failed to load payments:', error);
                this.payments = [];
                const errorMsg = error.response?.data?.message || 'Failed to load payments';
                this.$emit('showToast', errorMsg, 'error');
            } finally {
                this.loading = false;
            }
        },
        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.load();
            }
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID');
        },
        formatCurrency(amount) {
            return 'Rp ' + Number(amount).toLocaleString('id-ID');
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
        async confirmDelete(payment) {
            if (!confirm(`Are you sure you want to delete this payment?`)) {
                return;
            }
            try {
                await dashboardAxios.delete(`/payments/${payment.id}`);
                this.$emit('showToast', 'Payment deleted successfully', 'success');
                this.load();
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to delete payment';
                this.$emit('showToast', msg, 'error');
            }
        }
    }
};
</script>
