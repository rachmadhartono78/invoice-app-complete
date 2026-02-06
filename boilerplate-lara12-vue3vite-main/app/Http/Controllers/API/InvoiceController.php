<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller {
    public function index(Request $request) {
        $query = Invoice::with('items');
        if($request->search) $query->where('invoice_number','like',"%{$request->search}%")->orWhere('customer_name','like',"%{$request->search}%");
        if($request->status) $query->where('status', $request->status);
        return $query->orderBy('invoice_date','desc')->paginate($request->per_page ?? 15);
    }
    
    public function store(Request $request) {
        $data = $request->validate([
            'invoice_date'=>'required|date','customer_name'=>'required','payment_terms'=>'nullable',
            'items'=>'required|array|min:1','items.*.item_code'=>'required','items.*.item_name'=>'required',
            'items.*.quantity'=>'required|numeric','items.*.unit_price'=>'required|numeric'
        ]);
        
        $invoice = Invoice::create($data);
        foreach($data['items'] as $i => $item) {
            $item['sort_order'] = $i+1;
            $invoice->items()->create($item);
        }
        $invoice->calculate()->save();
        return response()->json($invoice->load('items'), 201);
    }
    
    public function show(Invoice $invoice) { return $invoice->load('items'); }
    
    public function update(Request $request, Invoice $invoice) {
        $invoice->update($request->all());
        $invoice->items()->delete();
        foreach($request->items as $i => $item) {
            $item['sort_order'] = $i+1;
            $invoice->items()->create($item);
        }
        $invoice->calculate()->save();
        return $invoice->load('items');
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
}
