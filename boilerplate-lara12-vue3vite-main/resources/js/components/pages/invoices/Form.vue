<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6">
            {{ id ? "Edit" : "New" }} Invoice
        </h1>
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
                    <label class="block mb-1">Invoice Date*</label>
                    <input
                        v-model="f.invoice_date"
                        type="date"
                        required
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
                <div>
                    <label class="block mb-1">Currency</label>
                    <input
                        v-model="f.currency_name"
                        class="border px-3 py-2 rounded w-full"
                    />
                </div>
                <div class="col-span-2">
                    <label class="block mb-1">Customer*</label>
                    <input
                        v-model="f.customer_name"
                        required
                        class="border px-3 py-2 rounded w-full"
                    />
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
            <div>
                <h3 class="font-bold mb-2">Items</h3>
                <div
                    v-for="(it, i) in f.items"
                    :key="i"
                    class="grid grid-cols-7 gap-2 mb-2 items-end"
                >
                    <div>
                        <label class="block text-xs">Select Item*</label>
                        <select
                            @change="selectItem(i, $event)"
                            class="border px-2 py-1 rounded w-full text-sm"
                        >
                            <option value="">-- Choose Item --</option>
                            <option
                                v-for="item in masterItems"
                                :key="item.id"
                                :value="item.id"
                                :selected="it.item_id === item.id"
                            >
                                {{ item.name }} ({{ item.unit }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs">Code</label
                        ><input
                            v-model="it.item_code"
                            class="border px-2 py-1 rounded w-full text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs">Name</label
                        ><input
                            v-model="it.item_name"
                            class="border px-2 py-1 rounded w-full text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs">Qty*</label
                        ><input
                            v-model.number="it.quantity"
                            type="number"
                            required
                            class="border px-2 py-1 rounded w-full text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs">Price*</label
                        ><input
                            v-model.number="it.unit_price"
                            type="number"
                            required
                            class="border px-2 py-1 rounded w-full text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-xs">Disc</label
                        ><input
                            v-model.number="it.discount"
                            type="number"
                            class="border px-2 py-1 rounded w-full text-sm"
                        />
                    </div>
                    <div class="flex gap-1">
                        <span
                            class="border px-2 py-1 rounded bg-gray-100 text-sm flex-1"
                            >{{
                                fmt(
                                    (it.quantity || 0) * (it.unit_price || 0) -
                                        (it.discount || 0),
                                )
                            }}</span
                        >
                        <button
                            type="button"
                            @click="f.items.splice(i, 1)"
                            class="text-red-600"
                        >
                            🗑
                        </button>
                    </div>
                </div>
                <button
                    type="button"
                    @click="
                        f.items.push({
                            item_id: null,
                            item_code: '',
                            item_name: '',
                            quantity: 1,
                            unit_price: 0,
                            discount: 0,
                        })
                    "
                    class="text-blue-600 text-sm"
                >
                    + Add Item
                </button>
            </div>
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
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from '../../../api/dashboardAxios';
const route = useRoute();
const router = useRouter();
const id = route.params.id;
const nextInvoiceNumber = ref(null);
const masterItems = ref([]); // Master items list for dropdown

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
    
    // Round to nearest rupiah (no cents in Indonesian currency)
    n = Math.round(n);
    
    const ones = ["","satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan"];
    const teens = ["sepuluh","sebelas","dua belas","tiga belas","empat belas","lima belas","enam belas","tujuh belas","delapan belas","sembilan belas"];
    const tens = ["","","dua puluh","tiga puluh","empat puluh","lima puluh","enam puluh","tujuh puluh","delapan puluh","sembilan puluh"];
    
    function convertBelowThousand(num) {
        let result = "";
        
        const hundreds = Math.floor(num / 100);
        if (hundreds > 0) {
            if (hundreds === 1) {
                result = "seratus";
            } else {
                result = ones[hundreds] + " ratus";
            }
        }
        
        const remainder = num % 100;
        if (remainder > 0) {
            if (result) result += " ";
            
            if (remainder < 10) {
                result += ones[remainder];
            } else if (remainder < 20) {
                result += teens[remainder - 10];
            } else {
                const t = Math.floor(remainder / 10);
                const o = remainder % 10;
                result += tens[t];
                if (o > 0) {
                    result += " " + ones[o];
                }
            }
        }
        
        return result;
    }
    
    if (n === 0) return "nol rupiah";
    
    let result = "";
    
    // Milyar (billions)
    const milyar = Math.floor(n / 1000000000);
    if (milyar > 0) {
        result = convertBelowThousand(milyar) + " milyar";
    }
    
    // Juta (millions)
    const juta = Math.floor((n % 1000000000) / 1000000);
    if (juta > 0) {
        if (result) result += " ";
        result += convertBelowThousand(juta) + " juta";
    }
    
    // Ribu (thousands)
    const ribu = Math.floor((n % 1000000) / 1000);
    if (ribu > 0) {
        if (result) result += " ";
        if (ribu === 1) {
            result += "seribu";
        } else {
            result += convertBelowThousand(ribu) + " ribu";
        }
    }
    
    // Satuan (ones)
    const satuan = n % 1000;
    if (satuan > 0) {
        if (result) result += " ";
        result += convertBelowThousand(satuan);
    }
    
    return result.trim() + " rupiah";
}
const save = async () => {
    const sub = subtotal.value;
    const afterDisc = sub - (f.value.discount || 0);
    const ppnAmt = (afterDisc * (f.value.ppn_percent || 0)) / 100;
    const tot = afterDisc + ppnAmt + (f.value.other_charges || 0);
    
    const data = {
        ...f.value,
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
        f.value.items[index].unit_price = selectedMasterItem.price || 0;
        // Keep quantity as is (user should set this)
        if (f.value.items[index].quantity === 1) {
            f.value.items[index].quantity = selectedMasterItem.quantity || 1;
        }
    }
};

onMounted(async () => {
    // Load master items
    try {
        const response = await axios.get('items?per_page=100');
        masterItems.value = response.data;
    } catch (e) {
        console.warn('Could not load master items:', e);
    }

    if (id) {
        const data = await axios.get(`invoices/${id}`);
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
