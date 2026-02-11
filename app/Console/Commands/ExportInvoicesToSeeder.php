<?php
namespace App\Console\Commands;
use App\Models\Invoice;
use Illuminate\Console\Command;

class ExportInvoicesToSeeder extends Command {
    protected $signature = 'invoices:export-to-seeder {--file= : Output file path (default: stdout)}';
    protected $description = 'Export all invoices from database to seeder format (PHP code)';

    public function handle() {
        $invoices = Invoice::with('items')->latest('id')->get();
        
        if ($invoices->isEmpty()) {
            $this->error('❌ No invoices found in database!');
            return 1;
        }
        
        $output = $this->generateSeederCode($invoices);
        
        if ($this->option('file')) {
            file_put_contents($this->option('file'), $output);
            $this->info("✅ Exported " . count($invoices) . " invoices to: " . $this->option('file'));
        } else {
            $this->line($output);
        }
        
        return 0;
    }
    
    private function generateSeederCode($invoices) {
        $code = "<?php\nnamespace Database\Seeders;\nuse App\Models\Invoice;\nuse Illuminate\Database\Seeder;\n\n";
        $code .= "class ProductionInvoiceSeeder extends Seeder {\n";
        $code .= "    public function run() {\n";
        $code .= "        // Seeded data - " . count($invoices) . " invoices exported from dev\n";
        
        foreach ($invoices as $invoice) {
            $code .= $this->invoiceToCode($invoice);
        }
        
        $code .= "    }\n";
        $code .= "}\n";
        
        return $code;
    }
    
    private function invoiceToCode($invoice) {
        $code = "\n        // Invoice: {$invoice->invoice_number}\n";
        
        // Check for existing invoice
        $code .= "        \$existingInv = Invoice::where('invoice_number', '{$invoice->invoice_number}')->first();\n";
        $code .= "        if (\$existingInv) {\n";
        $code .= "            \$this->command->line('⏭️  Skipping invoice {$invoice->invoice_number} (already exists)');\n";
        $code .= "            return;\n";
        $code .= "        }\n\n";
        
        // Create invoice
        $invDate = $invoice->invoice_date ? "'{$invoice->invoice_date}'" : 'now()';
        $code .= "        \$inv = Invoice::create([\n";
        $code .= "            'invoice_number' => '{$invoice->invoice_number}',\n";
        $code .= "            'invoice_date' => {$invDate},\n";
        $code .= "            'customer_name' => " . var_export($invoice->customer_name, true) . ",\n";
        $code .= "            'customer_address' => " . var_export($invoice->customer_address, true) . ",\n";
        $code .= "            'payment_terms' => " . var_export($invoice->payment_terms, true) . ",\n";
        $code .= "            'po_number' => " . var_export($invoice->po_number, true) . ",\n";
        $code .= "            'currency' => " . var_export($invoice->currency, true) . ",\n";
        $code .= "            'currency_name' => " . var_export($invoice->currency_name, true) . ",\n";
        $code .= "            'ppn_percent' => {$invoice->ppn_percent},\n";
        $code .= "            'status' => '{$invoice->status}',\n";
        $code .= "            'prepared_by' => " . var_export($invoice->prepared_by, true) . ",\n";
        $code .= "            'approved_by' => " . var_export($invoice->approved_by, true) . ",\n";
        $code .= "            'notes' => " . var_export($invoice->notes, true) . ",\n";
        $code .= "        ]);\n\n";
        
        // Create items
        foreach ($invoice->items as $item) {
            $code .= "        \$inv->items()->create([\n";
            $code .= "            'item_code' => " . var_export($item->item_code, true) . ",\n";
            $code .= "            'item_name' => " . var_export($item->item_name, true) . ",\n";
            $code .= "            'quantity' => {$item->quantity},\n";
            $code .= "            'unit_price' => {$item->unit_price},\n";
            $code .= "            'discount' => {$item->discount},\n";
            $code .= "            'sort_order' => {$item->sort_order},\n";
            $code .= "        ]);\n";
        }
        
        $code .= "        \$inv->calculate()->save();\n";
        $code .= "        \$this->command->info('✅ Created invoice {$invoice->invoice_number}');\n";
        
        return $code;
    }
}
