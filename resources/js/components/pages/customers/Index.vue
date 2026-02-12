<template>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-xl">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Customers</h1>
            </div>

            <table-molecule
                :columns="columns"
                :data="customers"
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
                <!-- Custom Column: Status -->
                <template v-slot:column-status="{ row }">
                    <span
                        :class="row.is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                        class="px-2.5 py-0.5 rounded text-xs font-medium"
                    >
                        {{ row.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </template>

                 <!-- Custom Column: Contact -->
                 <template v-slot:column-contact="{ row }">
                    <div v-if="row.phone">{{ row.phone }}</div>
                    <div v-if="row.contact_person" class="text-xs text-gray-500 dark:text-gray-400">
                        CP: {{ row.contact_person }}
                    </div>
                </template>

                <!-- Custom Column: Name -->
                 <template v-slot:column-name="{ row }">
                    <div class="font-medium text-gray-900 dark:text-white">{{ row.name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ row.email }}</div>
                </template>


            </table-molecule>
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
                per_page: 10,
                total: 0,
                from: 0,
                to: 0
            },
            columns: [
                {
                    name: 'Code',
                    prop: 'code',
                },
                {
                    name: 'Name',
                    prop: 'name',
                },
                {
                    name: 'Contact',
                    prop: 'contact',
                },
                {
                    name: 'Payment Terms',
                    prop: 'payment_terms_label', // We need to map this in data processing
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
                
                const response = await dashboardAxios.get('/customers', { params });
                const result = response?.data || [];
                
                // Map data to match table expectations and add formatted fields
                this.customers = result.map(customer => ({
                    ...customer,
                    payment_terms_label: this.formatPaymentTerms(customer.payment_terms),
                    // Add required actions flags if your backend doesn't provide them, 
                    // or rely on the backend response. 
                    // Assuming TableMolecule expects 'actions' object for built-in buttons
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
                console.error('Failed to load customers:', error);
                this.customers = [];
                this.$emit('showToast', 'Failed to load customers', 'error');
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
            this.$router.push('/app/invoices/customers/create');
        },
        navigateToDetail(row) {
            this.$router.push(`/app/invoices/customers/${row.id}`);
        },
        navigateToEdit(row) {
            this.$router.push(`/app/invoices/customers/${row.id}/edit`);
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
        async confirmDelete(row) {
            // Check if user has confirmation dialog or use browser confirm for now as per reference
             // The reference uses a custom confirmation dialog component. 
             // We can emit an event or use the standard confirm for quick parity if we don't have the ref locally working perfectly yet.
             // But looking at reference `Index.vue`, it imports `confirmation-dialog`.
             // I'll stick to simple confirm for now to match the previous implementation, 
             // or I can implement the dialog if I see it in `App.vue/Global`. 
             // usage: <confirmation-dialog ... />

            if (!confirm(`Are you sure you want to delete customer "${row.name}"?`)) {
                return;
            }
            try {
                await dashboardAxios.delete(`/customers/${row.id}`);
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
