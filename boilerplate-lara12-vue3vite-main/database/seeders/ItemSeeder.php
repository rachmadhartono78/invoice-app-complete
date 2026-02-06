<?php
namespace Database\Seeders;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder {
    public function run() {
        // Create categories
        $categories = [
            'peralatan-dapur' => ['name' => 'Peralatan Dapur', 'description' => 'Komor, blender, choper, timbangan'],
            'tempat-makan' => ['name' => 'Tempat Makan', 'description' => 'Baskom, mangkok, piring, gelas'],
            'peralatan-masak' => ['name' => 'Peralatan Masak', 'description' => 'Katel, dandang, serok, centong'],
            'penyimpanan' => ['name' => 'Rak & Penyimpanan', 'description' => 'Rak, keranjang, bakul, jerigen'],
            'furniture' => ['name' => 'Furniture', 'description' => 'Kursi, meja, stand hanger'],
            'keselamatan' => ['name' => 'Safety & Emergency', 'description' => 'APAR, P3K, insect killer, alat ukur'],
            'kebersihan' => ['name' => 'Kebersihan', 'description' => 'Sapu, pel, keset, alat pel'],
            'ppe' => ['name' => 'Personal Protective Equipment', 'description' => 'Sepatu slop, sandal anti slip'],
        ];

        $categoryIds = [];
        foreach ($categories as $slug => $data) {
            $cat = ItemCategory::firstOrCreate(
                ['slug' => $slug],
                ['name' => $data['name'], 'description' => $data['description']]
            );
            $categoryIds[$slug] = $cat->id;
        }

        // Master items dari CSV
        $items = [
            // Peralatan Dapur
            ['name' => 'Kompor', 'category' => 'peralatan-dapur', 'qty' => 8],
            ['name' => 'Blender 6 lt', 'category' => 'peralatan-dapur', 'qty' => 2],
            ['name' => 'Choper 6 lt', 'category' => 'peralatan-dapur', 'qty' => 2],
            ['name' => 'Timbangan 20 kg', 'category' => 'peralatan-dapur', 'qty' => 2],

            // Tempat Makan
            ['name' => 'Baskom Stainless', 'category' => 'tempat-makan', 'qty' => 7],
            ['name' => 'Mangkok Besar', 'category' => 'tempat-makan', 'qty' => 25],
            ['name' => 'Mangkok Kecil', 'category' => 'tempat-makan', 'qty' => 35],
            ['name' => 'Mangkok 17 cm', 'category' => 'tempat-makan', 'qty' => 30],
            ['name' => 'Piring', 'category' => 'tempat-makan', 'qty' => 30],
            ['name' => 'Gelas Takar', 'category' => 'tempat-makan', 'qty' => 2],

            // Peralatan Masak
            ['name' => 'Katel 93 cm', 'category' => 'peralatan-masak', 'qty' => 3],
            ['name' => 'Katel 56 cm', 'category' => 'peralatan-masak', 'qty' => 4],
            ['name' => 'Katel 59 cm', 'category' => 'peralatan-masak', 'qty' => 4],
            ['name' => 'Katel 83 cm', 'category' => 'peralatan-masak', 'qty' => 4],
            ['name' => 'Katel 52 cm', 'category' => 'peralatan-masak', 'qty' => 3],
            ['name' => 'Dandang 50 lt', 'category' => 'peralatan-masak', 'qty' => 6],
            ['name' => 'Asahan Pisau', 'category' => 'peralatan-masak', 'qty' => 5],
            ['name' => 'Centong Nasi', 'category' => 'peralatan-masak', 'qty' => 10],
            ['name' => 'Serok 36 cm', 'category' => 'peralatan-masak', 'qty' => 7],
            ['name' => 'Serok 34 cm', 'category' => 'peralatan-masak', 'qty' => 2],
            ['name' => 'Serok 32 cm', 'category' => 'peralatan-masak', 'qty' => 2],
            ['name' => 'Serok 26 cm', 'category' => 'peralatan-masak', 'qty' => 2],
            ['name' => 'Pencapit Makanan', 'category' => 'peralatan-masak', 'qty' => 10],
            ['name' => 'Sodet Sedang', 'category' => 'peralatan-masak', 'qty' => 2],
            ['name' => 'Sodet Jumbo', 'category' => 'peralatan-masak', 'qty' => 8],
            ['name' => 'Takaran Nasi/Cetakan Nasi', 'category' => 'peralatan-masak', 'qty' => 10],

            // Penyimpanan
            ['name' => 'Rak 150 kg Stainless 4 Susun (P=100 L=45 T=150)', 'category' => 'penyimpanan', 'qty' => 4],
            ['name' => 'Rak 250 kg 5 Susun (P=100 L=45 T=150)', 'category' => 'penyimpanan', 'qty' => 2],
            ['name' => 'Keranjang', 'category' => 'penyimpanan', 'qty' => 10],
            ['name' => 'Baskom 50 L', 'category' => 'penyimpanan', 'qty' => 3],
            ['name' => 'Bakul 55 cm', 'category' => 'penyimpanan', 'qty' => 23],
            ['name' => 'Keranjang Buah Besar', 'category' => 'penyimpanan', 'qty' => 10],
            ['name' => 'Keranjang Sayur Besar', 'category' => 'penyimpanan', 'qty' => 10],
            ['name' => 'Jerigen 30 L', 'category' => 'penyimpanan', 'qty' => 10],
            ['name' => 'Ember Besar 48 cm', 'category' => 'penyimpanan', 'qty' => 5],
            ['name' => 'Ember Kecil 32 cm', 'category' => 'penyimpanan', 'qty' => 5],

            // Furniture
            ['name' => 'Kursi Plastik Senderan Kecil', 'category' => 'furniture', 'qty' => 10],
            ['name' => 'Kursi Plastik Tinggi', 'category' => 'furniture', 'qty' => 10],
            ['name' => 'Stand Hanger Stainless Besar', 'category' => 'furniture', 'qty' => 1],
            ['name' => 'Kipas Angin Dinding', 'category' => 'furniture', 'qty' => 3],
            ['name' => 'Kipas Angin Berdiri 20 Inch', 'category' => 'furniture', 'qty' => 2],

            // Keselamatan
            ['name' => 'APAR', 'category' => 'keselamatan', 'qty' => 1],
            ['name' => 'P3K dan Isinya', 'category' => 'keselamatan', 'qty' => 1],
            ['name' => 'Insect Killer', 'category' => 'keselamatan', 'qty' => 4],
            ['name' => 'Alat Ukur Suhu Ruangan', 'category' => 'keselamatan', 'qty' => 2],

            // Kebersihan
            ['name' => 'Sapu Pengki', 'category' => 'kebersihan', 'qty' => 3],
            ['name' => 'Alat Pel', 'category' => 'kebersihan', 'qty' => 3],
            ['name' => 'Keset', 'category' => 'kebersihan', 'qty' => 10],

            // PPE
            ['name' => 'Sepatu Slop/Sandal Anti Slip', 'category' => 'ppe', 'qty' => 50],

            // Lainnya
            ['name' => 'Dispenser', 'category' => 'tempat-makan', 'qty' => 2],
            ['name' => 'Pemantik Api', 'category' => 'peralatan-masak', 'qty' => 2],
        ];

        // Create items
        foreach ($items as $itemData) {
            Item::firstOrCreate(
                ['name' => $itemData['name']],
                [
                    'category_id' => $categoryIds[$itemData['category']],
                    'unit' => 'pcs',
                    'quantity' => $itemData['qty'],
                    'price' => 0, // Will be set separately or from quotation
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ ' . count($items) . ' items seeded successfully!');
    }
}
