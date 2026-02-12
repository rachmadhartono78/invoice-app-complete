<template>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-xl">
        <div class="w-full mb-1">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Items & Services</h1>
                <button
                    @click="showImportModal = true"
                    class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2.5 flex items-center gap-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import CSV
                </button>
            </div>

            <!-- Category Filter -->
            <div class="mb-4">
                <select
                    v-model="filters.category_id"
                    @change="load"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full sm:w-auto p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                    <option value="">All Categories</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <table-molecule
                :columns="columns"
                :data="items"
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
                <!-- Custom Column: Name -->
                <template v-slot:column-name="{ row }">
                    <div class="font-medium text-gray-900 dark:text-white">{{ row.name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ row.code || 'NO-CODE' }}</div>
                </template>

                 <!-- Custom Column: Category -->
                <template v-slot:column-category="{ row }">
                    <span v-if="row.category_name" class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                        {{ row.category_name }}
                    </span>
                    <span v-else class="text-gray-400 text-xs">Uncategorized</span>
                </template>

                <!-- Custom Column: Price -->
                <template v-slot:column-price="{ row }">
                    <div class="font-medium text-primary-600 dark:text-primary-400">{{ formatCurrency(row.price) }}</div>
                    <div class="text-xs text-gray-400">per {{ row.unit }}</div>
                </template>

                 <!-- Custom Column: Stock -->
                 <template v-slot:column-stock="{ row }">
                    <span class="font-medium">{{ row.quantity || 0 }}</span>
                </template>

                <!-- Custom Column: Status -->
                <template v-slot:column-status="{ row }">
                    <span
                        :class="row.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                        class="px-2.5 py-0.5 rounded text-xs font-medium"
                    >
                        {{ row.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </template>
            </table-molecule>
        </div>
        
        <ImportModal
            :show="showImportModal"
            @close="showImportModal = false"
            @success="onImportSuccess"
        />
    </div>
</template>

<script>
import dashboardAxios from '@/api/dashboardAxios';
import ImportModal from './ImportModal.vue';

export default {
    name: 'ItemIndex',
    components: { ImportModal },
    data() {
        return {
            items: [],
            categories: [],
            loading: false,
            showImportModal: false,
            filters: {
                search: '',
                category_id: ''
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
                { name: 'Name', prop: 'name' },
                { name: 'Category', prop: 'category' },
                { name: 'Price', prop: 'price' },
                { name: 'Stock', prop: 'stock' },
                { name: 'Status', prop: 'status' },
                { name: 'Action', prop: 'actions' },
            ],
        };
    },
    mounted() {
        this.loadCategories();
        this.load();
    },
    methods: {
        async loadCategories() {
            try {
                const response = await dashboardAxios.get('/items/categories');
                this.categories = response?.data || [];
            } catch (error) {
                console.error('Failed to load categories:', error);
                this.categories = [];
            }
        },
        async load() {
            this.loading = true;
            try {
                const params = {
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    ...this.filters
                };
                const response = await dashboardAxios.get('/items', { params });
                const result = response?.data || [];
                
                this.items = result.map(item => ({
                    ...item,
                    category_name: item.category?.name || '',
                    stock: item.quantity || 0,
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
                console.error('Failed to load items:', error);
                this.items = [];
                this.$emit('showToast', 'Failed to load items', 'error');
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
            this.$router.push('/app/invoices/items/create');
        },
        navigateToEdit(row) {
            this.$router.push(`/app/invoices/items/${row.id}/edit`);
        },
        onImportSuccess() {
            this.showImportModal = false;
            this.$emit('showToast', 'Items imported successfully!', 'success');
            this.load();
            this.loadCategories();
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        },
        async confirmDelete(row) {
            if (!confirm(`Are you sure you want to delete item "${row.name}"?`)) {
                return;
            }
            try {
                await dashboardAxios.delete(`/items/${row.id}`);
                this.$emit('showToast', 'Item deleted successfully', 'success');
                this.load();
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to delete item';
                this.$emit('showToast', msg, 'error');
            }
        }
    }
};
</script>
