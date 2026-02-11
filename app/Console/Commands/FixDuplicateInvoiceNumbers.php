<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicateInvoiceNumbers extends Command
{
    protected $signature = 'invoices:fix-duplicates';
    protected $description = 'Fix duplicate invoice numbers by renumbering them';

    public function handle()
    {
        $this->info('🔍 Checking for duplicate invoice numbers...');
        
        // Find all duplicates
        $duplicates = DB::table('invoices')
            ->select('invoice_number', DB::raw('COUNT(*) as count'))
            ->groupBy('invoice_number')
            ->having('count', '>', 1)
            ->get();
        
        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicates found!');
            return 0;
        }
        
        $this->warn(sprintf('⚠️ Found %d duplicate invoice number(s)', $duplicates->count()));
        
        foreach ($duplicates as $dup) {
            $invoices = Invoice::where('invoice_number', $dup->invoice_number)
                ->orderBy('created_at', 'asc')
                ->get();
            
            $this->line("\n📋 Invoice Number: {$dup->invoice_number} (appears {$dup->count} times)");
            
            // Keep first, renumber the rest
            foreach ($invoices->skip(1) as $index => $invoice) {
                $newNumber = $this->generateUniqueNumber($invoice->invoice_date);
                
                $oldNumber = $invoice->invoice_number;
                $invoice->invoice_number = $newNumber;
                $invoice->save();
                
                $this->line("  ✅ ID {$invoice->id} ({$invoice->customer_name}): {$oldNumber} → {$newNumber}");
            }
        }
        
        $this->info("\n✅ All duplicates fixed successfully!");
        return 0;
    }
    
    private function generateUniqueNumber($invoiceDate)
    {
        $date = \Carbon\Carbon::parse($invoiceDate);
        $year = $date->year;
        $month = $date->month;
        $prefix = 'SI.' . $date->format('Y.m.');
        
        // Get highest number for this month
        $last = Invoice::withTrashed()
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->orderByRaw("CAST(SUBSTRING(invoice_number, -5) AS UNSIGNED) DESC")
            ->first();
        
        $num = $last ? intval(substr($last->invoice_number, -5)) + 1 : 1;
        
        // Ensure uniqueness
        $maxAttempts = 100;
        while ($maxAttempts-- > 0) {
            $candidate = $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
            if (!Invoice::withTrashed()->where('invoice_number', $candidate)->exists()) {
                return $candidate;
            }
            $num++;
        }
        
        throw new \Exception("Unable to generate unique invoice number");
    }
}
