<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateInvoiceStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event)
    {
        $payment = $event->payment;
        $invoice = $payment->invoice;

        if (!$invoice) {
            return;
        }

        // Recalculate total paid amount from database to be safe
        // (or we could use the amount from the invoice if we trust it's up to date)
        // Better to re-sum for accuracy
        $totalPaid = $invoice->payments()->sum('amount');
        
        $invoice->paid_amount = $totalPaid;

        // Determine status
        if ($totalPaid >= $invoice->total) {
            $invoice->status = 'PAID';
            // Only update paid_at/paid_by if not already set or if fully paying now
            if (!$invoice->paid_at) {
                $invoice->paid_at = now();
                $invoice->paid_by = $payment->created_by ?? auth()->id();
            }
        } elseif ($totalPaid > 0) {
            $invoice->status = 'PARTIAL_PAID';
            // If it was PAID but now partial (e.g. edited payment), clear paid_at
            if ($invoice->paid_at && $totalPaid < $invoice->total) {
                $invoice->paid_at = null;
                $invoice->paid_by = null;
            }
        } else {
            $invoice->status = 'INVOICED';
            $invoice->paid_at = null;
            $invoice->paid_by = null;
        }

        $invoice->save();
    }
}
