<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoiceType }} - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
        }
        .info-box {
            background-color: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            color: #333;
            text-align: right;
        }
        .invoice-summary {
            margin: 30px 0;
            border: 1px solid #ddd;
            border-collapse: collapse;
            width: 100%;
        }
        .invoice-summary tr {
            border-bottom: 1px solid #ddd;
        }
        .invoice-summary td {
            padding: 12px;
        }
        .invoice-summary tr:last-child {
            border-bottom: none;
        }
        .summary-label {
            font-weight: bold;
            color: #666;
            width: 60%;
        }
        .summary-value {
            text-align: right;
            color: #333;
            font-size: 14px;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
            font-size: 18px;
            color: #667eea;
        }
        .total-row .summary-value {
            font-size: 18px;
            color: #667eea;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }
        .button:hover {
            opacity: 0.9;
        }
        .message {
            background-color: #f0f8ff;
            border: 1px solid #bce4ff;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            color: #0056b3;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #ddd;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $invoiceType }}</h1>
            <p>{{ $invoice->invoice_number }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                Yth. {{ $customerName }},
                <p style="margin: 10px 0 0 0;">
                    Kami dengan senang hati menyampaikan {{ strtolower($invoiceType) }} dengan detail sebagai berikut:
                </p>
            </div>

            <!-- Invoice Details -->
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">{{ $invoiceType }} No.:</span>
                    <span class="info-value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal {{ $invoiceType }}:</span>
                    <span class="info-value">{{ $invoice->invoice_date->format('d M Y') }}</span>
                </div>
                @if($invoice->delivery_date)
                <div class="info-row">
                    <span class="info-label">Tanggal Pengiriman:</span>
                    <span class="info-value">{{ $invoice->delivery_date->format('d M Y') }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Syarat Pembayaran:</span>
                    <span class="info-value">{{ $invoice->payment_terms ?? '-' }}</span>
                </div>
                @if($invoice->po_number)
                <div class="info-row">
                    <span class="info-label">PO No.:</span>
                    <span class="info-value">{{ $invoice->po_number }}</span>
                </div>
                @endif
            </div>

            <!-- Invoice Summary -->
            <table class="invoice-summary">
                <tr>
                    <td class="summary-label">Sub Total:</td>
                    <td class="summary-value">Rp {{ number_format($invoice->subtotal, 0, '.', ',') }}</td>
                </tr>
                @if($invoice->discount > 0)
                <tr>
                    <td class="summary-label">Diskon:</td>
                    <td class="summary-value">Rp {{ number_format($invoice->discount, 0, '.', ',') }}</td>
                </tr>
                @endif
                @if($invoice->ppn_amount > 0)
                <tr>
                    <td class="summary-label">PPN ({{ $invoice->ppn_percent ?? 0 }}%):</td>
                    <td class="summary-value">Rp {{ number_format($invoice->ppn_amount, 0, '.', ',') }}</td>
                </tr>
                @endif
                @if($invoice->other_charges > 0)
                <tr>
                    <td class="summary-label">Biaya Lain-lain:</td>
                    <td class="summary-value">Rp {{ number_format($invoice->other_charges, 0, '.', ',') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="summary-label">TOTAL:</td>
                    <td class="summary-value">Rp {{ number_format($invoice->total, 0, '.', ',') }}</td>
                </tr>
            </table>

            <!-- Message -->
            @if($invoice->status === 'QUOTED')
            <div class="message">
                <strong>Perhatian:</strong> Ini adalah penawaran harga. Mohon konfirmasi atau hubungi kami untuk informasi lebih lanjut.
            </div>
            @else
            <div class="message">
                <strong>Pembayaran:</strong> Mohon melakukan pembayaran sesuai dengan syarat pembayaran yang telah disepakati.
            </div>
            @endif

            @if($invoice->notes)
            <div style="margin: 20px 0; padding: 15px; background-color: #f5f5f5; border-radius: 4px;">
                <strong>Catatan:</strong>
                <p style="margin: 10px 0 0 0; white-space: pre-wrap;">{{ $invoice->notes }}</p>
            </div>
            @endif

            <!-- Document Attached -->
            <div style="margin: 20px 0; padding: 15px; background-color: #fff3cd; border-radius: 4px; border-left: 4px solid #ffc107;">
                <strong>📎 Dokumen PDF</strong>
                <p style="margin: 10px 0 0 0; color: #666; font-size: 14px;">
                    {{ $invoiceType }} dalam format PDF terlampir pada email ini.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                Email ini dikirim secara otomatis. Jangan membalas email ini.
            </p>
            <p style="margin: 0;">
                &copy; {{ date('Y') }} {{ config('app.name', 'Aplikasi') }}. Semua hak cipta dilindungi.
            </p>
        </div>
    </div>
</body>
</html>
