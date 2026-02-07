<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6">
            {{ id ? "Edit" : "New" }} Invoice
        </h1>
        
        <!-- Required Fields Legend -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded flex items-start gap-2">
            <div class="text-sm">
                <span class="font-semibold">Tanda: <span class="text-red-600 font-bold text-lg">*</span> = Field wajib diisi</span>
                <p class="text-xs text-gray-600 mt-1">Pastikan semua field yang ditandai sudah terisi sebelum menyimpan.</p>
            </div>
        </div>
        
        <form
            @submit.prevent="save"
            class="bg-white p-6 rounded shadow space-y-6"
        >
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block mb-1">Invoice #</label>
                    <input
                        v-model="f.invoice_number"
                        :readonly="!id"
                        :disabled="!id"
                        :placeholder="!id ? 'Auto-generated' : ''"
                        :class="!id ? 'bg-gray-100 cursor-not-allowed' : ''"
                        class="border px-3 py-2 rounded w-full"
                    />
                    <div v-if="!id && nextInvoiceNumber" class="text-xs text-gray-500 mt-1">
                        Next: {{ nextInvoiceNumber }}
                    </div>
                </div>
                <div>
                    <label class="block mb-1">Invoice Date <span class="text-red-600 font-bold">*</span></label>
                    <input
                        v-model="f.invoice_date"
                        type="date"
                        required
                        :class="[
                            'border px-3 py-2 rounded w-full',
                            errors.invoice_date ? 'border-red-600 bg-red-50' : 'border-gray-300'
                        ]"
                    />
                    <div v-if="errors.invoice_date" class="text-red-600 text-xs mt-1">{{ errors.invoice_date }}</div>
                </div>
                <div>
                    <label class="block mb-1">Currency</label>
                    <input
                        v-model="f.currency_name"
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
                <div class="col-span-2">
                    <label class="block mb-1">Customer <span class="text-red-600 font-bold">*</span></label>
                    <input
                        v-model="f.customer_name"
                        required
                        :class="[
                            'border px-3 py-2 rounded w-full',
                            errors.customer_name ? 'border-red-600 bg-red-50' : 'border-gray-300'
                        ]"
                    />
                    <div v-if="errors.customer_name" class="text-red-600 text-xs mt-1">{{ errors.customer_name }}</div>
                </div>
                <div>
                    <label class="block mb-1">PO Number</label>
                    <input
                        v-model="f.po_number"
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
                <div>
                    <label class="block mb-1">Address</label>
                    <input
                        v-model="f.customer_address"
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
                <div>
                    <label class="block mb-1">Payment Terms</label>
                    <input
                        v-model="f.payment_terms"
                        placeholder="eg: COD"
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
                <div>
                    <label class="block mb-1">Expedition</label>
                    <input
                        v-model="f.expedition"
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
            </div>
            <!-- Items Section -->
            <div>
                <div class="flex justify-between items-end mb-2">
                    <h3 class="font-bold">Items <span class="text-red-600 font-bold">*</span> (At least 1 item required)</h3>
                    <button 
                        type="button" 
                        @click="showItemSelector = true"
                        class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 flex items-center gap-1"
                    >
                        <span>📚</span> Ambil dari Katalog (Batch)
                    </button>
                </div>
                <div v-if="errors.items" class="mb-2 p-3 bg-red-50 border border-red-600 rounded text-red-700 text-sm">
                    ⚠️ {{ errors.items }}
                </div>
                <!-- Group Items by Area in Display? Or just flat list with Area column? 
                     For now, flat list with Area column is easier to implement in Form. 
                     PDF will handle grouping. -->
                <div
                    v-for="(it, i) in f.items"
                    :key="i"
                    class="grid grid-cols-8 gap-2 mb-2 items-end"
                >
                    <div class="col-span-2 relative">
                        <label class="block text-xs">Select Item <span class="text-red-600 font-bold">*</span></label>
                        <input
                            v-model="itemSearch[i]"
                            type="text"
                            placeholder="Search item..."
                            @focus="itemSearch[i] = itemSearch[i] || ''"
                            class="border px-2 py-1 rounded w-full text-sm"
                            :class="!it.item_id && Object.keys(errors).length > 0 ? 'border-red-600 bg-red-50' : 'border-gray-300'"
                        />
                        <!-- Search Dropdown Logic (Existing) -->
                        <div v-if="itemSearch[i] && getFilteredItems(i).length > 0"
                            class="absolute top-full left-0 right-0 mt-1 border border-gray-300 bg-white rounded z-10 max-h-48 overflow-y-auto shadow-lg">
                            <div v-for="item in getFilteredItems(i)"
                                :key="item.id"
                                @click="selectItemFromSearch(i, item)"
                                class="px-2 py-2 hover:bg-blue-100 cursor-pointer text-sm border-b border-gray-200 last:border-b-0">
                                <div class="font-semibold text-gray-800">{{ item.name }}</div>
                                <div class="text-xs text-gray-500">{{ item.code }} • {{ item.unit }} • Rp {{ fmt(item.price) }}</div>
                            </div>

                        </div>
                    </div>
                    <div>
                        <label class="block text-xs">Area</label>
                        <input
                            v-model="it.area"
                            class="border border-gray-300 px-2 py-1 rounded w-full text-sm bg-gray-50"
                            placeholder="Area"
                        />
                    </div>
                    <div>
                        <label class="block text-xs">Code</label>
                        <input
                            v-model="it.item_code"
                            class="border border-gray-300 px-2 py-1 rounded w-full text-sm"
                        />
                    </div>
                    <!-- Reuse existing fields -->
                    <div>
                        <label class="block text-xs">Qty <span class="text-red-600 font-bold">*</span></label>
                        <input
                            v-model.number="it.quantity"
                            type="number"
                            required
                            class="border px-2 py-1 rounded w-full text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs">Price <span class="text-red-600 font-bold">*</span></label>
                        <input
                            v-model.number="it.unit_price"
                            type="number"
                            required
                            class="border px-2 py-1 rounded w-full text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs">Disc</label>
                        <input
                            v-model.number="it.discount"
                            type="number"
                            class="border border-gray-300 px-2 py-1 rounded w-full text-sm"
                        />
                    </div>
                    <div class="flex gap-1">
                        <span class="border border-gray-300 px-2 py-1 rounded bg-gray-100 text-sm flex-1">
                            {{ fmt((it.quantity || 0) * (it.unit_price || 0) - (it.discount || 0)) }}
                        </span>
                        <button type="button" @click="deleteItem(i)" class="text-red-600 hover:text-red-800">🗑</button>
                    </div>
                </div>
                <button
                    type="button"
                    @click="addNewItem"
                    class="text-blue-600 text-sm hover:text-blue-800"
                >
                    + Add Single Item
                </button>
            </div>
            
            <InternalItemSelector 
                :show="showItemSelector" 
                :items="masterItems"
                @close="showItemSelector = false"
                @confirm="handleItemsSelected"
            />

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block mb-1">Discount</label
                    ><input
                        v-model.number="f.discount"
                        type="number"
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
                <div>
                    <label class="block mb-1">PPN %</label
                    ><input
                        v-model.number="f.ppn_percent"
                        type="number"
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
                <div>
                    <label class="block mb-1">Other Charges</label
                    ><input
                        v-model.number="f.other_charges"
                        type="number"
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
            </div>
            <div>
                <label class="block mb-1">Terbilang (amount in words) - Auto-calculated from total</label>
                <div class="border px-3 py-2 rounded w-full bg-blue-50 text-sm">
                    {{ terbilangPlaceholder }}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">Prepared By</label>
                    <input v-model="f.prepared_by" class="border px-3 py-2 rounded w-full" />
                </div>
                <div>
                    <label class="block mb-1">Approved By</label>
                    <input v-model="f.approved_by" class="border px-3 py-2 rounded w-full" />
                </div>
            </div>
            <div>
                <label class="block mb-1">Notes / Keterangan</label>
                <textarea v-model="f.notes" class="border px-3 py-2 rounded w-full" rows="4"></textarea>
            </div>
            <div class="text-right text-xl font-bold">
                Total: {{ fmt(total) }}
            </div>
            <div class="flex gap-4 justify-end">
                <button
                    type="button"
                    @click="$router.push('/app/invoices/invoices')"
                    class="px-4 py-2 border rounded"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded"
                >
                    Save
                </button>
            </div>
        </form>
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
