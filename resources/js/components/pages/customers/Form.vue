<template>
    <div>
        <div class="relative sm:p-4 xs:p-0 w-full h-full md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ isEdit ? 'Edit Customer' : 'New Customer' }}
                    </h3>
                </div>

                <form @submit.prevent="submit">
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <!-- Code -->
                        <div>
                            <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Customer Code <span class="text-gray-400 text-xs">(Auto)</span>
                            </label>
                            <input
                                type="text"
                                id="code"
                                v-model="form.code"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Leave empty to auto-generate"
                            />
                        </div>

                         <!-- Name -->
                         <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Customer Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="PT. ABC Company"
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="customer@email.com"
                            />
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone</label>
                            <input
                                type="text"
                                id="phone"
                                v-model="form.phone"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="+62 812 3456 7890"
                            />
                        </div>

                        <!-- Contact Person -->
                        <div>
                            <label for="contact_person" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Person</label>
                            <input
                                type="text"
                                id="contact_person"
                                v-model="form.contact_person"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="John Doe"
                            />
                        </div>

                        <!-- Contact Phone -->
                         <div>
                            <label for="contact_phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Phone</label>
                            <input
                                type="text"
                                id="contact_phone"
                                v-model="form.contact_phone"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="+62 812 3456 7890"
                            />
                        </div>

                        <!-- Tax ID -->
                        <div>
                            <label for="tax_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tax ID (NPWP)</label>
                            <input
                                type="text"
                                id="tax_id"
                                v-model="form.tax_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="00.000.000.0-000.000"
                            />
                        </div>

                        <!-- Payment Terms -->
                        <div>
                            <label for="payment_terms" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Payment Terms <span class="text-red-500">*</span></label>
                            <select
                                id="payment_terms"
                                v-model="form.payment_terms"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
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
                            <label for="credit_limit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Credit Limit</label>
                            <input
                                type="number"
                                id="credit_limit"
                                v-model="form.credit_limit"
                                step="0.01"
                                min="0"    
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="0.00"
                            />
                        </div>
                        
                         <!-- Status -->
                         <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                             <div class="flex items-center ps-3 border border-gray-200 rounded-sm dark:border-gray-700 h-[42px]">
                                <input 
                                    id="is_active" 
                                    type="checkbox" 
                                    v-model="form.is_active"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                >
                                <label for="is_active" class="w-full py-2 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Active</label>
                            </div>
                        </div>


                        <!-- Address -->
                        <div class="sm:col-span-2">
                            <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                            <textarea
                                id="address"
                                v-model="form.address"
                                rows="3"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Full address..."
                            ></textarea>
                        </div>

                         <!-- Notes -->
                         <div class="sm:col-span-2">
                            <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Additional notes..."
                            ></textarea>
                        </div>

                    </div>

                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-5">
                         <button type="button" @click="$router.push('/app/invoices/customers')"
                            class="text-gray-600 dark:text-white hover:text-white flex items-center justify-center  w-full sm:w-auto  border border-gray-700 dark:border-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center  dark:hover:bg-gray-700 dark:focus:ring-primary-800">
                            <svg class="mr-1 -ml-1 w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m14 8-4 4 4 4" />
                            </svg>
                            <span>Back</span>
                        </button>
                    
                        <button type="submit" 
                            :disabled="loading"
                            class="text-white flex items-center justify-center w-full sm:w-auto bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 disabled:opacity-50"
                        >
                             <svg v-show="!isEdit" class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <svg v-show="isEdit" class="mr-1 -ml-1 w-6 h-6" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 5a1 1 0 0 1 1-1h11.586a1 1 0 0 1 .707.293l2.414 2.414a1 1 0 0 1 .293.707V19a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Z" />
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="1"
                                    d="M8 4h8v4H8V4Zm7 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span>{{ loading ? 'Saving...' : (isEdit ? 'Save Changes' : 'Save') }}</span>
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
                // Ensure values map correctly
                this.form = {
                    ...data,
                    is_active: !!data.is_active // Ensure boolean
                };
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
