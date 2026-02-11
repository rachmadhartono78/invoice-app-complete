<template>
    <div class="p-6">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold dark:text-white">Items & Services (Katalog)</h1>
            <div class="flex gap-2">
                <button
                    @click="showImportModal = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import CSV
                </button>
                <button
                    @click="$router.push('/app/invoices/items/create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Item
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex gap-4 mb-4 sticky top-0 bg-gray-50 dark:bg-gray-900 z-20 py-2">
            <input
                v-model="filters.search"
                @input="load"
                placeholder="Search by name or code..."
                class="border dark:border-gray-600 px-3 py-2 rounded flex-1 dark:bg-gray-700 dark:text-white shadow-sm"
            />
            <select
                v-model="filters.category_id"
                @change="load"
                class="border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white shadow-sm"
            >
                <option value="">All Categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-500">Loading items...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="!items.length" class="text-center py-12 bg-white dark:bg-gray-800 rounded shadow">
            <p class="text-gray-500 dark:text-gray-400">No items found matching your criteria.</p>
        </div>

        <!-- Grouped Grid View -->
        <div v-else>
            <div v-for="(groupItems, area) in groupedItems" :key="area" class="mb-8">
                <h3 class="font-bold text-lg text-gray-700 dark:text-gray-200 border-b pb-2 mb-4 sticky top-14 bg-gray-50 dark:bg-gray-900 z-10 flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ groupItems.length }}</span>
                    {{ area || 'Uncategorized' }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div 
                        v-for="item in groupItems" 
                        :key="item.id" 
                        class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow relative group"
                    >
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            <span
                                :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider"
                            >
                                {{ item.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="font-bold text-gray-800 dark:text-white pr-16 mb-1">{{ item.name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-mono bg-gray-50 dark:bg-gray-700 inline-block px-1 rounded">
                            {{ item.code || 'NO-CODE' }}
                        </div>
                        
                        <div class="flex justify-between items-end mt-4">
                            <div>
                                <div class="text-xs text-gray-500">Price</div>
                                <div class="font-bold text-blue-600 dark:text-blue-400">
                                    {{ formatCurrency(item.price) }}
                                </div>
                                <div class="text-[10px] text-gray-400">per {{ item.unit }}</div>
                            </div>
                            
                            <div class="flex gap-2 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    @click="$router.push(`/app/invoices/items/${item.id}/edit`)"
                                    class="p-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100"
                                    title="Edit"
                                >
                                    ✏️
                                </button>
                                <button
                                    @click="confirmDelete(item)"
                                    class="p-2 bg-red-50 text-red-600 rounded hover:bg-red-100"
                                    title="Delete"
                                >
                                    🗑
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > 0 && !loading" class="mt-8 flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded shadow">
            <div class="text-sm text-gray-600 dark:text-gray-300">
                Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
            </div>
            <div class="flex gap-2">
                <button
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    class="px-3 py-1 border rounded disabled:opacity-50 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    Previous
                </button>
                <div class="px-3 py-1 bg-blue-50 text-blue-600 rounded font-bold">
                    {{ pagination.current_page }}
                </div>
                <button
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="px-3 py-1 border rounded disabled:opacity-50 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    Next
                </button>
            </div>
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
                per_page: 15,
                total: 0,
                from: 0,
                to: 0
            }
        };
    },
    mounted() {
        this.loadCategories();
        this.load();
    },
    computed: {
        groupedItems() {
            const groups = {};
            this.items.forEach(item => {
                const area = item.category ? item.category.name : 'Uncategorized';
                if (!groups[area]) groups[area] = [];
                groups[area].push(item);
            });
            // Optional: Sort keys
            return Object.keys(groups).sort().reduce(
                (obj, key) => { 
                    obj[key] = groups[key]; 
                    return obj;
                }, 
                {}
            );
        }
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
                    per_page: 60, // Optimized: 60 items is a good balance for grid view
                    ...this.filters
                };
                const response = await dashboardAxios.get('/items', { params });
                // dashboardAxios interceptor returns response.data, so 'response' is already the paginated object
                this.items = response?.data || [];
                this.pagination = {
                    current_page: response?.current_page || 1,
                    last_page: response?.last_page || 1,
                    per_page: response?.per_page || 15,
                    total: response?.total || 0,
                    from: response?.from || 0,
                    to: response?.to || 0
                };
            } catch (error) {
                console.error('Failed to load items:', error);
                this.items = []; // Reset to empty array on error
                this.$emit('showToast', 'Failed to load items', 'error');
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
        onImportSuccess() {
            this.showImportModal = false;
            this.$emit('showToast', 'Items imported successfully!', 'success');
            this.load();
            this.loadCategories(); // Reload categories in case valid new ones were created
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        },
        async confirmDelete(item) {
            if (!confirm(`Are you sure you want to delete item "${item.name}"?`)) {
                return;
            }
            try {
                await dashboardAxios.delete(`/items/${item.id}`);
                this.$emit('showToast', 'Item deactivated successfully', 'success');
                this.load();
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to delete item';
                this.$emit('showToast', msg, 'error');
            }
        }
    }
};
</script>
