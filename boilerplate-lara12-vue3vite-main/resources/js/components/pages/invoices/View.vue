<template><div class="p-6" v-if="inv"><div class="flex justify-between mb-6">
<h1 class="text-2xl font-bold">Invoice {{inv.invoice_number}}</h1>
<div class="flex gap-2"><a :href="`/api/invoices/${inv.id}/pdf`" target="_blank" class="bg-red-600 text-white px-4 py-2 rounded">📄 PDF</a>
<button @click="$router.push(`/invoices/${inv.id}/edit`)" class="bg-blue-600 text-white px-4 py-2 rounded">Edit</button>
<button @click="$router.push('/invoices')" class="border px-4 py-2 rounded">Back</button></div></div>
<div class="bg-white p-8 rounded shadow"><div class="text-center mb-6"><h1 class="text-3xl font-bold">Faktur Penjualan</h1></div>
<div class="mb-6"><p><strong>Kepada:</strong></p><p class="font-bold">{{inv.customer_name}}</p><p>{{inv.customer_address}}</p></div>
<div class="border-2 border-black p-4 mb-6"><div class="grid grid-cols-2 gap-4">
<div><strong>Tanggal:</strong> {{new Date(inv.invoice_date).toLocaleDateString('id')}}</div>
<div><strong>Nomor:</strong> {{inv.invoice_number}}</div>
<div><strong>Payment:</strong> {{inv.payment_terms}}</div><div><strong>PO:</strong> {{inv.po_number}}</div></div></div>
<table class="w-full border-collapse mb-6"><thead><tr class="bg-gray-100">
<th class="border border-black p-2">Code</th><th class="border border-black p-2">Item</th><th class="border border-black p-2">Qty</th>
<th class="border border-black p-2">Price</th><th class="border border-black p-2">Disc</th><th class="border border-black p-2">Total</th></tr></thead>
<tbody><tr v-for="it in inv.items" :key="it.id"><td class="border border-black p-2">{{it.item_code}}</td>
<td class="border border-black p-2">{{it.item_name}}</td><td class="border border-black p-2 text-center">{{it.quantity}}</td>
<td class="border border-black p-2 text-right">{{fmt(it.unit_price)}}</td><td class="border border-black p-2 text-right">{{fmt(it.discount)}}</td>
<td class="border border-black p-2 text-right">{{fmt(it.total)}}</td></tr></tbody></table>
<div class="ml-auto w-1/2"><table class="w-full"><tr><td>Sub Total</td><td class="text-right">{{fmt(inv.subtotal)}}</td></tr>
<tr v-if="inv.discount"><td>Discount</td><td class="text-right">{{fmt(inv.discount)}}</td></tr>
<tr v-if="inv.ppn_amount"><td>PPN ({{inv.ppn_percent}}%)</td><td class="text-right">{{fmt(inv.ppn_amount)}}</td></tr>
<tr v-if="inv.other_charges"><td>Other</td><td class="text-right">{{fmt(inv.other_charges)}}</td></tr>
<tr class="font-bold border-t-2 border-black"><td>TOTAL</td><td class="text-right">{{fmt(inv.total)}}</td></tr></table></div></div></div></template>
<script setup>
import {ref,onMounted} from 'vue';import {useRoute} from 'vue-router';import axios from 'axios';const route=useRoute();
const inv=ref(null);const fmt=n=>new Intl.NumberFormat('id').format(n);
onMounted(async()=>{const {data}=await axios.get(`/api/invoices/${route.params.id}`);inv.value=data});</script>
