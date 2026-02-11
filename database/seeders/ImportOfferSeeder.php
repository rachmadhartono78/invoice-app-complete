<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\ItemCategory;

class ImportOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('MASTER PENAWARAN BARU 2026 rachmad.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: $csvPath");
            return;
        }

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file, 0, ';'); // Semicolon separator based on file preview

        $currentArea = null;
        $count = 0;

        $this->command->info("Importing items from CSV...");

        DB::beginTransaction();
        try {
            // Optional: Truncate items to start fresh (or keep usage of updateOrCreate)
            // Item::truncate(); 
            // ItemCategory::truncate();

            while (($row = fgetcsv($file, 0, ';')) !== false) {
                // Map columns based on CSV structure
                // 0: AREA
                // 1: ALAT
                // 2: Dimensi(mm)
                // 3: HARGA
                // 4: QTY
                // 5: JUMLAH

                $area = trim($row[0] ?? '');
                $name = trim($row[1] ?? '');
                $dimension = trim($row[2] ?? '');
                $priceRaw = trim($row[3] ?? '');
                $qtyRaw = trim($row[4] ?? '');

                // Skip partial/empty rows or total rows
                if (($name === '' && $area === '') || stripos($area, 'Total') !== false || stripos($name, 'Total') !== false) {
                    continue;
                }

                // If Area is present, update current area
                if ($area !== '') {
                    $currentArea = $area;
                }

                // If Name is present (it's an item)
                if ($name !== '') {
                    // Clean price: remove "Rp", dots, spaces
                    $price = preg_replace('/[^0-9]/', '', $priceRaw);
                    $quantity = preg_replace('/[^0-9]/', '', $qtyRaw);

                    if (!is_numeric($price))
                        $price = 0;
                    if (!is_numeric($quantity))
                        $quantity = 0;

                    // Create/Find Category based on Area
                    $category = null;
                    if ($currentArea) {
                        $category = ItemCategory::firstOrCreate(
                        ['name' => $currentArea],
                        ['slug' => \Illuminate\Support\Str::slug($currentArea)]
                        );
                    }

                    // Create Item
                    Item::updateOrCreate(
                    ['name' => $name], // Unique by name
                    [
                        'category_id' => $category ? $category->id : null,
                        'area' => $currentArea, // Store area directly too if needed
                        'description' => $dimension ? "Dimensi: $dimension" : null,
                        'price' => $price,
                        'quantity' => $quantity, // Default stock from CSV
                        'unit' => 'pcs', // Default unit
                        'is_active' => true,
                        'code' => 'ITM-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT) // Generate simple code
                    ]
                    );

                    $count++;
                    if ($count % 10 === 0) {
                        $this->command->info("Imported $count items...");
                    }
                }
            }

            DB::commit();
            $this->command->info("Successfully imported $count items!");

        }
        catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error importing CSV: " . $e->getMessage());
            Log::error($e);
        }
        finally {
            fclose($file);
        }
    }
}
