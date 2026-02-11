<?php
namespace Database\Seeders;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run()
    {
        // Skip if invoices already exist (for safety on production)
        if (Invoice::exists()) {
            $this->command->info('✅ Invoices already exist. Skipping seeding.');
            return;
        }

        // Create sample invoice if database is empty
        $inv = Invoice::create([
            'invoice_number' => 'SI.2026.02.00001',
            'invoice_date' => '2026-01-09',
            'customer_name' => 'PT Edo Mandiri Pratama',
            'customer_address' => 'Jakarta',
            'payment_terms' => 'C.O.D',
            'po_number' => '',
            'currency' => 'IDR',

            'ppn_percent' => 0,
            'status' => 'paid',
            'prepared_by' => 'BAGUS',
            'approved_by' => 'AEN',
            'notes' => 'Sample procurement invoice',
        ]);

        $inv->items()->create([
            'item_code' => 'PD004',
            'item_name' => 'Rak Stainless 4 Tier Non Solid',
            'quantity' => 10,
            'unit_price' => 1700000,
            'discount' => 0,
            'sort_order' => 1,
        ]);

        $inv->calculate()->save();

        $this->command->info('✅ Sample invoice created!');
    }
}
