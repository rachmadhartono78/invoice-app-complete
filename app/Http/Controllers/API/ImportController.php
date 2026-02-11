<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function items(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048', // Allow csv and txt
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found'], 400);
        }

        $handle = fopen($path, 'r');
        $currentCategory = null;
        $count = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                 // Cleanup whitespace
                $row = array_map('trim', $row);

                // Skip empty rows or header rows that don't look like data
                if (empty($row[1]) && empty($row[3])) {
                    continue;
                }

                // Check if this row defines a new category (AREA)
                if (!empty($row[0]) && $row[0] !== 'Total' && $row[0] !== 'Grand Total' && $row[0] !== 'AREA' && $row[0] !== 'PRICELIST' && $row[0] !== 'REQUEST TAMBAHAN') {
                    $categoryName = $row[0];
                    $slug = Str::slug($categoryName);
                    
                    $currentCategory = ItemCategory::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $categoryName, 'description' => 'Kategori: ' . $categoryName]
                    );
                }

                // Check if this is a valid item row
                // Must have a name, and a price containing "Rp" or numeric
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
                    
                    // Parse Qty
                    $qty = (int) $qtyStr; 

                    // Check if item already exists
                    $existingItem = Item::where('name', $name)->first();
                    
                    if ($existingItem) {
                        // Update existing item
                        $existingItem->update([
                            'category_id' => $currentCategory->id,
                            'unit' => 'pcs',
                            'quantity' => $qty,
                            'price' => $price,
                            'area' => $dimension,
                            'is_active' => true,
                        ]);
                        $updated++;
                    } else {
                        // Create new item
                        Item::create([
                            'name' => $name,
                            'category_id' => $currentCategory->id,
                            'unit' => 'pcs',
                            'quantity' => $qty,
                            'price' => $price,
                            'area' => $dimension,
                            'is_active' => true,
                            'code' => strtoupper(substr($currentCategory->slug, 0, 3)) . '-' . rand(1000, 9999)
                        ]);
                        $created++;
                    }
                    $count++;
                }
            }

            DB::commit();
            fclose($handle);

            // Build informative message
            $messageParts = [];
            if ($created > 0) $messageParts[] = "$created item baru ditambahkan";
            if ($updated > 0) $messageParts[] = "$updated item diperbarui";
            if ($skipped > 0) $messageParts[] = "$skipped item dilewati";
            
            $message = count($messageParts) > 0 
                ? "Import berhasil! " . implode(', ', $messageParts) . "."
                : "Tidak ada data yang diproses.";

            return response()->json([
                'message' => $message,
                'count' => $count,
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }
}
