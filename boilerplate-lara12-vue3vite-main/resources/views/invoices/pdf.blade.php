<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 210mm;
            margin: 0 auto;
        }
        /* Header Section */
        .header-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }
        .header-left {
            flex: 1;
        }
        .header-left strong {
            font-size: 10px;
            display: block;
            margin-bottom: 5px;
        }
        .header-left .customer-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header-left .underline {
            border-bottom: 1px solid #000;
            min-height: 20px;
        }
        .header-right {
            flex: 1;
            text-align: center;
        }
        .header-right h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header-right h2 {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        /* Info Box */
        .info-box {
            border: 2px solid #000;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .info-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-box td {
            padding: 8px 10px;
            border-right: 1px solid #000;
            border-bottom: 1px dashed #999;
            font-size: 11px;
        }
        .info-box td:last-child {
            border-right: none;
        }
        .info-box tr:last-child td {
            border-bottom: none;
        }
        .info-box .label {
            font-weight: bold;
            width: 25%;
        }
        .info-box .value {
            width: 25%;
        }
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 2px solid #000;
        }
        .items-table thead {
            background-color: #f5f5f5;
        }
        .items-table th {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 11px;
        }
        .items-table .kode {
            width: 12%;
            text-align: center;
        }
        .items-table .nama {
            width: 35%;
        }
        .items-table .qty {
            width: 8%;
            text-align: center;
        }
        .items-table .harga {
            width: 15%;
            text-align: right;
        }
        .items-table .diskon {
            width: 12%;
            text-align: right;
        }
        .items-table .total {
            width: 18%;
            text-align: right;
            font-weight: bold;
        }
        /* Sections */
        .section-box {
            border: 1px solid #000;
            padding: 12px;
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .terbilang-text {
            font-size: 12px;
            line-height: 1.6;
        }
        .notes-content {
            font-size: 11px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        /* Summary Section */
        .summary-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .summary-left {
            flex: 1;
        }
        .summary-right {
            flex: 0 0 220px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .summary-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ccc;
            font-size: 11px;
        }
        .summary-table tr:last-child td {
            border-bottom: none;
        }
        .summary-table .label {
            font-weight: normal;
            width: 50%;
        }
        .summary-table .value {
            text-align: right;
            font-weight: normal;
        }
        .summary-table .total-row td {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-weight: bold;
            background-color: #f9f9f9;
        }
        /* Signature Section */
        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            gap: 20px;
        }
        .sign-box {
            flex: 1;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
            min-height: 100px;
            display: flex;
            flex-direction: column;
        }
        .sign-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .sign-line {
            flex: 1;
            border-bottom: 1px solid #000;
            margin: 30px 0 5px;
        }
        .sign-name {
            font-size: 11px;
            margin-top: 5px;
        }
        /* Utility Classes */
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header-container">
            <div class="header-left">
                <strong>Kepada :</strong>
                <div class="underline"></div>
                <div class="customer-name">{{ $invoice->customer_name }}</div>
                @if($invoice->customer_address)
                    <div style="font-size: 10px; margin-top: 5px;">{{ $invoice->customer_address }}</div>
                @endif
            </div>
            <div class="header-right">
                <h1>Indonesia</h1>
                <h2>Faktur Penjualan</h2>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <table>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="value">{{ $invoice->invoice_date->format('d M Y') }}</td>
                    <td class="label">Nomor</td>
                    <td class="value">{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td class="label">Syarat Pembayaran</td>
                    <td class="value">{{ $invoice->payment_terms ?? '-' }}</td>
                    <td class="label">{{ $invoice->expedition ? 'Ekspedisi' : 'PO No' }}</td>
                    <td class="value">{{ $invoice->expedition ?? $invoice->po_number ?? '-' }}</td>
                </tr>
                @if($invoice->delivery_date)
                <tr>
                    <td class="label">Tanggal Pengiriman</td>
                    <td class="value">{{ $invoice->delivery_date->format('d M Y') }}</td>
                    <td class="label">Mata Uang</td>
                    <td class="value">{{ $invoice->currency_name ?? $invoice->currency ?? 'Indonesian Rupiah' }}</td>
                </tr>
                @elseif($invoice->currency_name)
                <tr>
                    <td class="label">Mata Uang</td>
                    <td class="value">{{ $invoice->currency_name }}</td>
                    <td class="label"></td>
                    <td class="value"></td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="kode">Kode Barang</th>
                    <th class="nama">Nama Barang</th>
                    <th class="qty">Kts.</th>
                    <th class="harga">@Harga</th>
                    <th class="diskon">Diskon</th>
                    <th class="total">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items->groupBy('area') as $area => $items)
                    @if($area)
                    <tr style="background-color: #f0f0f0;">
                        <td colspan="6" style="font-weight: bold; padding: 8px 10px; border: 1px solid #000; text-transform: uppercase;">{{ $area }}</td>
                    </tr>
                    @endif
                    @foreach($items as $item)
                    <tr>
                        <td class="kode">{{ $item->item_code }}</td>
                        <td class="nama">{{ $item->item_name }}</td>
                        <td class="qty">{{ number_format($item->quantity) }}</td>
                        <td class="harga">{{ number_format($item->unit_price, 0, '.', ',') }}</td>
                        <td class="diskon">{{ number_format($item->discount, 0, '.', ',') }}</td>
                        <td class="total">{{ number_format($item->total, 0, '.', ',') }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-container">
            <!-- Left Side: Terbilang & Notes -->
            <div class="summary-left">
                <div class="section-box">
                    <div class="section-title">Terbilang :</div>
                    <div class="terbilang-text">{{ ucwords(terbilang($invoice->total)) }} Rupiah</div>
                </div>

                @if($invoice->notes)
                <div class="section-box">
                    <div class="section-title">Keterangan :</div>
                    <div class="notes-content">{{ $invoice->notes }}</div>
                </div>
                @endif
            </div>

            <!-- Right Side: Summary Table -->
            <div class="summary-right">
                <table class="summary-table">
                    <tr>
                        <td class="label">Sub Total</td>
                        <td class="value">{{ number_format($invoice->subtotal, 0, '.', ',') }}</td>
                    </tr>
                    @if($invoice->discount > 0)
                    <tr>
                        <td class="label">Diskon</td>
                        <td class="value">{{ number_format($invoice->discount, 0, '.', ',') }}</td>
                    </tr>
                    @endif
                    @if($invoice->ppn_amount > 0)
                    <tr>
                        <td class="label">PPN ({{ $invoice->ppn_percent ?? 0 }}%)</td>
                        <td class="value">{{ number_format($invoice->ppn_amount, 0, '.', ',') }}</td>
                    </tr>
                    @endif
                    @if($invoice->other_charges > 0)
                    <tr>
                        <td class="label">Biaya Lain-lain</td>
                        <td class="value">{{ number_format($invoice->other_charges, 0, '.', ',') }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="label">Total</td>
                        <td class="value">{{ number_format($invoice->total, 0, '.', ',') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-container">
            <div class="sign-box">
                <div class="sign-title">Disiapkan Oleh</div>
                <div class="sign-line"></div>
                <div class="sign-name">{{ $invoice->prepared_by ?? '__________' }}</div>
            </div>
            <div class="sign-box">
                <div class="sign-title">Disetujui Oleh</div>
                <div class="sign-line"></div>
                <div class="sign-name">{{ $invoice->approved_by ?? '__________' }}</div>
            </div>
        </div>
    </div>
</body>
</html>

@php
function terbilang($n) {
    $angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    
    if (!$n && $n !== 0) return '';
    if ($n == 0) return 'Nol';
    if ($n < 0) $n = abs($n);
    
    $n = (int)$n;
    
    if ($n < 12) return $angka[$n];
    if ($n < 20) return terbilang($n - 10) . " Belas";
    if ($n < 100) return terbilang((int)($n / 10)) . " Puluh" . ($n % 10 ? " " . terbilang($n % 10) : "");
    if ($n < 200) return "Seratus" . ($n - 100 ? " " . terbilang($n - 100) : "");
    if ($n < 1000) return terbilang((int)($n / 100)) . " Ratus" . ($n % 100 ? " " . terbilang($n % 100) : "");
    if ($n < 2000) return "Seribu" . ($n - 1000 ? " " . terbilang($n - 1000) : "");
    if ($n < 1000000) return terbilang((int)($n / 1000)) . " Ribu" . ($n % 1000 ? " " . terbilang($n % 1000) : "");
    if ($n < 1000000000) return terbilang((int)($n / 1000000)) . " Juta" . ($n % 1000000 ? " " . terbilang($n % 1000000) : "");
    if ($n < 1000000000000) return terbilang((int)($n / 1000000000)) . " Milyar" . ($n % 1000000000 ? " " . terbilang($n % 1000000000) : "");
    return terbilang((int)($n / 1000000000000)) . " Triliun" . ($n % 1000000000000 ? " " . terbilang($n % 1000000000000) : "");
}
@endphp
