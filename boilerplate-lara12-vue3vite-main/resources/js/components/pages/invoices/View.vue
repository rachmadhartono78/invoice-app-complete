<template>
	<div class="p-6" v-if="inv">
		<div class="flex justify-between items-start mb-6">
			<div>
				<h2 class="text-xl font-bold">Your Company</h2>
				<div class="text-sm">Address line 1</div>
				<div class="text-sm">Address line 2</div>
			</div>
			<div class="border p-4 text-sm">
				<div><strong>Faktur Penjualan</strong></div>
				<div class="grid grid-cols-2 gap-2 mt-2 text-xs">
					<div><strong>Tanggal</strong><div>{{ new Date(inv.invoice_date).toLocaleDateString('id') }}</div></div>
					<div><strong>Nomor</strong><div>{{ inv.invoice_number }}</div></div>
					<div><strong>Syarat Pembayaran</strong><div>{{ inv.payment_terms }}</div></div>
					<div><strong>PO No</strong><div>{{ inv.po_number }}</div></div>
					<div><strong>Ekspedisi</strong><div>{{ inv.expedition }}</div></div>
					<div><strong>Mata Uang</strong><div>{{ inv.currency_name || 'Indonesian Rupiah' }}</div></div>
				</div>
			</div>
		</div>

		<div class="bg-white p-6 rounded shadow mb-6">
			<div class="mb-4">
				<div class="text-sm"><strong>Kepada:</strong></div>
				<div class="font-bold">{{ inv.customer_name }}</div>
				<div class="text-sm">{{ inv.customer_address }}</div>
			</div>

			<table class="w-full border-collapse mb-4">
				<thead>
					<tr>
						<th class="border p-2 text-left">Kode Barang</th>
						<th class="border p-2 text-left">Nama Barang</th>
						<th class="border p-2 text-center">Kts.</th>
						<th class="border p-2 text-right">@Harga</th>
						<th class="border p-2 text-right">Diskon</th>
						<th class="border p-2 text-right">Total Harga</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(it, i) in inv.items" :key="i">
						<td class="border p-2">{{ it.item_code }}</td>
						<td class="border p-2">{{ it.item_name }}</td>
						<td class="border p-2 text-center">{{ it.quantity }}</td>
						<td class="border p-2 text-right">{{ fmt(it.unit_price) }}</td>
						<td class="border p-2 text-right">{{ fmt(it.discount) }}</td>
						<td class="border p-2 text-right">{{ fmt((it.quantity||0)*(it.unit_price||0) - (it.discount||0)) }}</td>
					</tr>
				</tbody>
			</table>

			<div class="mb-4">
				<strong>Terbilang :</strong>
				<div class="border p-2 mt-1">{{ inv.terbilang || terbilangIndo(Math.round(inv.total||0)) }}</div>
			</div>

			<div class="flex gap-6 justify-end">
				<div class="w-1/3">
					<table class="w-full">
						<tr><td>Sub Total</td><td class="text-right">{{ fmt(inv.subtotal) }}</td></tr>
						<tr v-if="inv.discount"><td>Diskon</td><td class="text-right">{{ fmt(inv.discount) }}</td></tr>
						<tr v-if="inv.ppn_amount"><td>PPN ({{ inv.ppn_percent }}%)</td><td class="text-right">{{ fmt(inv.ppn_amount) }}</td></tr>
						<tr v-if="inv.other_charges"><td>Biaya Lain-lain</td><td class="text-right">{{ fmt(inv.other_charges) }}</td></tr>
						<tr class="font-bold border-t-2"><td>Total</td><td class="text-right">{{ fmt(inv.total) }}</td></tr>
					</table>
				</div>
			</div>

			<div class="grid grid-cols-2 gap-8 mt-8">
				<div class="text-center">
					<div>Disiapkan Oleh</div>
					<div class="mt-12">{{ inv.prepared_by || '__________' }}</div>
				</div>
				<div class="text-center">
					<div>Disetujui Oleh</div>
					<div class="mt-12">{{ inv.approved_by || '__________' }}</div>
				</div>
			</div>
		</div>

		<div class="flex gap-2">
			<button @click="downloadPdf(inv.id)" :disabled="loadingPdf" :class="[loadingPdf ? 'bg-gray-400 cursor-wait opacity-75' : 'bg-red-600 hover:bg-red-700', 'text-white px-4 py-2 rounded']">{{ loadingPdf ? '⏳ Generating...' : '📄 PDF' }}</button>
			<button @click="$router.push(`/app/invoices/invoices/${inv.id}/edit`)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Edit</button>
			<button @click="$router.push('/app/invoices/invoices')" class="border px-4 py-2 rounded hover:bg-gray-50">Back</button>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../../../api/dashboardAxios';
const route = useRoute();
const inv = ref(null);
const loadingPdf = ref(false);
const fmt = n => new Intl.NumberFormat('id').format(n);

onMounted(async () => {
	const data = await axios.get(`invoices/${route.params.id}`);
	inv.value = data;
});

const downloadPdf = async (id) => {
	try {
		loadingPdf.value = true;
		const blob = await axios.get(`invoices/${id}/pdf`, { responseType: 'blob', headers: { Accept: 'application/pdf' } });
		const url = window.URL.createObjectURL(blob);
		const a = document.createElement('a');
		a.href = url;
		a.target = '_blank';
		a.download = `invoice-${id}.pdf`;
		document.body.appendChild(a);
		a.click();
		a.remove();
		window.URL.revokeObjectURL(url);
	} catch (e) {
		console.error('PDF download failed', e);
		alert('Failed to generate PDF.');
	} finally {
		loadingPdf.value = false;
	}
};

function terbilangIndo(n) {
	const angka = ["","satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan","sepuluh","sebelas"];
	function inWords(n){
		n = Math.floor(n);
		if (n < 12) return angka[n];
		if (n < 20) return inWords(n-10) + ' belas';
		if (n < 100) return inWords(Math.floor(n/10)) + ' puluh' + (n%10 ? ' ' + inWords(n%10) : '');
		if (n < 200) return 'seratus' + (n-100 ? ' ' + inWords(n-100) : '');
		if (n < 1000) return inWords(Math.floor(n/100)) + ' ratus' + (n%100 ? ' ' + inWords(n%100) : '');
		if (n < 2000) return 'seribu' + (n-1000 ? ' ' + inWords(n-1000) : '');
		if (n < 1000000) return inWords(Math.floor(n/1000)) + ' ribu' + (n%1000 ? ' ' + inWords(n%1000) : '');
		if (n < 1000000000) return inWords(Math.floor(n/1000000)) + ' juta' + (n%1000000 ? ' ' + inWords(n%1000000) : '');
		return inWords(Math.floor(n/1000000000)) + ' milyar' + (n%1000000000 ? ' ' + inWords(n%1000000000) : '');
	}
	if (!n && n !== 0) return '';
	if (n === 0) return 'nol';
	return inWords(n) + ' rupiah';
}
</script>
