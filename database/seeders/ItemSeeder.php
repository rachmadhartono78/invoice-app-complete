<?php
namespace Database\Seeders;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder {
    public function run() {
        $csvPath = base_path('MASTER PENAWARAN BARU 2026 rachmad.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("⛔ CSV File not found at: $csvPath");
            return;
        }

        $this->command->info("📂 Reading CSV from: $csvPath");

        $file = fopen($csvPath, 'r');
        $currentCategory = null;
        $count = 0;
        $skipped = 0;

        // Skip header lines manually if needed, or handle in loop
        // We trigger on "Rp" in price column to identify item rows, 
        // and column 0 for category changes.

        while (($row = fgetcsv($file, 1000, ';')) !== false) {
            // Row structure:
            // 0: AREA (Category)
            // 1: ALAT (Name)
            // 2: Dimensi (Area)
            // 3: HARGA (Price)
            // 4: QTY (Quantity)
            // 5: JUMLAH (Total) - ignore

            // Cleanup whitespace
            $row = array_map('trim', $row);

            // Skip empty rows or header rows that don't look like data
            if (empty($row[1]) && empty($row[3])) {
                continue;
            }

            // Check if this row defines a new category
            if (!empty($row[0]) && $row[0] !== 'Total' && $row[0] !== 'Grand Total' && $row[0] !== 'AREA' && $row[0] !== 'PRICELIST' && $row[0] !== 'REQUEST TAMBAHAN') {
                $categoryName = $row[0];
                $slug = \Illuminate\Support\Str::slug($categoryName);
                
                $currentCategory = ItemCategory::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $categoryName, 'description' => 'Kategori: ' . $categoryName]
                );
            }

            // Check if this is a valid item row
            // Must have a name, and a price containing "Rp"
            if (!empty($row[1]) && (str_contains($row[3], 'Rp') || is_numeric(str_replace(['Rp', '.', ' '], '', $row[3])))) {
                
                if (!$currentCategory) {
                    $skipped++;
                    continue; // Skip items found before any category is defined
                }

                $name = $row[1];
                $dimension = $row[2];
                $priceStr = $row[3];
                $qtyStr = $row[4];

                // Parse Price
                $price = (float) str_replace(['Rp', '.', ' '], '', $priceStr);
                
                // Parse Qty (default to 0 if empty or bonus)
                $qty = (int) $qtyStr;

                Item::firstOrCreate(
                    ['name' => $name],
                    [
                        'category_id' => $currentCategory->id,
                        'unit' => 'pcs', // Default unit
                        'quantity' => $qty,
                        'price' => $price,
                        'area' => $dimension, // Save dimension into area field
                        'is_active' => true,
                        // Generate code based on category and index? Or leave auto-gen?
                        // Let's leave code empty to trigger auto-gen in model if we implemented it for Items too,
                        // but Item model doesn't have auto-gen boot method yet. 
                        // Let's set a code.
                        'code' => strtoupper(substr($currentCategory->slug, 0, 3)) . '-' . rand(1000, 9999)
                    ]
                );
                $count++;
            }
        }

        fclose($file);

        $this->command->info("✅ Imported $count items successfully!");
        if ($skipped > 0) {
            $this->command->warn("⚠️ Skipped $skipped items because no category was set.");
        }
    }
}

