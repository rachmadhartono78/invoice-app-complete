<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Mail\InvoiceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller {
    public function index(Request $request) {
        // Eager load relationships to prevent N+1 queries
        $query = Invoice::with('items', 'customer', 'payments');
        
        // Apply search filter
        if($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('po_number', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter (independent of search)
        if($request->status && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        return $query->orderBy('invoice_date', 'desc')->paginate($request->per_page ?? 15);
    }
    
    public function unpaid(Request $request) {
        // Get invoices that are not fully paid and not void/draft
        $invoices = Invoice::with(['payments'])
            ->whereNotIn('status', ['PAID', 'VOID', 'DRAFT', 'paid', 'void', 'draft'])
            ->get()
            ->map(function($invoice) {
                $paidAmount = $invoice->payments->sum('amount');
                $remainingBalance = $invoice->total - $paidAmount;
                
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_name' => $invoice->customer_name,
                    'invoice_date' => $invoice->invoice_date,
                    'total' => (float) $invoice->total,
                    'paid_amount' => (float) $paidAmount,
                    'remaining_balance' => (float) $remainingBalance,
                    'status' => $invoice->status
                ];
            })
            ->filter(function($invoice) {
                // Only return invoices with remaining balance > 0
                return $invoice['remaining_balance'] > 0.01; // Use 0.01 to handle float precision
            })
            ->values();
        
        return response()->json($invoices);
    }
    
    public function store(Request $request) {
        $data = $request->validate([
            'invoice_date' => 'required|date',
            'customer_name' => 'required',
            'customer_address' => 'nullable',
            'payment_terms' => 'nullable',
            'expedition' => 'nullable',
            'po_number' => 'nullable',
            'currency_name' => 'nullable',
            'discount' => 'nullable|numeric|min:0',
            'ppn_percent' => 'nullable|numeric|min:0|max:100',
            'other_charges' => 'nullable|numeric|min:0',
            'prepared_by' => 'nullable',
            'approved_by' => 'nullable',
            'notes' => 'nullable',
            'terbilang' => 'nullable',
            'subtotal' => 'nullable|numeric',
            'ppn_amount' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'project_name' => 'nullable',
            'delivery_phase' => 'nullable',
            'internal_notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required',
            'items.*.item_name' => 'required',
            'items.*.quantity' => 'required|numeric',
            'items.*.unit_price' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.area' => 'nullable|string'
        ]);

        // Set status and audit fields
        $data['status'] = 'DRAFT';
        $data['created_by'] = auth()->id();

        // Create invoice using transaction
        $invoice = \DB::transaction(function () use ($data, $request) {
            $invoice = Invoice::create($data);
            
            foreach($request->items as $i => $item) {
                $validatedItem = [
                    'item_code' => $item['item_code'] ?? '',
                    'item_name' => $item['item_name'] ?? '',
                    'area' => $item['area'] ?? null,
                    'item_id' => $item['item_id'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'sort_order' => $i + 1
                ];
                $invoice->items()->create($validatedItem);
            }
            
            return $invoice;
        });

        return response()->json([
            'message' => '✅ Invoice created successfully',
            'data' => $invoice->load('items')
        ], 201);
    }
    
    public function show(Invoice $invoice) { return $invoice->load('items'); }
    
    public function update(Request $request, Invoice $invoice) {
        // Check if invoice can be edited based on status
        if (in_array($invoice->status, ['PAID', 'VOID'])) {
            return response()->json(['message' => 'Cannot edit invoice with status ' . $invoice->status], 403);
        }

        // Validate all input
        $data = $request->validate([
            'invoice_date' => 'required|date',
            'customer_name' => 'required',
            'customer_address' => 'nullable',
            'payment_terms' => 'nullable',
            'expedition' => 'nullable',
            'po_number' => 'nullable',
            'currency_name' => 'nullable',
            'discount' => 'nullable|numeric|min:0',
            'ppn_percent' => 'nullable|numeric|min:0|max:100',
            'other_charges' => 'nullable|numeric|min:0',
            'prepared_by' => 'nullable',
            'approved_by' => 'nullable',
            'notes' => 'nullable',
            'terbilang' => 'nullable',
            'subtotal' => 'nullable|numeric',
            'ppn_amount' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'status' => 'nullable|in:DRAFT,QUOTED,INVOICED,PARTIAL_PAID,PAID,VOID',
            'project_name' => 'nullable',
            'delivery_phase' => 'nullable',
            'internal_notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required',
            'items.*.item_name' => 'required',
            'items.*.quantity' => 'required|numeric',
            'items.*.unit_price' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.area' => 'nullable|string'
        ]);

        // Use transaction to ensure data consistency
        \DB::transaction(function () use ($invoice, $data, $request) {
            // Update invoice header only (exclude items)
            $invoice->update($data);
            
            // Delete old items and recreate
            $invoice->items()->delete();
            foreach($request->items as $i => $item) {
                $validatedItem = [
                    'item_code' => $item['item_code'] ?? '',
                    'item_name' => $item['item_name'] ?? '',
                    'area' => $item['area'] ?? null,
                    'item_id' => $item['item_id'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'sort_order' => $i + 1
                ];
                $invoice->items()->create($validatedItem);
            }
        });

        return response()->json([
            'message' => '✅ Invoice updated successfully',
            'data' => $invoice->load('items')
        ]);
    }
    
    // Status transition actions
    public function markAsQuoted(Request $request, Invoice $invoice) {
        if ($invoice->status !== 'DRAFT') {
            return response()->json(['message' => 'Can only quote invoices in DRAFT status'], 403);
        }
        
        $invoice->update([
            'status' => 'QUOTED',
            'quoted_by' => auth()->id(),
            'quoted_at' => now(),
        ]);
        
        return response()->json([
            'message' => '✅ Invoice marked as quoted',
            'data' => $invoice
        ]);
    }
    
    public function markAsInvoiced(Request $request, Invoice $invoice) {
        if (!in_array($invoice->status, ['QUOTED', 'DRAFT'])) {
            return response()->json(['message' => 'Cannot invoice this invoice'], 403);
        }
        
        $invoice->update([
            'status' => 'INVOICED',
            'invoiced_by' => auth()->id(),
            'invoiced_at' => now(),
        ]);
        
        return response()->json([
            'message' => '✅ Invoice marked as invoiced',
            'data' => $invoice
        ]);
    }
    
    public function markAsPaid(Request $request, Invoice $invoice) {
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0'
        ]);
        
        if (!in_array($invoice->status, ['INVOICED', 'PARTIAL_PAID'])) {
            return response()->json(['message' => 'Can only mark invoiced or partial paid invoices as complete'], 403);
        }
        
        $invoice->update([
            'status' => 'PAID',
            'paid_by' => auth()->id(),
            'paid_at' => now(),
            'paid_amount' => $validated['paid_amount']
        ]);
        
        return response()->json([
            'message' => '✅ Invoice marked as fully paid',
            'data' => $invoice
        ]);
    }
    
    public function markAsPartialPaid(Request $request, Invoice $invoice) {
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0'
        ]);
        
        if (!in_array($invoice->status, ['INVOICED', 'PARTIAL_PAID'])) {
            return response()->json(['message' => 'Can only collect partial payment on invoiced invoices'], 403);
        }
        
        $currentPaid = $invoice->paid_amount ?? 0;
        $newPaidAmount = $currentPaid + $validated['paid_amount'];
        
        if ($newPaidAmount >= $invoice->total) {
            return response()->json(['message' => 'Amount exceeds invoice total. Use mark as paid instead.'], 400);
        }
        
        $invoice->update([
            'status' => 'PARTIAL_PAID',
            'paid_by' => auth()->id(),
            'paid_at' => now(),
            'paid_amount' => $newPaidAmount
        ]);
        
        return response()->json([
            'message' => '✅ Partial payment recorded',
            'data' => $invoice
        ]);
    }
    
    public function markAsVoid(Request $request, Invoice $invoice) {
        $validated = $request->validate([
            'void_reason' => 'nullable|string'
        ]);
        
        if (in_array($invoice->status, ['PAID', 'VOID'])) {
            return response()->json(['message' => 'Cannot void paid or already voided invoices'], 403);
        }
        
        $invoice->update([
            'status' => 'VOID',
            'internal_notes' => ($invoice->internal_notes ? $invoice->internal_notes . '\n' : '') . 
                               'VOIDED: ' . ($validated['void_reason'] ?? 'No reason provided')
        ]);
        
        return response()->json([
            'message' => '✅ Invoice voided',
            'data' => $invoice
        ]);
    }
    
    public function destroy(Invoice $invoice) {
        $invoice->delete();
        return response()->json(['message'=>'Deleted']);
    }
    
    public function pdf(Invoice $invoice) {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))->setPaper('a4');
        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf");
    }
    
    public function nextNumber() {
        return response()->json(['next_number' => Invoice::generateNumber()]);
    }

    public function sendEmail(Request $request, Invoice $invoice) {
        // Validate customer has email
        if (!$invoice->customer_id) {
            return response()->json([
                'message' => '❌ Customer email is required to send invoice'
            ], 422);
        }

        $customer = $invoice->customer;
        if (!$customer || !$customer->email) {
            return response()->json([
                'message' => '❌ Customer does not have a valid email address'
            ], 422);
        }

        try {
            // Send email using queue
            Mail::to($customer->email)
                ->queue(new InvoiceNotification($invoice));

            return response()->json([
                'message' => "✅ Email queued successfully for {$customer->email}",
                'data' => [
                    'invoice_id' => $invoice->id,
                    'customer_email' => $customer->email,
                    'sent_at' => now()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send invoice email', [
                'invoice_id' => $invoice->id,
                'customer_email' => $customer->email ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => '❌ Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
}
