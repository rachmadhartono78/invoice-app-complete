<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\ItemCategory;

class ImportItemsFromCsvSeeder extends Seeder
{
    public function run()
    {
        $csvFile = base_path('MASTER PENAWARAN BARU 2026 rachmad.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("File not found: $csvFile");
            return;
        }

        $file = fopen($csvFile, 'r');
        // $header = fgetcsv($file, 1000, ';'); // Assuming semicolon delimiter based on previous read - NO HEADER SKIP LOGIC SEEMS COMPLEX, TRUSTING LOOP

        $currentArea = '';
        $count = 0;

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($file, 1000, ';')) !== FALSE) {
                // Skip empty rows or rows with just Total
                if (empty($data[1]) && empty($data[3])) {
                    continue;
                }

                // Map CSV columns
                // 0: AREA, 1: ALAT, 2: Dimensi(mm), 3: HARGA, 4: QTY, 5: JUMLAH
                $area = trimming($data[0] ?? '');
                $name = trimming($data[1] ?? '');
                $dimensi = trimming($data[2] ?? '');
                $hargaRaw = trimming($data[3] ?? '');
                $qtyRaw = trimming($data[4] ?? '');

                if (empty($name) || strpos(strtolower($name), 'total') !== false) {
                    continue;
                }

                // If Area is present, update current area
                if (!empty($area)) {
                    $currentArea = $area;
                }

                // Clean price
                $price = 0;
                if ($hargaRaw) {
                    $cleanPrice = str_replace(['Rp', '.', ' '], '', $hargaRaw);
                    $price = (float) $cleanPrice;
                }

                // Clean Qty (default to 1 if empty or invalid)
                $qty = 1;
                if ($qtyRaw) {
                    $qty = (int) $qtyRaw;
                }

                // Find or Create Category
                $categoryId = null;
                if ($currentArea) {
                    $category = ItemCategory::firstOrCreate(['name' => $currentArea]);
                    $categoryId = $category->id;
                }

                // Update or Create Item
                Item::updateOrCreate(
                    ['name' => $name],
                    [
                        'category_id' => $categoryId,
                        'description' => $dimensi, // Store dimension in description
                        'price' => $price,
                        'quantity' => $qty, // Store default quantity
                        'unit' => 'unit', // Default unit
                        'is_active' => true,
                        'area' => $currentArea
                    ]
                );

                $count++;
            }
            DB::commit();
            $this->command->info("Successfully imported $count items.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error importing data: " . $e->getMessage());
        }

        fclose($file);
    }
}

function trimming($str) {
    return trim($str, " \t\n\r\0\x0B\xC2\xA0");
}
