<template>
    <div class="p-6">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Invoices</h1>
            <button
                @click="$router.push('/app/invoices/invoices/create')"
                class="bg-blue-600 text-white px-4 py-2 rounded"
            >
                + New Invoice
            </button>
        </div>
        <div class="flex gap-4 mb-4">
            <input
                v-model="search"
                @input="load"
                placeholder="Search..."
                class="border px-3 py-2 rounded flex-1"
            />
            <select
                v-model="status"
                @change="load"
                class="border px-3 py-2 rounded"
            >
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
            </select>
        </div>
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Invoice#</th>
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Customer</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="inv in invoices"
                        :key="inv.id"
                        class="border-t hover:bg-gray-50"
                    >
                        <td class="p-3">{{ inv.invoice_number }}</td>
                        <td class="p-3">
                            {{
                                new Date(inv.invoice_date).toLocaleDateString(
                                    "id",
                                )
                            }}
                        </td>
                        <td class="p-3">{{ inv.customer_name }}</td>
                        <td class="p-3 text-right">{{ fmt(inv.total) }}</td>
                        <td class="p-3 text-center">
                            <span
                                :class="
                                    'px-2 py-1 rounded text-xs ' +
                                    badge(inv.status)
                                "
                                >{{ inv.status }}</span
                            >
                        </td>
                        <td class="p-3">
                            <div class="flex gap-2 justify-center">
                                <button
                                    @click="$router.push(`/invoices/${inv.id}`)"
                                    class="text-blue-600"
                                >
                                    👁
                                </button>
                                <button
                                    @click="
                                        $router.push(`//app/invoices/${inv.id}/edit`)
                                    "
                                    class="text-green-600"
                                >
                                    ✏️
                                </button>
                                <a
                                    :href="`/api/invoices/${inv.id}/pdf`"
                                    target="_blank"
                                    class="text-red-600"
                                    >📄</a
                                >
                                <button
                                    @click="del(inv.id)"
                                    class="text-red-600"
                                >
                                    🗑
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="pages > 1" class="flex justify-center gap-4 mt-4">
            <button
                @click="load(page - 1)"
                :disabled="page === 1"
                class="px-4 py-2 border rounded"
            >
                Prev
            </button>
            <span>Page {{ page }} of {{ pages }}</span>
            <button
                @click="load(page + 1)"
                :disabled="page === pages"
                class="px-4 py-2 border rounded"
            >
                Next
            </button>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import axios from '../../../api/dashboardAxios';
const invoices = ref([]);
const search = ref("");
const status = ref("");
const page = ref(1);
const pages = ref(1);
const load = async (p = page.value) => {
    const data = await axios.get("invoices", {
        params: { page: p, search: search.value, status: status.value },
    });
    invoices.value = data.data;
    page.value = data.current_page;
    pages.value = data.last_page;
};
const del = async (id) => {
    if (!confirm("Delete?")) return;
    await axios.delete(`invoices/${id}`);
    load();
};
const fmt = (n) => new Intl.NumberFormat("id").format(n);
const badge = (s) =>
    ({ draft: "bg-gray-200", pending: "bg-yellow-200", paid: "bg-green-200" })[
        s
    ] || "";
onMounted(load);
</script>
