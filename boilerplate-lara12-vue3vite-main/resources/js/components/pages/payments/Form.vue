<template>
    <div class="p-6 max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold dark:text-white">{{ isEdit ? 'Edit Payment' : 'Record Payment' }}</h1>
        </div>

        <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Invoice Selection -->
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium dark:text-gray-200">Invoice <span class="text-red-500">*</span></label>
                    <select
                        v-model="form.invoice_id"
                        @change="onInvoiceChange"
                        required
                        :disabled="isEdit"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Select Invoice</option>
                        <option v-for="inv in unpaidInvoices" :key="inv.id" :value="inv.id">
                            {{ inv.invoice_number }} - {{ inv.customer_name }} (Remaining: {{ formatCurrency(inv.remaining_balance) }})
                        </option>
                    </select>
                    <div v-if="selectedInvoice" class="mt-2 p-3 bg-blue-50 dark:bg-blue-900 rounded text-sm dark:text-gray-200">
                        <div class="font-medium">Invoice Details:</div>
                        <div>Total: {{ formatCurrency(selectedInvoice.total) }}</div>
                        <div>Paid: {{ formatCurrency(selectedInvoice.paid_amount || 0) }}</div>
                        <div class="font-bold">Remaining: {{ formatCurrency(selectedInvoice.remaining_balance) }}</div>
                    </div>
                </div>

                <!-- Payment Date -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Payment Date <span class="text-red-500">*</span></label>
                    <input
                        v-model="form.payment_date"
                        type="date"
                        required
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                    />
                </div>

                <!-- Amount -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Amount <span class="text-red-500">*</span></label>
                    <input
                        v-model="form.amount"
                        type="number"
                        step="0.01"
                        min="0"
                        :max="selectedInvoice?.remaining_balance"
                        required
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="0.00"
                    />
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block mb-2 font-medium dark:text-gray-200">Payment Method <span class="text-red-500">*</span></label>
                    <select
                        v-model="form.payment_method"
                        required
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
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
                    <label class="block mb-2 font-medium dark:text-gray-200">Reference Number</label>
                    <input
                        v-model="form.reference_number"
                        type="text"
                        class="w-full border dark:border-gray-600 px-3 py-2 rounded dark:bg-gray-700 dark:text-white"
                        placeholder="Bank ref, check number, etc"
                    />
                </div>

                <!-- Notes -->
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
                    {{ loading ? 'Saving...' : (isEdit ? 'Update' : 'Record Payment') }}
                </button>
                <button
                    type="button"
                    @click="$router.push('/app/invoices/payments')"
                    class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 px-6 py-2 rounded dark:text-white"
                >
                    Cancel
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { dashboardAxios } from '@/axios';

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
            try {
                const { data } = await dashboardAxios.get('/invoices', {
                    params: { 
                        status: 'INVOICED,PARTIAL_PAID',
                        per_page: 100
                    }
                });
                this.unpaidInvoices = data.data.map(inv => ({
                    ...inv,
                    remaining_balance: inv.total - (inv.paid_amount || 0)
                }));
            } catch (error) {
                console.error('Failed to load invoices:', error);
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
                this.$router.push('/app/invoices/payments');
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
            return 'Rp ' + Number(amount).toLocaleString('id-ID');
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
                this.$router.push('/app/invoices/payments');
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
