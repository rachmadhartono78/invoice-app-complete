<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Find and keep only the first occurrence of each duplicate invoice number
        // Delete the newer duplicates
        
        $duplicates = DB::table('invoices')
            ->select('invoice_number', DB::raw('COUNT(*) as count'))
            ->groupBy('invoice_number')
            ->having('count', '>', 1)
            ->get();
        
        foreach ($duplicates as $dup) {
            // Get all invoices with this number, ordered by created_at
            $invoices = DB::table('invoices')
                ->where('invoice_number', $dup->invoice_number)
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Delete all but the first one
            $idsToDelete = $invoices->skip(1)->pluck('id')->toArray();
            
            if (!empty($idsToDelete)) {
                DB::table('invoices')->whereIn('id', $idsToDelete)->delete();
                $this->command->info("Deleted duplicate entries for {$dup->invoice_number}");
            }
        }
    }

    public function down()
    {
        // No rollback needed
    }
};
