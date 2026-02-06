<!DOCTYPE html><html><head><meta charset="utf-8"><title>Invoice</title>
<style>*{margin:0;padding:0}body{font-family:Arial;font-size:11px}.header{text-align:center;margin-bottom:20px}.header h1{font-size:18px}.info{margin-bottom:15px}.box{border:2px solid #000;padding:10px;margin-bottom:15px}.box table{width:100%}.box td{padding:3px}table.items{width:100%;border-collapse:collapse;margin-bottom:15px}table.items th,table.items td{border:1px solid #000;padding:6px;text-align:left}table.items th{background:#f0f0f0;text-align:center}.right{text-align:right}.center{text-align:center}.summary{width:50%;margin-left:auto}.summary table{width:100%}.summary td{padding:5px}.total{font-weight:bold;border-top:2px solid #000;padding-top:8px}.sig{margin-top:50px;display:flex;justify-content:space-between}.sig div{text-align:center;width:45%}.sig-line{border-bottom:1px solid #000;height:60px;margin:10px 0}
</style></head><body>
<div class="header"><h1>Indonesia</h1><h2>Faktur Penjualan</h2></div>
<div class="info"><strong>Kepada:</strong><br><strong>{{$invoice->customer_name}}</strong>
@if($invoice->customer_address)<br>{{$invoice->customer_address}}@endif</div>
<div class="box"><table><tr><td width="20%">Tanggal</td><td>:</td><td>{{$invoice->invoice_date->format('d M Y')}}</td>
<td width="20%">Nomor</td><td>:</td><td>{{$invoice->invoice_number}}</td></tr>
<tr><td>Syarat Pembayaran</td><td>:</td><td>{{$invoice->payment_terms??'-'}}</td>
<td>{{$invoice->expedition?'Ekspedisi':'PO No'}}</td><td>:</td><td>{{$invoice->expedition??$invoice->po_number??'-'}}</td></tr>
@if($invoice->delivery_date)<tr><td>Tanggal Pengiriman</td><td>:</td><td>{{$invoice->delivery_date->format('d M Y')}}</td>
<td>Mata Uang</td><td>:</td><td>{{$invoice->currency}}</td></tr>@endif</table></div>
<table class="items"><thead><tr><th width="15%">Kode</th><th>Nama Barang</th><th width="10%">Kts.</th>
<th width="15%">@Harga</th><th width="12%">Diskon</th><th width="18%">Total</th></tr></thead><tbody>
@foreach($invoice->items as $item)<tr><td>{{$item->item_code}}</td><td>{{$item->item_name}}</td>
<td class="center">{{number_format($item->quantity)}}</td><td class="right">{{number_format($item->unit_price,0,'.',',')}}</td>
<td class="right">{{number_format($item->discount,0,'.',',')}}</td><td class="right">{{number_format($item->total,0,'.',',')}}</td></tr>@endforeach
</tbody></table>
<div class="box"><strong>Terbilang:</strong> {{ucwords(terbilang($invoice->total))}} Rupiah</div>
@if($invoice->notes)<div style="margin-bottom:15px"><strong>Keterangan:</strong><p>{{$invoice->notes}}</p></div>@endif
<div class="summary"><table><tr><td>Sub Total</td><td class="right">{{number_format($invoice->subtotal,0,'.',',')}}</td></tr>
@if($invoice->discount>0)<tr><td>Diskon</td><td class="right">{{number_format($invoice->discount,0,'.',',')}}</td></tr>@endif
@if($invoice->ppn_amount>0)<tr><td>PPN ({{$invoice->ppn_percent}}%)</td><td class="right">{{number_format($invoice->ppn_amount,0,'.',',')}}</td></tr>@endif
@if($invoice->other_charges>0)<tr><td>Biaya Lain-lain</td><td class="right">{{number_format($invoice->other_charges,0,'.',',')}}</td></tr>@endif
<tr class="total"><td>Total</td><td class="right">{{number_format($invoice->total,0,'.',',')}}</td></tr></table></div>
<div class="sig"><div><p>Disiapkan Oleh</p><div class="sig-line"></div><p>{{$invoice->prepared_by??'__________'}}</p></div>
<div><p>Disetujui Oleh</p><div class="sig-line"></div><p>{{$invoice->approved_by??'__________'}}</p></div></div>
</body></html>
@php function terbilang($n){$h=["","Satu","Dua","Tiga","Empat","Lima","Enam","Tujuh","Delapan","Sembilan","Sepuluh","Sebelas"];$n=abs($n);if($n<12)return $h[$n];if($n<20)return terbilang($n-10)." Belas";if($n<100)return terbilang($n/10)." Puluh ".terbilang($n%10);if($n<200)return"Seratus ".terbilang($n-100);if($n<1000)return terbilang($n/100)." Ratus ".terbilang($n%100);if($n<2000)return"Seribu ".terbilang($n-1000);if($n<1000000)return terbilang($n/1000)." Ribu ".terbilang($n%1000);if($n<1000000000)return terbilang($n/1000000)." Juta ".terbilang($n%1000000);return"Angka terlalu besar";}@endphp
