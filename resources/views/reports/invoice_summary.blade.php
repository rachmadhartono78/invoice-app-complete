<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Ringkasan Invoice</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer-stats {
            width: 300px;
            float: right;
        }
        .footer-stats table td {
            border: none;
            padding: 4px 8px;
        }
        .footer-stats .label {
            font-weight: bold;
        }
        .total-row {
            background-color: #eee;
            font-weight: bold;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Ringkasan Invoice</h1>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Status</th>
                <th class="text-right">Total</th>
                <th class="text-right">Terbayar</th>
                <th class="text-right">Piutang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                <td>{{ $invoice->customer_name }}</td>
                <td>{{ $invoice->status }}</td>
                <td class="text-right">{{ number_format($invoice->total, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($invoice->paid_amount ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($invoice->total - ($invoice->paid_amount ?? 0), 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($stats['total_amount'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($stats['total_paid'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($stats['total_outstanding'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-stats">
        <table>
            <tr>
                <td class="label">Total Invoice:</td>
                <td class="text-right">{{ $invoices->count() }}</td>
            </tr>
            <tr>
                <td class="label">Total Nilai:</td>
                <td class="text-right">{{ number_format($stats['total_amount'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Terbayar:</td>
                <td class="text-right">{{ number_format($stats['total_paid'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label" style="font-size: 14px;">Sisa Piutang:</td>
                <td class="text-right" style="font-size: 14px; color: #d32f2f; font-weight: bold;">
                    {{ number_format($stats['total_outstanding'], 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
