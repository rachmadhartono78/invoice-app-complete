<template>
    <div>
        <div class="relative sm:p-4 xs:p-0 w-full h-full md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ isEdit ? 'Edit Payment' : 'Record Payment' }}
                    </h3>
                </div>

                <!-- Loading State -->
                <div v-if="loading && !isEdit && !unpaidInvoices.length" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Loading invoices...</p>
                </div>

                <!-- No Unpaid Invoices -->
                <div v-else-if="!isEdit && !unpaidInvoices.length && !loading" class="py-8 text-center">
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 max-w-lg mx-auto">
                        <div class="text-4xl mb-3">📝</div>
                        <h2 class="text-lg font-bold text-yellow-800 dark:text-yellow-400 mb-2">All Invoices Paid</h2>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-4">
                            All invoices have been fully paid or there are no invoices pending payment.
                        </p>
                        <div class="flex gap-3 justify-center">
                            <button
                                @click="$router.push({ name: 'invoices-index' })"
                                class="text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-4 py-2.5"
                            >
                                View Invoices
                            </button>
                            <button
                                @click="$router.push({ name: 'payments-index' })"
                                class="text-gray-600 dark:text-white border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium rounded-lg text-sm px-4 py-2.5"
                            >
                                Back
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form v-if="isEdit || (unpaidInvoices.length > 0)" @submit.prevent="submit">
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <!-- Invoice Selection -->
                        <div class="sm:col-span-2">
                            <label for="invoice_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Invoice <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="invoice_id"
                                v-model="form.invoice_id"
                                @change="onInvoiceChange"
                                required
                                :disabled="isEdit"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-60"
                            >
                                <option value="">Select Invoice</option>
                                <option v-for="inv in unpaidInvoices" :key="inv.id" :value="inv.id">
                                    {{ inv.invoice_number }} - {{ inv.customer_name }} (Remaining: {{ formatCurrency(inv.remaining_balance) }})
                                </option>
                            </select>

                            <!-- Invoice Summary -->
                            <div v-if="selectedInvoice" class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                                <div class="font-medium text-blue-800 dark:text-blue-300 mb-1">Invoice Details:</div>
                                <div class="grid grid-cols-3 gap-2 text-gray-700 dark:text-gray-300">
                                    <div>Total: <span class="font-semibold">{{ formatCurrency(selectedInvoice.total) }}</span></div>
                                    <div>Paid: <span class="font-semibold">{{ formatCurrency(selectedInvoice.paid_amount || 0) }}</span></div>
                                    <div>Remaining: <span class="font-bold text-blue-700 dark:text-blue-400">{{ formatCurrency(selectedInvoice.remaining_balance) }}</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Date -->
                        <div>
                            <label for="payment_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="payment_date"
                                v-model="form.payment_date"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="amount"
                                v-model="form.amount"
                                step="0.01"
                                min="0"
                                :max="selectedInvoice?.remaining_balance"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="0.00"
                            />
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="payment_method"
                                v-model="form.payment_method"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                                <option value="giro">Giro</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Reference Number -->
                        <div>
                            <label for="reference_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference Number</label>
                            <input
                                type="text"
                                id="reference_number"
                                v-model="form.reference_number"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Bank ref, check number, etc"
                            />
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-2">
                            <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Additional notes..."
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-5">
                        <button type="button" @click="$router.push({ name: 'payments-index' })"
                            class="text-gray-600 dark:text-white hover:text-white flex items-center justify-center w-full sm:w-auto border border-gray-700 dark:border-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:hover:bg-gray-700 dark:focus:ring-primary-800">
                            <svg class="mr-1 -ml-1 w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14 8-4 4 4 4" />
                            </svg>
                            <span>Back</span>
                        </button>

                        <button type="submit"
                            :disabled="loading || !form.invoice_id"
                            class="text-white flex items-center justify-center w-full sm:w-auto bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-show="!isEdit" class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg v-show="isEdit" class="mr-1 -ml-1 w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 0 1 1-1h11.586a1 1 0 0 1 .707.293l2.414 2.414a1 1 0 0 1 .293.707V19a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Z" />
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="1" d="M8 4h8v4H8V4Zm7 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span>{{ loading ? 'Saving...' : (isEdit ? 'Save Changes' : 'Record Payment') }}</span>
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
    name: 'PaymentForm',
    data() {
        return {
            loading: false,
            isEdit: false,
            paymentId: null,
            unpaidInvoices: [],
            selectedInvoice: null,
            form: {
                invoice_id: '',
                payment_date: new Date().toISOString().split('T')[0],
                amount: 0,
                payment_method: 'bank_transfer',
                reference_number: '',
                notes: ''
            }
        };
    },
    mounted() {
        this.paymentId = this.$route.params.id;
        this.isEdit = !!this.paymentId;
        
        if (this.isEdit) {
            this.loadPayment();
        } else {
            this.loadUnpaidInvoices();
        }
    },
    methods: {
        async loadUnpaidInvoices() {
            this.loading = true;
            try {
                const { data } = await dashboardAxios.get('/invoices/unpaid');
                this.unpaidInvoices = Array.isArray(data) ? data : [];
            } catch (error) {
                console.error('Failed to load invoices:', error);
                this.$emit('showToast', 'Failed to load invoices', 'error');
            } finally {
                this.loading = false;
            }
        },
        async loadPayment() {
            this.loading = true;
            try {
                const { data } = await dashboardAxios.get(`/payments/${this.paymentId}`);
                this.form = {
                    invoice_id: data.invoice_id,
                    payment_date: data.payment_date,
                    amount: data.amount,
                    payment_method: data.payment_method,
                    reference_number: data.reference_number || '',
                    notes: data.notes || ''
                };
                this.selectedInvoice = data.invoice;
            } catch (error) {
                console.error('Failed to load payment:', error);
                this.$emit('showToast', 'Failed to load payment', 'error');
                this.$router.push({ name: 'payments-index' });
            } finally {
                this.loading = false;
            }
        },
        onInvoiceChange() {
            this.selectedInvoice = this.unpaidInvoices.find(inv => inv.id === parseInt(this.form.invoice_id));
            if (this.selectedInvoice) {
                this.form.amount = this.selectedInvoice.remaining_balance;
            }
        },
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
        },
        async submit() {
            this.loading = true;
            try {
                if (this.isEdit) {
                    await dashboardAxios.put(`/payments/${this.paymentId}`, this.form);
                    this.$emit('showToast', '✅ Payment updated successfully', 'success');
                } else {
                    await dashboardAxios.post('/payments', this.form);
                    this.$emit('showToast', '✅ Payment recorded successfully', 'success');
                }
                this.$router.push({ name: 'payments-index' });
            } catch (error) {
                const msg = error.response?.data?.message || 'Failed to save payment';
                this.$emit('showToast', msg, 'error');
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>
