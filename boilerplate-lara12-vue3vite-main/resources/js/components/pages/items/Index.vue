<template>
    <div class="p-6">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold dark:text-white">Items & Services</h1>
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

        <!-- Filters -->
        <div class="flex gap-4 mb-4">
            <input
                v-model="filters.search"
                @input="load"
                placeholder="Search by name or code..."
                class="border dark:border-gray-600 px-3 py-2 rounded flex-1 dark:bg-gray-700 dark:text-white"
            />
            <select
                v-model="filters.category_id"
                @change="load"
                class="border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
            >
                <option value="">All Categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded shadow overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-3 text-left dark:text-gray-200">Code</th>
                        <th class="p-3 text-left dark:text-gray-200">Name</th>
                        <th class="p-3 text-left dark:text-gray-200">Category</th>
                        <th class="p-3 text-left dark:text-gray-200">Unit</th>
                        <th class="p-3 text-right dark:text-gray-200">Price</th>
                        <th class="p-3 text-right dark:text-gray-200">Stock</th>
                        <th class="p-3 text-center dark:text-gray-200">Status</th>
                        <th class="p-3 text-center dark:text-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading" class="border-t dark:border-gray-700">
                        <td colspan="8" class="p-4 text-center dark:text-gray-300">Loading...</td>
                    </tr>
                    <tr v-else-if="!items.length" class="border-t dark:border-gray-700">
                        <td colspan="8" class="p-4 text-center text-gray-500 dark:text-gray-400">No items found</td>
                    </tr>
                    <tr
                        v-else
                        v-for="item in items"
                        :key="item.id"
                        class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <td class="p-3 dark:text-gray-300">{{ item.code || '-' }}</td>
                        <td class="p-3 dark:text-gray-300">
                            <div class="font-medium">{{ item.name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ item.description }}</div>
                        </td>
                        <td class="p-3 dark:text-gray-300">{{ item.category ? item.category.name : '-' }}</td>
                        <td class="p-3 dark:text-gray-300">{{ item.unit }}</td>
                        <td class="p-3 text-right dark:text-gray-300">{{ formatCurrency(item.price) }}</td>
                        <td class="p-3 text-right dark:text-gray-300">{{ item.quantity }}</td>
                        <td class="p-3 text-center">
                            <span
                                :class="item.is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                class="px-2 py-1 rounded text-xs"
                            >
                                {{ item.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="flex gap-2 justify-center">
                                <button
                                    @click="$router.push(`/app/invoices/items/${item.id}/edit`)"
                                    class="text-green-600 dark:text-green-400 hover:text-green-800"
                                    title="Edit"
                                >
                                    ✏️
                                </button>
                                <button
                                    @click="confirmDelete(item)"
                                    class="text-red-600 dark:text-red-400 hover:text-red-800"
                                    title="Deactivate"
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
    name: 'ItemIndex',
    data() {
        return {
            items: [],
            categories: [],
            loading: false,
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
    methods: {
        async loadCategories() {
            try {
                const { data } = await dashboardAxios.get('/items/categories');
                this.categories = data.data; // API returns paginated data, maybe logic needs adjustment if lots of categories
            } catch (error) {
                console.error('Failed to load categories:', error);
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
                const { data } = await dashboardAxios.get('/items', { params });
                this.items = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    per_page: data.per_page,
                    total: data.total,
                    from: data.from,
                    to: data.to
                };
            } catch (error) {
                console.error('Failed to load items:', error);
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
