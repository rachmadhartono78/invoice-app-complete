<template>
    <div>
        <div class="relative sm:p-4 xs:p-0 w-full h-full md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ isEdit ? 'Edit Item' : 'New Item' }}
                    </h3>
                </div>

                <form @submit.prevent="submit">
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                            <select
                                id="category_id"
                                v-model="form.category_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            >
                                <option value="">Select Category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Code -->
                        <div>
                            <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Item Code <span class="text-gray-400 text-xs">(Auto)</span>
                            </label>
                            <input
                                type="text"
                                id="code"
                                v-model="form.code"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Leave empty to auto-generate"
                            />
                        </div>

                        <!-- Name -->
                        <div class="sm:col-span-2">
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Item Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Item name"
                            />
                        </div>

                        <!-- Unit -->
                        <div>
                            <label for="unit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Unit <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="unit"
                                v-model="form.unit"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="pcs, kg, box, etc."
                            />
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Price <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="price"
                                v-model="form.price"
                                min="0"
                                step="0.01"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="0.00"
                            />
                        </div>

                        <!-- Initial Stock -->
                        <div>
                            <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Initial Stock</label>
                            <input
                                type="number"
                                id="quantity"
                                v-model="form.quantity"
                                min="0"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="0"
                            />
                        </div>

                        <!-- Storage Area -->
                        <div>
                            <label for="area" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Storage Area</label>
                            <input
                                type="text"
                                id="area"
                                v-model="form.area"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Shelf A, Warehouse 1, etc."
                            />
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Item description..."
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-5">
                        <button type="button" @click="$router.push('/app/invoices/items')"
                            class="text-gray-600 dark:text-white hover:text-white flex items-center justify-center w-full sm:w-auto border border-gray-700 dark:border-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:hover:bg-gray-700 dark:focus:ring-primary-800">
                            <svg class="mr-1 -ml-1 w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14 8-4 4 4 4" />
                            </svg>
                            <span>Back</span>
                        </button>

                        <button type="submit"
                            :disabled="loading"
                            class="text-white flex items-center justify-center w-full sm:w-auto bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 disabled:opacity-50"
                        >
                            <svg v-show="!isEdit" class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg v-show="isEdit" class="mr-1 -ml-1 w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 0 1 1-1h11.586a1 1 0 0 1 .707.293l2.414 2.414a1 1 0 0 1 .293.707V19a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Z" />
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="1" d="M8 4h8v4H8V4Zm7 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span>{{ loading ? 'Saving...' : (isEdit ? 'Save Changes' : 'Create') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
                this.form = { ...data.data };
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
