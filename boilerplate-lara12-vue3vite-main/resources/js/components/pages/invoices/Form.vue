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
                <label class="block mb-1">Terbilang (amount in words)</label>
                <input v-model="f.terbilang" :placeholder="terbilangPlaceholder" class="border px-3 py-2 rounded w-full" />
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
                <textarea v-model="f.keterangan" class="border px-3 py-2 rounded w-full" rows="4"></textarea>
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
    terbilang: '',
    keterangan: '',
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
    const sub = f.value.items.reduce(
        (s, it) =>
            s + (it.quantity || 0) * (it.unit_price || 0) - (it.discount || 0),
        0,
    );
    const afterDis = sub - (f.value.discount || 0);
    const ppn = (afterDis * (f.value.ppn_percent || 0)) / 100;
    return afterDis + ppn + (f.value.other_charges || 0);
});
const fmt = (n) => new Intl.NumberFormat("id").format(n);

const terbilangPlaceholder = computed(() => terbilangIndo(Math.round(total.value)));

function terbilangIndo(n) {
    if (!n && n !== 0) return '';
    const angka = ["","satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan","sepuluh","sebelas"];
    function inWords(n) {
        n = Math.floor(n);
        if (n < 12) return angka[n];
        if (n < 20) return inWords(n - 10) + ' belas';
        if (n < 100) return inWords(Math.floor(n / 10)) + ' puluh' + (n % 10 ? ' ' + inWords(n % 10) : '');
        if (n < 200) return 'seratus' + (n - 100 ? ' ' + inWords(n - 100) : '');
        if (n < 1000) return inWords(Math.floor(n / 100)) + ' ratus' + (n % 100 ? ' ' + inWords(n % 100) : '');
        if (n < 2000) return 'seribu' + (n - 1000 ? ' ' + inWords(n - 1000) : '');
        if (n < 1000000) return inWords(Math.floor(n / 1000)) + ' ribu' + (n % 1000 ? ' ' + inWords(n % 1000) : '');
        if (n < 1000000000) return inWords(Math.floor(n / 1000000)) + ' juta' + (n % 1000000 ? ' ' + inWords(n % 1000000) : '');
        return inWords(Math.floor(n / 1000000000)) + ' milyar' + (n % 1000000000 ? ' ' + inWords(n % 1000000000) : '');
    }
    if (n === 0) return 'nol';
    return inWords(n) + ' rupiah';
}
const save = async () => {
    const data = {
        ...f.value,
        subtotal: f.value.items.reduce(
            (s, it) =>
                s +
                (it.quantity || 0) * (it.unit_price || 0) -
                (it.discount || 0),
            0,
        ),
        ppn_amount: total.value,
        total: total.value,
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
