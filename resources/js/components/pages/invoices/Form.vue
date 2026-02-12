<template>
    <div class="p-4 md:p-6 max-w-7xl mx-auto">
        <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ id ? "Edit Invoice" : "New Invoice" }}
                </h3>
            </div>
        
            <!-- Required Fields Legend -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-2 dark:bg-blue-900/30 dark:border-blue-800">
                <div class="text-sm dark:text-gray-300">
                    <span class="font-semibold">Note: <span class="text-red-600 font-bold text-lg">*</span> = Required Fields</span>
                    <p class="text-xs text-gray-600 mt-1 dark:text-gray-400">Ensure all marked fields are filled before saving.</p>
                </div>
            </div>
            
            <form @submit.prevent="save">
                <div class="grid gap-4 mb-4 sm:grid-cols-3">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Invoice #</label>
                        <input
                            v-model="f.invoice_number"
                            :readonly="true"
                            disabled
                            placeholder="Auto-generated"
                            class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"
                        />
                         <div v-if="!id && nextInvoiceNumber" class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                            Next: {{ nextInvoiceNumber }}
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Invoice Date <span class="text-red-600 font-bold">*</span></label>
                        <input
                            v-model="f.invoice_date"
                            type="date"
                            required
                             :class="[
                                'bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500',
                                errors.invoice_date ? 'border-red-600 bg-red-50 dark:border-red-500 dark:bg-red-900/30' : 'border-gray-300'
                            ]"
                        />
                        <div v-if="errors.invoice_date" class="text-red-600 text-xs mt-1">{{ errors.invoice_date }}</div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Currency</label>
                        <input
                            v-model="f.currency_name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Customer <span class="text-red-600 font-bold">*</span></label>
                        <input
                            v-model="f.customer_name"
                            required
                            :class="[
                                'bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white',
                                errors.customer_name ? 'border-red-600 bg-red-50' : 'border-gray-300'
                            ]"
                            placeholder="Customer Name"
                        />
                        <div v-if="errors.customer_name" class="text-red-600 text-xs mt-1">{{ errors.customer_name }}</div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PO Number</label>
                        <input
                            v-model="f.po_number"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                    <div class="md:col-span-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                        <input
                            v-model="f.customer_address"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Full Address"
                        />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Payment Terms</label>
                        <input
                            v-model="f.payment_terms"
                            placeholder="e.g. COD, Net 30"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Expedition</label>
                        <input
                            v-model="f.expedition"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mt-8 mb-4">
                    <div class="flex justify-between items-end mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Items <span class="text-red-600">*</span></h3>
                        <button 
                            type="button" 
                            @click="showItemSelector = true"
                            class="text-sm bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2 transition-colors duration-200"
                        >
                            <span>📚</span> Open Catalog
                        </button>
                    </div>
                    
                    <div v-if="errors.items" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm dark:bg-red-900/30 dark:border-red-800 dark:text-red-400">
                        ⚠️ {{ errors.items }}
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(it, i) in f.items"
                            :key="i"
                            class="p-4 border border-gray-200 rounded-lg bg-gray-50/50 dark:bg-gray-700/50 dark:border-gray-600"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <!-- Item Search -->
                                <div class="md:col-span-3 relative">
                                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Item <span class="text-red-600">*</span></label>
                                    <input
                                        v-model="itemSearch[i]"
                                        type="text"
                                        placeholder="Search item..."
                                        @focus="itemSearch[i] = itemSearch[i] || ''"
                                        class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                        :class="!it.item_id && Object.keys(errors).length > 0 ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <!-- Search Dropdown -->
                                    <div v-if="itemSearch[i] && getFilteredItems(i).length > 0"
                                        class="absolute top-full left-0 right-0 mt-1 border border-gray-200 bg-white rounded-lg z-20 max-h-48 overflow-y-auto shadow-xl dark:bg-gray-700 dark:border-gray-600">
                                        <div v-for="item in getFilteredItems(i)"
                                            :key="item.id"
                                            @click="selectItemFromSearch(i, item)"
                                            class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm border-b border-gray-100 last:border-b-0 dark:border-gray-600 dark:hover:bg-gray-600">
                                            <div class="font-semibold text-gray-800 dark:text-white">{{ item.name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ item.code }} • {{ item.unit }} • Rp {{ fmt(item.price) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Area</label>
                                    <input
                                        v-model="it.area"
                                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                    />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Code</label>
                                    <input
                                        v-model="it.item_code"
                                        class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                        readonly
                                    />
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Qty <span class="text-red-600">*</span></label>
                                    <input
                                        v-model.number="it.quantity"
                                        type="number"
                                        required
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                    />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Price <span class="text-red-600">*</span></label>
                                    <input
                                        v-model.number="it.unit_price"
                                        type="number"
                                        required
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                    />
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Disc</label>
                                    <input
                                        v-model.number="it.discount"
                                        type="number"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                    />
                                </div>
                                    <div class="md:col-span-1 flex items-center justify-end h-full py-2.5">
                                    <button type="button" @click="deleteItem(i)" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/50 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                            <!-- Subtotal for item -->
                             <div class="text-right mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                Subtotal: {{ fmt((it.quantity || 0) * (it.unit_price || 0) - (it.discount || 0)) }}
                            </div>
                        </div>
                    </div>
                    
                    <button
                        type="button"
                        @click="addNewItem"
                        class="mt-4 text-primary-600 text-sm font-medium hover:text-primary-800 flex items-center gap-1 dark:text-primary-400 dark:hover:text-primary-300"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Single Item
                    </button>
                </div>
                
                <InternalItemSelector 
                    :show="showItemSelector" 
                    :items="masterItems"
                    @close="showItemSelector = false"
                    @confirm="handleItemsSelected"
                />

                <div class="border-t border-gray-200 mt-6 pt-6 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left Calculation -->
                        <div class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Global Discount</label>
                                <input
                                    v-model.number="f.discount"
                                    type="number"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PPN %</label>
                                <input
                                    v-model.number="f.ppn_percent"
                                    type="number"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Other Charges</label>
                                <input
                                    v-model.number="f.other_charges"
                                    type="number"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                            </div>
                        </div>

                         <!-- Middle Info -->
                        <div class="space-y-4 md:col-span-1">
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Prepared By</label>
                                <input v-model="f.prepared_by" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Approved By</label>
                                <input v-model="f.approved_by" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Terbilang</label>
                                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600 italic dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                    {{ terbilangPlaceholder }}
                                </div>
                            </div>
                        </div>

                        <!-- Right Total -->
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 h-fit dark:bg-gray-700/50 dark:border-gray-600">
                             <div class="flex justify-between mb-2 text-sm text-gray-600 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span>{{ fmt(subtotal) }}</span>
                            </div>
                             <div class="flex justify-between mb-2 text-sm text-gray-600 dark:text-gray-400" v-if="f.discount > 0">
                                <span>Discount</span>
                                <span class="text-red-500">- {{ fmt(f.discount) }}</span>
                            </div>
                             <div class="flex justify-between mb-2 text-sm text-gray-600 dark:text-gray-400" v-if="f.ppn_percent > 0">
                                <span>PPN ({{ f.ppn_percent }}%)</span>
                                <span>+ {{ fmt((subtotal - f.discount) * f.ppn_percent / 100) }}</span>
                            </div>
                            <div class="flex justify-between mb-2 text-sm text-gray-600 dark:text-gray-400" v-if="f.other_charges > 0">
                                <span>Other Charges</span>
                                <span>+ {{ fmt(f.other_charges) }}</span>
                            </div>
                             <div class="border-t border-gray-300 my-3 pt-3 flex justify-between items-center dark:border-gray-500">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ fmt(total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                    <textarea v-model="f.notes" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" rows="3"></textarea>
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-8 justify-end">
                     <button
                        type="button"
                        @click="$router.push('/app/invoices/invoices')"
                        class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 flex items-center justify-center gap-2"
                    >
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        Save Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { ref, computed, onMounted, watch, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from '../../../api/dashboardAxios';
import InternalItemSelector from './InternalItemSelector.vue';
const route = useRoute();
const router = useRouter();
const id = route.params.id;
const nextInvoiceNumber = ref(null);
const masterItems = ref([]); // Master items list for dropdown
const showItemSelector = ref(false);
const errors = ref({});
const itemSearch = reactive({}); // Track search for each item row - using reactive for proper reactivity

const f = ref({
    invoice_number: '',
    invoice_date: new Date().toISOString().split("T")[0],
    currency_name: 'Indonesian Rupiah',
    notes: '',
    terbilang: '',
    prepared_by: '',
    approved_by: '',
    customer_name: "",
    customer_address: "",
    payment_terms: "",
    expedition: "",
    po_number: "",
    discount: 0,
    ppn_percent: 0,
    other_charges: 0,
    items: [
        {
            item_id: null,
            item_code: "",
            item_name: "",
            area: "",
            quantity: 1,
            unit_price: 0,
            discount: 0,
        },
    ],
});
const total = computed(() => {
    // Subtotal: sum of all items (qty × price - discount)
    const subtotal = f.value.items.reduce(
        (s, it) => s + ((it.quantity || 0) * (it.unit_price || 0) - (it.discount || 0)),
        0,
    );
    
    // After invoice-level discount
    const afterDiscount = subtotal - (f.value.discount || 0);
    
    // PPN calculation (on afterDiscount amount)
    const ppn = (afterDiscount * (f.value.ppn_percent || 0)) / 100;
    
    // Final total with other charges
    return afterDiscount + ppn + (f.value.other_charges || 0);
});

const subtotal = computed(() => {
    return f.value.items.reduce(
        (s, it) => s + ((it.quantity || 0) * (it.unit_price || 0) - (it.discount || 0)),
        0,
    );
});
const fmt = (n) => new Intl.NumberFormat("id").format(n);

const terbilangPlaceholder = computed(() => terbilangIndo(Math.round(total.value)));

function terbilangIndo(n) {
    if (!n && n !== 0) return '';
    
    n = Math.round(n);
    if (n < 0) n = Math.abs(n);
    if (n === 0) return 'nol rupiah';
    
    const ones = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
    const tens = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];
    const teens = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
    const scale = ['', 'ribu', 'juta', 'milyar'];
    
    function convertBelowThousand(num) {
        if (num === 0) return '';
        
        let result = '';
        const h = Math.floor(num / 100);
        const t = Math.floor((num % 100) / 10);
        const o = num % 10;
        
        if (h > 0) {
            if (h === 1) result += 'seratus';
            else result += ones[h] + ' ratus';
        }
        
        if (t === 0 && o > 0) {
            if (result) result += ' ';
            result += ones[o];
        } else if (t === 1) {
            if (result) result += ' ';
            result += teens[o];
        } else if (t > 1) {
            if (result) result += ' ';
            result += tens[t];
            if (o > 0) result += ' ' + ones[o];
        }
        
        return result;
    }
    
    let parts = [];
    let scaleIndex = 0;
    
    while (n > 0 && scaleIndex < scale.length) {
        const chunk = n % 1000;
        
        if (chunk > 0) {
            let chunkText = convertBelowThousand(chunk);
            
            if (scaleIndex === 1 && chunk === 1) {
                parts.unshift('seribu');
            } else if (scaleIndex === 2 && chunk === 1) {
                parts.unshift('satu juta');
            } else if (scaleIndex === 3 && chunk === 1) {
                parts.unshift('satu milyar');
            } else if (scale[scaleIndex]) {
                parts.unshift(chunkText + ' ' + scale[scaleIndex]);
            } else {
                parts.unshift(chunkText);
            }
        }
        
        n = Math.floor(n / 1000);
        scaleIndex++;
    }
    
    return parts.join(' ') + ' rupiah';
}
const save = async () => {
    // Reset errors
    errors.value = {};
    
    // Validation
    if (!f.value.invoice_date) {
        errors.value.invoice_date = 'Invoice Date is required';
    }
    if (!f.value.customer_name || f.value.customer_name.trim() === '') {
        errors.value.customer_name = 'Customer name is required';
    }
    
    // Validate items
    if (!f.value.items || f.value.items.length === 0) {
        errors.value.items = 'At least one item is required';
    } else {
        const invalidItems = f.value.items.filter(it => !it.item_id || !it.quantity || !it.unit_price);
        if (invalidItems.length > 0) {
            errors.value.items = `Please fill in: Item Selection, Quantity, and Price for all items`;
        }
    }
    
    // If there are errors, scroll to top and stop
    if (Object.keys(errors.value).length > 0) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        alert('❌ Please fill in all required fields (marked with *)');
        return;
    }
    
    const sub = subtotal.value;
    const afterDisc = sub - (f.value.discount || 0);
    const ppnAmt = (afterDisc * (f.value.ppn_percent || 0)) / 100;
    const tot = afterDisc + ppnAmt + (f.value.other_charges || 0);
    
    const data = {
        ...f.value,
        // Ensure date is in YYYY-MM-DD format
        invoice_date: formatDateForInput(f.value.invoice_date),
        subtotal: sub,
        discount: f.value.discount || 0,
        ppn_percent: f.value.ppn_percent || 0,
        ppn_amount: ppnAmt,
        other_charges: f.value.other_charges || 0,
        total: tot,
        terbilang: terbilangIndo(Math.round(tot)),
    };
    
    id
        ? await axios.put(`invoices/${id}`, data)
        : await axios.post("invoices", { ...data, invoice_number: f.value.invoice_number || null });
    router.push("/app/invoices/invoices");
};

const selectItem = (index, event) => {
    const itemId = event.target.value;
    if (!itemId) return;

    const selectedMasterItem = masterItems.value.find(item => item.id == itemId);
    if (selectedMasterItem) {
        f.value.items[index].item_id = selectedMasterItem.id;
        f.value.items[index].item_code = selectedMasterItem.code || '';
        f.value.items[index].item_name = selectedMasterItem.name;
        f.value.items[index].area = selectedMasterItem.category?.name || '';
        f.value.items[index].unit_price = selectedMasterItem.price || 0;
        // Keep quantity as is (user should set this)
        if (f.value.items[index].quantity === 1) {
            f.value.items[index].quantity = selectedMasterItem.quantity || 1;
        }
    }
};

const getFilteredItems = (index) => {
    const search = (itemSearch[index] || '').toLowerCase();
    if (!search) return [];
    
    return masterItems.value.filter(item => 
        item.name.toLowerCase().includes(search) ||
        (item.code && item.code.toLowerCase().includes(search))
    ).slice(0, 10); // Limit to 10 results
};

const selectItemFromSearch = (index, item) => {
    f.value.items[index].item_id = item.id;
    f.value.items[index].item_code = item.code || '';
    f.value.items[index].item_name = item.name;
    f.value.items[index].area = item.category?.name || '';
    f.value.items[index].unit_price = item.price || 0;
    
    // Keep quantity as is (user should set this)
    if (f.value.items[index].quantity === 1) {
        f.value.items[index].quantity = item.quantity || 1;
    }
    
    // Clear search after selection
    itemSearch[index] = '';
};

const addNewItem = () => {
    const newIndex = f.value.items.length;
    f.value.items.push({
        item_id: null,
        item_code: '',
        item_name: '',
        area: '',
        quantity: 1,
        unit_price: 0,
        discount: 0,
    });
    itemSearch[newIndex] = ''; // Initialize search for new item
};

const handleItemsSelected = (selectedItems) => {
    // Check if we have an empty first item that hasn't been used, if so replace it
    if (f.value.items.length === 1 && !f.value.items[0].item_id && !f.value.items[0].item_name) {
        f.value.items = [];
    }
    
    // Append new items
    selectedItems.forEach(item => {
        f.value.items.push(item);
    });
    
    showItemSelector.value = false;
};

const deleteItem = (index) => {
    f.value.items.splice(index, 1);
    // Clean up itemSearch when item is deleted
    const newSearch = {};
    Object.keys(itemSearch).forEach((key) => {
        const idx = parseInt(key);
        if (idx < index) {
            newSearch[idx] = itemSearch[idx];
        } else if (idx > index) {
            newSearch[idx - 1] = itemSearch[idx];
        }
    });
    // Clear old keys and set new ones
    Object.keys(itemSearch).forEach(key => delete itemSearch[key]);
    Object.assign(itemSearch, newSearch);
};

const formatDateForInput = (dateStr) => {
    if (!dateStr) return '';
    // Handle ISO format: 2026-02-06T00:00:00.000000Z → 2026-02-06
    if (dateStr.includes('T')) {
        return dateStr.split('T')[0];
    }
    // Already in YYYY-MM-DD format
    return dateStr;
};

onMounted(async () => {
    // Load master items
    try {
        const response = await axios.get('items?per_page=500');
        // Handle both paginated and array responses
        masterItems.value = Array.isArray(response.data) ? response.data : response.data.data || response.data;
    } catch (e) {
        console.warn('Could not load master items:', e);
    }

    if (id) {
        const data = await axios.get(`invoices/${id}`);
        // Ensure invoice_date is in YYYY-MM-DD format
        if (data.invoice_date) {
            data.invoice_date = formatDateForInput(data.invoice_date);
        }
        f.value = data;
    } else {
        // Generate next invoice number for display
        try {
            const response = await axios.get('invoices/next-number');
            nextInvoiceNumber.value = response.next_number;
            f.value.invoice_number = response.next_number;
        } catch (e) {
            console.warn('Could not fetch next invoice number:', e);
            // Backend will generate on save if not provided
        }
    }
});
</script>
