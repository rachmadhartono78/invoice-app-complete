<template>
    <div class="p-6">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold dark:text-white">Customers</h1>
            <button
                @click="$router.push('/app/invoices/customers/create')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Customer
            </button>
        </div>

        <!-- Filters -->
        <div class="flex gap-4 mb-4">
            <input
                v-model="filters.search"
                @input="load"
                placeholder="Search by name, code, phone, email..."
                class="border dark:border-gray-600 px-3 py-2 rounded flex-1 dark:bg-gray-700 dark:text-white"
            />
            <select
                v-model="filters.is_active"
                @change="load"
                class="border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
            >
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded shadow overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-3 text-left dark:text-gray-200">Code</th>
                        <th class="p-3 text-left dark:text-gray-200">Name</th>
                        <th class="p-3 text-left dark:text-gray-200">Contact</th>
                        <th class="p-3 text-left dark:text-gray-200">Payment Terms</th>
                        <th class="p-3 text-center dark:text-gray-200">Status</th>
                        <th class="p-3 text-center dark:text-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading" class="border-t dark:border-gray-700">
                        <td colspan="6" class="p-4 text-center dark:text-gray-300">Loading...</td>
                    </tr>
                    <tr v-else-if="!customers.length" class="border-t dark:border-gray-700">
                        <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-400">No customers found</td>
                    </tr>
                    <tr
                        v-else
                        v-for="customer in customers"
                        :key="customer.id"
                        class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <td class="p-3 dark:text-gray-300">{{ customer.code }}</td>
                        <td class="p-3 dark:text-gray-300">
                            <div class="font-medium">{{ customer.name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ customer.email }}</div>
                        </td>
                        <td class="p-3 dark:text-gray-300">
                            <div v-if="customer.phone">📞 {{ customer.phone }}</div>
                            <div v-if="customer.contact_person" class="text-sm text-gray-500 dark:text-gray-400">
                                {{ customer.contact_person }}
                            </div>
                        </td>
                        <td class="p-3 dark:text-gray-300">{{ formatPaymentTerms(customer.payment_terms) }}</td>
                        <td class="p-3 text-center">
                            <span
                                :class="customer.is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                class="px-2 py-1 rounded text-xs"
                            >
                                {{ customer.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="flex gap-2 justify-center">
                                <button
                                    @click="$router.push(`/app/invoices/customers/${customer.id}`)"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800"
                                    title="View"
                                >
                                    👁
                                </button>
                                <button
                                    @click="$router.push(`/app/invoices/customers/${customer.id}/edit`)"
                                    class="text-green-600 dark:text-green-400 hover:text-green-800"
                                    title="Edit"
                                >
                                    ✏️
                                </button>
                                <button
                                    @click="confirmDelete(customer)"
                                    class="text-red-600 dark:text-red-400 hover:text-red-800"
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
    name: 'CustomerIndex',
    data() {
        return {
            customers: [],
            loading: false,
            filters: {
                search: '',
                is_active: ''
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
                // dashboardAxios interceptor returns response.data directly
                const response = await dashboardAxios.get('/customers', { params });
                this.customers = response?.data || [];
                this.pagination = {
                    current_page: response?.current_page || 1,
                    last_page: response?.last_page || 1,
                    per_page: response?.per_page || 15,
                    total: response?.total || 0,
                    from: response?.from || 0,
                    to: response?.to || 0
                };
            } catch (error) {
                console.error('Failed to load customers:', error);
                this.customers = [];
                this.$emit('showToast', 'Failed to load customers', 'error');
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
        formatPaymentTerms(terms) {
            const map = {
                cash: 'Cash',
                net_7: 'Net 7',
                net_14: 'Net 14',
                net_30: 'Net 30',
                net_45: 'Net 45',
                net_60: 'Net 60'
            };
            return map[terms] || terms;
        },
        async confirmDelete(customer) {
            if (!confirm(`Are you sure you want to delete customer "${customer.name}"?`)) {
                return;
            }
            try {
                await dashboardAxios.delete(`/customers/${customer.id}`);
                this.$emit('showToast', 'Customer deleted successfully', 'success');
                this.load();
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to delete customer';
                this.$emit('showToast', msg, 'error');
            }
        }
    }
};
</script>
