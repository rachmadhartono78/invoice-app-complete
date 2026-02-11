<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Get invoices that don't have status VOID or DRAFT
        $invoices = Invoice::whereNotIn('status', ['VOID', 'DRAFT', 'void', 'draft'])
                           ->with('payments')
                           ->get();

        if ($invoices->isEmpty()) {
            $this->command->warn('⚠️  No invoices found with valid status for payments.');
            $this->command->info('💡 Updating draft invoices to PENDING status...');
            
            // Update draft invoices to PENDING
            $updated = Invoice::whereIn('status', ['DRAFT', 'draft'])
                             ->update(['status' => 'PENDING']);
            
            $this->command->info("   Updated {$updated} invoices from DRAFT to PENDING");
            
            // Retry getting invoices
            $invoices = Invoice::whereNotIn('status', ['VOID', 'DRAFT', 'void', 'draft'])
                               ->with('payments')
                               ->get();
            
            if ($invoices->isEmpty()) {
                $this->command->warn('⚠️  Still no invoices found. Please create invoices first.');
                return;
            }
        }

        $paymentMethods = ['cash', 'bank_transfer', 'check', 'giro', 'credit_card'];
        $paymentCount = 0;

        foreach ($invoices as $invoice) {
            // Skip if already paid
            if (in_array(strtoupper($invoice->status), ['PAID'])) {
                continue;
            }

            // Calculate remaining amount
            $totalPaid = $invoice->payments->sum('amount');
            $remaining = $invoice->total - $totalPaid;

            if ($remaining <= 0) {
                continue;
            }

            // Randomly decide if this invoice gets payment (70% chance)
            if (rand(1, 10) > 7) {
                continue;
            }

            // Determine payment scenario
            $scenario = rand(1, 10);
            
            if ($scenario <= 3) {
                // Scenario 1: Full payment (30% chance)
                $this->createPayment($invoice, $remaining, $paymentMethods, 'full');
                $paymentCount++;
            } elseif ($scenario <= 6) {
                // Scenario 2: Single partial payment (30% chance)
                $amount = $remaining * (rand(30, 70) / 100);
                $this->createPayment($invoice, $amount, $paymentMethods, 'partial');
                $paymentCount++;
            } else {
                // Scenario 3: Multiple partial payments (40% chance)
                $paymentsNeeded = rand(2, 3);
                $amountPerPayment = $remaining / $paymentsNeeded;
                
                for ($i = 0; $i < $paymentsNeeded; $i++) {
                    // Last payment gets the exact remaining to avoid rounding issues
                    if ($i === $paymentsNeeded - 1) {
                        $currentRemaining = $remaining - ($amountPerPayment * $i);
                        $this->createPayment($invoice, $currentRemaining, $paymentMethods, 'final', $i);
                    } else {
                        // Add some variation to payment amounts (±20%)
                        $variation = rand(-20, 20) / 100;
                        $amount = $amountPerPayment * (1 + $variation);
                        $this->createPayment($invoice, $amount, $paymentMethods, 'installment', $i);
                    }
                    $paymentCount++;
                }
            }
        }

        $this->command->info("✅ Payment seeder completed!");
        $this->command->info("   Created {$paymentCount} payments for {$invoices->count()} invoices");
    }

    private function createPayment(Invoice $invoice, float $amount, array $methods, string $type = 'partial', int $sequence = 0): void
    {
        // Select random payment method
        $method = $methods[array_rand($methods)];

        // Calculate payment date (between invoice date and 60 days after)
        $invoiceDate = Carbon::parse($invoice->invoice_date);
        $daysAfter = rand(0, 60);
        $paymentDate = $invoiceDate->copy()->addDays($daysAfter);

        // Generate reference number based on payment method
        $refPrefix = match($method) {
            'cash' => 'CASH',
            'bank_transfer' => 'TRF',
            'check' => 'CHK',
            'giro' => 'GRO',
            'credit_card' => 'CC',
            default => 'PMT'
        };
        
        $referenceNumber = $refPrefix . '-' . $paymentDate->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Generate notes based on payment type
        $notes = match($type) {
            'full' => 'Pelunasan penuh',
            'partial' => 'Pembayaran sebagian (' . number_format(($amount / $invoice->total) * 100, 0) . '%)',
            'installment' => 'Angsuran ke-' . ($sequence + 1),
            'final' => 'Pelunasan terakhir',
            default => null
        };

        Payment::create([
            'payment_number' => Payment::generatePaymentNumber(),
            'invoice_id' => $invoice->id,
            'payment_date' => $paymentDate->format('Y-m-d'),
            'amount' => round($amount, 2),
            'payment_method' => $method,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
            'created_by' => $invoice->created_by ?? 1,
        ]);

        // Update invoice status based on remaining balance
        $totalPaid = $invoice->payments()->sum('amount') + $amount;
        $remaining = $invoice->total - $totalPaid;

        if ($remaining <= 0.01) { // Allow small rounding differences
            $invoice->update(['status' => 'PAID']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'PARTIAL']);
        }
    }
}
