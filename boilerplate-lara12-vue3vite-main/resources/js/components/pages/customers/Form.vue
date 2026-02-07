<template>
    <div class="p-6 max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold dark:text-white">{{ isEdit ? 'Edit Customer' : 'New Customer' }}</h1>
        </div>

        <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Code -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Customer Code <span class="text-red-500">*</span></label>
                    <input
                        v-model="form.code"
                        type="text"
                        required
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="CUST001"
                    />
                </div>

                <!-- Name -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Customer Name <span class="text-red-500">*</span></label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="PT. ABC Company"
                    />
                </div>

                <!-- Email -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="customer@email.com"
                    />
                </div>

                <!-- Phone -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Phone</label>
                    <input
                        v-model="form.phone"
                        type="text"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="+62 812 3456 7890"
                    />
                </div>

                <!-- Contact Person -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Contact Person</label>
                    <input
                        v-model="form.contact_person"
                        type="text"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="John Doe"
                    />
                </div>

                <!-- Contact Phone -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Contact Phone</label>
                    <input
                        v-model="form.contact_phone"
                        type="text"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="+62 812 3456 7890"
                    />
                </div>

                <!-- Tax ID -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Tax ID (NPWP)</label>
                    <input
                        v-model="form.tax_id"
                        type="text"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="00.000.000.0-000.000"
                    />
                </div>

                <!-- Payment Terms -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Payment Terms <span class="text-red-500">*</span></label>
                    <select
                        v-model="form.payment_terms"
                        required
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                    >
                        <option value="cash">Cash</option>
                        <option value="net_7">Net 7 Days</option>
                        <option value="net_14">Net 14 Days</option>
                        <option value="net_30">Net 30 Days</option>
                        <option value="net_45">Net 45 Days</option>
                        <option value="net_60">Net 60 Days</option>
                    </select>
                </div>

                <!-- Credit Limit -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Credit Limit</label>
                    <input
                        v-model="form.credit_limit"
                        type="number"
                        step="0.01"
                        min="0"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="0.00"
                    />
                </div>

                <!-- Status -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Status</label>
                    <div class="flex items-center">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="w-4 h-4 text-blue-600 rounded"
                        />
                        <span class="ml-2 dark:text-gray-300">Active</span>
                    </div>
                </div>

                <!-- Address (Full Width) -->
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium dark:text-gray-200">Address</label>
                    <textarea
                        v-model="form.address"
                        rows="3"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="Full address..."
                    ></textarea>
                </div>

                <!-- Notes (Full Width) -->
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium dark:text-gray-200">Notes</label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="Additional notes..."
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
                    @click="$router.push('/app/invoices/customers')"
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
    name: 'CustomerForm',
    data() {
        return {
            loading: false,
            isEdit: false,
            customerId: null,
            form: {
                code: '',
                name: '',
                email: '',
                phone: '',
                contact_person: '',
                contact_phone: '',
                tax_id: '',
                payment_terms: 'net_30',
                credit_limit: 0,
                address: '',
                notes: '',
                is_active: true
            }
        };
    },
    mounted() {
        this.customerId = this.$route.params.id;
        this.isEdit = !!this.customerId;
        if (this.isEdit) {
            this.loadCustomer();
        }
    },
    methods: {
        async loadCustomer() {
            this.loading = true;
            try {
                const { data } = await dashboardAxios.get(`/customers/${this.customerId}`);
                this.form = { ...data };
            } catch (error) {
                console.error('Failed to load customer:', error);
                this.$emit('showToast', 'Failed to load customer', 'error');
                this.$router.push('/app/invoices/customers');
            } finally {
                this.loading = false;
            }
        },
        async submit() {
            this.loading = true;
            try {
                if (this.isEdit) {
                    await dashboardAxios.put(`/customers/${this.customerId}`, this.form);
                    this.$emit('showToast', '✅ Customer updated successfully', 'success');
                } else {
                    await dashboardAxios.post('/customers', this.form);
                    this.$emit('showToast', '✅ Customer created successfully', 'success');
                }
                this.$router.push('/app/invoices/customers');
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to save customer';
                this.$emit('showToast', msg, 'error');
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>
