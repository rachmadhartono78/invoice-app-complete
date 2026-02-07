<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function stats()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. KPI Cards
        $totalAr = Invoice::whereIn('status', ['INVOICED', 'PARTIAL_PAID'])
            ->sum(DB::raw('total - COALESCE(paid_amount, 0)'));

        $paidThisMonth = Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $quotationCount = Invoice::where('status', 'QUOTED')->count();
        
        $totalOverdue = Invoice::whereIn('status', ['INVOICED', 'PARTIAL_PAID'])
            ->get()
            ->filter->is_overdue
            ->sum(function($invoice) {
                return $invoice->total - ($invoice->paid_amount ?? 0);
            });

        // 2. Status Breakdown for Chart
        $statusBreakdown = Invoice::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // 3. Monthly Trend (last 6 months)
        $monthlyTrend = Invoice::select(
                DB::raw("DATE_FORMAT(invoice_date, '%Y-%m') as month"),
                DB::raw("SUM(total) as total_amount"),
                DB::raw("SUM(COALESCE(paid_amount, 0)) as paid_amount")
            )
            ->where('invoice_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 4. Recent Activities
        $recentInvoices = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPayments = Payment::with('invoice')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'kpis' => [
                'total_ar' => $totalAr,
                'paid_this_month' => $paidThisMonth,
                'quotation_count' => $quotationCount,
                'total_overdue' => $totalOverdue,
            ],
            'status_breakdown' => $statusBreakdown,
            'monthly_trend' => $monthlyTrend,
            'recent_invoices' => $recentInvoices,
            'recent_payments' => $recentPayments,
        ]);
    }

    public function exportReport(Request $request)
    {
        $invoices = Invoice::with('customer')
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->orderBy('invoice_date', 'desc')
            ->get();

        $stats = [
            'total_amount' => $invoices->sum('total'),
            'total_paid' => $invoices->sum('paid_amount'),
            'total_outstanding' => $invoices->sum('total') - $invoices->sum('paid_amount'),
        ];

        $pdf = Pdf::loadView('reports.invoice_summary', compact('invoices', 'stats'))->setPaper('a4', 'landscape');
        
        return $pdf->stream('Invoice_Summary_Report.pdf');
    }
}
