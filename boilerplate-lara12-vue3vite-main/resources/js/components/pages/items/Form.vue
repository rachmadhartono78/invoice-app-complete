<template>
    <div class="p-6 max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold dark:text-white">{{ isEdit ? 'Edit Item' : 'New Item' }}</h1>
        </div>

        <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Category -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Category</label>
                    <select
                        v-model="form.category_id"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Select Category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <!-- Code -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Item Code</label>
                    <input
                        v-model="form.code"
                        type="text"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="Leave empty to auto-generate"
                    />
                </div>

                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium dark:text-gray-200">Item Name <span class="text-red-500">*</span></label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="Item name"
                    />
                </div>

                <!-- Unit -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Unit <span class="text-red-500">*</span></label>
                    <input
                        v-model="form.unit"
                        type="text"
                        required
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="pcs, kg, box, etc."
                    />
                </div>

                <!-- Price -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Price <span class="text-red-500">*</span></label>
                    <input
                        v-model="form.price"
                        type="number"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="0.00"
                    />
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Initial Stock</label>
                    <input
                        v-model="form.quantity"
                        type="number"
                        min="0"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="0"
                    />
                </div>

                <!-- Area -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Storage Area</label>
                    <input
                        v-model="form.area"
                        type="text"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="Shelf A, Warehouse 1, etc."
                    />
                </div>

                <!-- Status -->
                <!-- <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Status</label>
                    <div class="flex items-center">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="w-4 h-4 text-blue-600 rounded"
                        />
                        <span class="ml-2 dark:text-gray-300">Active</span>
                    </div>
                </div> -->

                <!-- Description (Full Width) -->
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium dark:text-gray-200">Description</label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="Item description..."
                    ></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 mt-6">
                <button
                    type="submit"
                    :disabled="loading"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded disabled:opacity-50"
                >
                    {{ loading ? 'Saving...' : (isEdit ? 'Update' : 'Create') }}
                </button>
                <button
                    type="button"
                    @click="$router.push('/app/invoices/items')"
                    class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 px-6 py-2 rounded dark:text-white"
                >
                    Cancel
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import dashboardAxios from '@/api/dashboardAxios';

export default {
    name: 'ItemForm',
    data() {
        return {
            loading: false,
            isEdit: false,
            itemId: null,
            categories: [],
            form: {
                category_id: '',
                code: '',
                name: '',
                unit: 'pcs',
                price: 0,
                quantity: 0,
                area: '',
                description: '',
                is_active: true
            }
        };
    },
    mounted() {
        this.itemId = this.$route.params.id;
        this.isEdit = !!this.itemId;
        this.loadCategories();
        if (this.isEdit) {
            this.loadItem();
        }
    },
    methods: {
        async loadCategories() {
            try {
                const { data } = await dashboardAxios.get('/items/categories');
                this.categories = data.data; 
            } catch (error) {
                console.error('Failed to load categories:', error);
            }
        },
        async loadItem() {
            this.loading = true;
            try {
                const { data } = await dashboardAxios.get(`/items/${this.itemId}`);
                this.form = { ...data.data }; // Match API response structure
            } catch (error) {
                console.error('Failed to load item:', error);
                this.$emit('showToast', 'Failed to load item', 'error');
                this.$router.push('/app/invoices/items');
            } finally {
                this.loading = false;
            }
        },
        async submit() {
            this.loading = true;
            try {
                // Ensure nullable fields are null if empty string (optional, Laravel usually handles empty strings for nullable if not validated as required)
                const payload = { ...this.form };
                if (!payload.code) delete payload.code;
                if (!payload.category_id) delete payload.category_id;

                if (this.isEdit) {
                    await dashboardAxios.put(`/items/${this.itemId}`, payload);
                    this.$emit('showToast', '✅ Item updated successfully', 'success');
                } else {
                    await dashboardAxios.post('/items', payload);
                    this.$emit('showToast', '✅ Item created successfully', 'success');
                }
                this.$router.push('/app/invoices/items');
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to save item';
                this.$emit('showToast', msg, 'error');
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>
