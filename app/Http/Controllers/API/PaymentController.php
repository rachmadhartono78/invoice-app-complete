<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Events\PaymentReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['invoice', 'creator']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('invoice', function ($iq) use ($search) {
                        $iq->where('invoice_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by invoice
        if ($request->has('invoice_id') && $request->invoice_id !== '' && $request->invoice_id !== null) {
            $query->where('invoice_id', $request->invoice_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '' && $request->date_from !== null) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '' && $request->date_to !== null) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        return $query->orderBy('payment_date', 'desc')->paginate($request->per_page ?? 15);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,check,giro,credit_card,other',
            'reference_number' => 'nullable|max:255',
            'notes' => 'nullable',
            'receipt_file' => 'nullable|max:255'
        ]);

        // Check invoice exists and get remaining amount
        $invoice = Invoice::findOrFail($validated['invoice_id']);
        
        if (in_array($invoice->status, ['PAID', 'VOID'])) {
            return response()->json([
                'message' => '❌ Cannot add payment to invoice with status ' . $invoice->status
            ], 422);
        }

        $totalPaid = $invoice->payments()->sum('amount');
        $remaining = $invoice->total - $totalPaid;

        if ($validated['amount'] > $remaining) {
            return response()->json([
                'message' => "❌ Payment amount exceeds remaining balance. Remaining: Rp " . number_format($remaining, 2)
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate payment number
            $validated['payment_number'] = Payment::generatePaymentNumber();
            $validated['created_by'] = auth()->id();
            
            $payment = Payment::create($validated);
            
            // Dispatch event to update invoice status
            PaymentReceived::dispatch($payment);

            DB::commit();

            return response()->json([
                'message' => '✅ Payment recorded successfully',
                'data' => $payment->load(['invoice', 'creator'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '❌ Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Payment $payment)
    {
        return $payment->load(['invoice', 'creator']);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,check,giro,credit_card,other',
            'reference_number' => 'nullable|max:255',
            'notes' => 'nullable',
            'receipt_file' => 'nullable|max:255'
        ]);

        $invoice = $payment->invoice;
        
        DB::beginTransaction();
        try {
            // Calculate new total paid
            $otherPayments = $invoice->payments()->where('id', '!=', $payment->id)->sum('amount');
            $newTotalPaid = $otherPayments + $validated['amount'];

            if ($newTotalPaid > $invoice->total) {
                return response()->json([
                    'message' => "❌ Payment amount would exceed invoice total"
                ], 422);
            }

            $payment->update($validated);

            // Dispatch event to update invoice status
            PaymentReceived::dispatch($payment);

            DB::commit();

            return response()->json([
                'message' => '✅ Payment updated successfully',
                'data' => $payment->load(['invoice', 'creator'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '❌ Failed to update payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;

        DB::beginTransaction();
        try {
            $paymentAmount = $payment->amount;
            $payment->delete();

            // Dispatch event to update invoice status (recalculation)
            PaymentReceived::dispatch($payment);

            DB::commit();

            return response()->json([
                'message' => '✅ Payment deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '❌ Failed to delete payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function nextPaymentNumber()
    {
        return response()->json([
            'payment_number' => Payment::generatePaymentNumber()
        ]);
    }
}
