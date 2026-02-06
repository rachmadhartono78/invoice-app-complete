<?php
namespace Database\Seeders;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class ProductionInvoiceSeeder extends Seeder {
    public function run() {
        // Seeded data - 2 invoices exported from dev

        // Invoice: SI.2026.02.00002
        $existingInv = Invoice::where('invoice_number', 'SI.2026.02.00002')->first();
        if ($existingInv) {
            $this->command->line('⏭️  Skipping invoice SI.2026.02.00002 (already exists)');
            return;
        }

        $inv = Invoice::create([
            'invoice_number' => 'SI.2026.02.00002',
            'invoice_date' => '2026-02-06 00:00:00',
            'customer_name' => 'Rachmad Hartono',
            'customer_address' => NULL,
            'payment_terms' => 'COD',
            'po_number' => NULL,
            'currency' => 'IDR',
            'currency_name' => NULL,
            'ppn_percent' => 0.00,
            'status' => 'draft',
            'prepared_by' => NULL,
            'approved_by' => NULL,
            'notes' => NULL,
        ]);

        $inv->items()->create([
            'item_code' => '1',
            'item_name' => '1',
            'quantity' => 1,
            'unit_price' => 0.00,
            'discount' => 0.00,
            'sort_order' => 1,
        ]);
        $inv->calculate()->save();
        $this->command->info('✅ Created invoice SI.2026.02.00002');

        // Invoice: SI.2026.02.00001
        $existingInv = Invoice::where('invoice_number', 'SI.2026.02.00001')->first();
        if ($existingInv) {
            $this->command->line('⏭️  Skipping invoice SI.2026.02.00001 (already exists)');
            return;
        }

        $inv = Invoice::create([
            'invoice_number' => 'SI.2026.02.00001',
            'invoice_date' => '2001-08-09 00:00:00',
            'customer_name' => '1',
            'customer_address' => '1',
            'payment_terms' => '1',
            'po_number' => NULL,
            'currency' => 'IDR',
            'currency_name' => NULL,
            'ppn_percent' => 0.00,
            'status' => 'draft',
            'prepared_by' => 'rachmad',
            'approved_by' => 'linda',
            'notes' => NULL,
        ]);

        $inv->items()->create([
            'item_code' => '1',
            'item_name' => '1',
            'quantity' => 1,
            'unit_price' => 1.00,
            'discount' => 0.00,
            'sort_order' => 1,
        ]);
        $inv->calculate()->save();
        $this->command->info('✅ Created invoice SI.2026.02.00001');
    }
}
