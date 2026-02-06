<?php
namespace Database\Seeders;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder {
    public function run() {
        // Create categories based on AREA from price list
        $categories = [
            'area-datang-barang' => ['name' => 'Area Datang Barang', 'description' => 'Penerimaan barang'],
            'area-cuci-ompreng' => ['name' => 'Area Cuci Ompreng', 'description' => 'Pencucian'],
            'gudang-kering-alat' => ['name' => 'Gudang Kering & Alat', 'description' => 'Penyimpanan kering'],
            'area-gudang-basah' => ['name' => 'Area Gudang Basah', 'description' => 'Penyimpanan basah (freezer)'],
            'area-masak' => ['name' => 'Area Masak', 'description' => 'Dapur/Memasak'],
            'area-persiapan' => ['name' => 'Area Persiapan', 'description' => 'Persiapan bahan'],
            'area-pemorsian' => ['name' => 'Area Pemorsian', 'description' => 'Pengemasan/Pemorsian'],
            'pelengkap-dapur' => ['name' => 'Pelengkap Dapur', 'description' => 'Alat pendukung dapur'],
            'lainnya' => ['name' => 'Lainnya', 'description' => 'Kategori lainnya'],
        ];

        $categoryIds = [];
        foreach ($categories as $slug => $data) {
            $cat = ItemCategory::firstOrCreate(
                ['slug' => $slug],
                ['name' => $data['name'], 'description' => $data['description']]
            );
            $categoryIds[$slug] = $cat->id;
        }

        // Master items dari PRICE LIST dengan harga satuan
        $items = [
            // AREA DATANG BARANG
            ['name' => 'Timbangan 150kg', 'category' => 'area-datang-barang', 'dimensi' => '400x500 mm', 'price' => 900000, 'qty' => 1],
            ['name' => 'Loading Trolley', 'category' => 'area-datang-barang', 'dimensi' => '450x725x920', 'price' => 2500000, 'qty' => 2],
            ['name' => 'Transit Table Backsplash', 'category' => 'area-datang-barang', 'dimensi' => '1500x600x850', 'price' => 3200000, 'qty' => 1],
            ['name' => 'Single Sink Knockdown', 'category' => 'area-datang-barang', 'dimensi' => '600x600x800', 'price' => 2400000, 'qty' => 1],

            // AREA CUCI OMPRENG
            ['name' => 'Triple Sink Knockdown', 'category' => 'area-cuci-ompreng', 'dimensi' => '1200x600x800', 'price' => 4000000, 'qty' => 2],
            ['name' => 'Long Sink Knockdown', 'category' => 'area-cuci-ompreng', 'dimensi' => '1700x600x800', 'price' => 4000000, 'qty' => 2],
            ['name' => 'Grease Trap', 'category' => 'area-cuci-ompreng', 'dimensi' => '500x300x300', 'price' => 1300000, 'qty' => 2],
            ['name' => 'Pengering 210 Tray', 'category' => 'area-cuci-ompreng', 'dimensi' => 'LISTRIK', 'price' => 11000000, 'qty' => 1],
            ['name' => 'Water Heater 30L', 'category' => 'area-cuci-ompreng', 'dimensi' => 'ARISTON', 'price' => 4500000, 'qty' => 1],
            ['name' => 'Rak Tiris 4 Susun Knockdown', 'category' => 'area-cuci-ompreng', 'dimensi' => '1500x500x1550', 'price' => 3200000, 'qty' => 2],

            // GUDANG KERING & ALAT
            ['name' => 'Solid Rak 4 Susun Knockdown', 'category' => 'gudang-kering-alat', 'dimensi' => '1500x500x1550', 'price' => 3000000, 'qty' => 4],
            ['name' => 'Palet Plastik', 'category' => 'gudang-kering-alat', 'dimensi' => '100x100x15', 'price' => 380000, 'qty' => 4],

            // AREA GUDANG BASAH
            ['name' => 'Chest Frezeer', 'category' => 'area-gudang-basah', 'dimensi' => '500 L', 'price' => 9000000, 'qty' => 1],
            ['name' => 'Showcase 2 Pintu', 'category' => 'area-gudang-basah', 'dimensi' => '1400x600x2000', 'price' => 10500000, 'qty' => 1],
            ['name' => 'Solid Rack 4 Tier Knockdown', 'category' => 'area-gudang-basah', 'dimensi' => '1500x500x1550', 'price' => 3000000, 'qty' => 4],

            // AREA MASAK
            ['name' => 'Rice Steamer 12 tray', 'category' => 'area-masak', 'dimensi' => '720x650x1650', 'price' => 9500000, 'qty' => 3],
            ['name' => 'Gas Stock Pot High Pressure', 'category' => 'area-masak', 'dimensi' => '600x600x500', 'price' => 3200000, 'qty' => 3],
            ['name' => 'Gas Stock Pot Low Pressure', 'category' => 'area-masak', 'dimensi' => '500x500x760', 'price' => 2500000, 'qty' => 2],
            ['name' => 'Kwali Range Single', 'category' => 'area-masak', 'dimensi' => '485x600x800', 'price' => 2800000, 'qty' => 1],
            ['name' => 'Kwali Range Double w/ Blower', 'category' => 'area-masak', 'dimensi' => '1300x800 x 800/1150', 'price' => 8500000, 'qty' => 1],
            ['name' => 'Gastronom', 'category' => 'area-masak', 'dimensi' => '380x550x1750', 'price' => 2300000, 'qty' => 2],
            ['name' => 'Double Sink Knockdown Area Masak', 'category' => 'area-masak', 'dimensi' => '1200x600x800', 'price' => 3000000, 'qty' => 1],

            // AREA PERSIAPAN
            ['name' => 'Service Trolley 3 Tiers', 'category' => 'area-persiapan', 'dimensi' => '805x460x905', 'price' => 2300000, 'qty' => 2],
            ['name' => 'Service Trolley 2 Tier', 'category' => 'area-persiapan', 'dimensi' => '805x460x900', 'price' => 2100000, 'qty' => 1],
            ['name' => 'Working Table Knockdown', 'category' => 'area-persiapan', 'dimensi' => '2000x800x800', 'price' => 4000000, 'qty' => 4],
            ['name' => 'Food Tray Stainless', 'category' => 'area-persiapan', 'dimensi' => 'Tinggi 6cm', 'price' => 35000, 'qty' => 2500],

            // AREA PEMORSIAN
            ['name' => 'Meja Pemorsian Knockdown', 'category' => 'area-pemorsian', 'dimensi' => '2000x800x800', 'price' => 4000000, 'qty' => 4],
            ['name' => 'Meja Rail Pemorsian', 'category' => 'area-pemorsian', 'dimensi' => '3000x1300x800', 'price' => 5000000, 'qty' => 2],
            ['name' => 'Solid Rack 4 Tier Knockdown Pemorsian', 'category' => 'area-pemorsian', 'dimensi' => '1500x500x1550', 'price' => 3000000, 'qty' => 2],

            // PELENGKAP DAPUR
            ['name' => 'Talenan Kayu', 'category' => 'pelengkap-dapur', 'dimensi' => '42x29,5x26', 'price' => 70000, 'qty' => 8],
            ['name' => 'Talenan Daging', 'category' => 'pelengkap-dapur', 'dimensi' => '45cm', 'price' => 185000, 'qty' => 4],
            ['name' => 'Cadangan Tray', 'category' => 'pelengkap-dapur', 'dimensi' => '50x30x7cm', 'price' => 160000, 'qty' => 24],
            ['name' => 'Pisau Daging sus 304', 'category' => 'pelengkap-dapur', 'dimensi' => '30x17x8cm', 'price' => 150000, 'qty' => 5],
            ['name' => 'Pisau Buah sus 304', 'category' => 'pelengkap-dapur', 'dimensi' => '24cm', 'price' => 85000, 'qty' => 5],
            ['name' => 'Pisau Sayur sus 304', 'category' => 'pelengkap-dapur', 'dimensi' => '33x21x4,5cm', 'price' => 75000, 'qty' => 5],
            ['name' => 'Manual Peeler Stainless', 'category' => 'pelengkap-dapur', 'price' => 15000, 'qty' => 10],
            ['name' => 'Panci (Stock Pot)', 'category' => 'pelengkap-dapur', 'dimensi' => '44,5x34', 'price' => 850000, 'qty' => 3],
            ['name' => 'Kukusan (Steamer) 3 Tingkat', 'category' => 'pelengkap-dapur', 'dimensi' => '55,5x 55,5x 48cm', 'price' => 1000000, 'qty' => 2],
            ['name' => 'Presto 12 Liter', 'category' => 'pelengkap-dapur', 'dimensi' => '45 x 34 x 32 cm', 'price' => 550000, 'qty' => 2],
            ['name' => 'Spatula', 'category' => 'pelengkap-dapur', 'price' => 130000, 'qty' => 8],
            ['name' => 'Gayung Stainless', 'category' => 'pelengkap-dapur', 'price' => 130000, 'qty' => 1],
            ['name' => 'Wajan Besar 80cm', 'category' => 'pelengkap-dapur', 'price' => 650000, 'qty' => 4],
            ['name' => 'Wajan Besar 60cm', 'category' => 'pelengkap-dapur', 'price' => 550000, 'qty' => 3],
            ['name' => 'Wajan Besar 55cm', 'category' => 'pelengkap-dapur', 'price' => 425000, 'qty' => 2],
            ['name' => 'Food Pan', 'category' => 'pelengkap-dapur', 'dimensi' => '52x32x15 cm', 'price' => 135000, 'qty' => 20],

            // LAINNYA & REQUEST TAMBAHAN
            ['name' => 'Topi masak', 'category' => 'lainnya', 'dimensi' => 'Isi 50 pcs', 'price' => 185000, 'qty' => 50],
            ['name' => 'Tempat Sampah 70 Liter', 'category' => 'lainnya', 'price' => 300000, 'qty' => 3],
            ['name' => 'Double Sink Knockdown Lainnya', 'category' => 'lainnya', 'dimensi' => '1200x600x800', 'price' => 3000000, 'qty' => 3],
            ['name' => 'Tempat Sampah 100 Liter', 'category' => 'lainnya', 'price' => 450000, 'qty' => 2],
            ['name' => 'Rak Sepatu Karyawan Stainless', 'category' => 'lainnya', 'price' => 250000, 'qty' => 2],
            ['name' => 'Loker Karyawan 24 Pintu', 'category' => 'lainnya', 'price' => 3200000, 'qty' => 2],
            
            // REQUEST TAMBAHAN
            ['name' => 'Mesin Potong Sayur', 'category' => 'lainnya', 'price' => 5500000, 'qty' => 1],
            ['name' => 'Blender 2 liter', 'category' => 'lainnya', 'price' => 1200000, 'qty' => 2],
            ['name' => 'Timbangan 40kg', 'category' => 'lainnya', 'price' => 350000, 'qty' => 1],
            ['name' => 'Baskom Solid Stainless', 'category' => 'lainnya', 'dimensi' => '80cm', 'price' => 150000, 'qty' => 6],
            ['name' => 'Baskom Sungku Lubang', 'category' => 'lainnya', 'dimensi' => '70cm', 'price' => 185000, 'qty' => 6],
            ['name' => 'Asahan Pisau', 'category' => 'lainnya', 'price' => 500000, 'qty' => 4],
            ['name' => 'Sendok Nasi Stainless', 'category' => 'lainnya', 'price' => 25000, 'qty' => 10],
            ['name' => 'APAR Powder 3 Kg', 'category' => 'lainnya', 'price' => 450000, 'qty' => 3],
            ['name' => 'P3K dan isinya', 'category' => 'lainnya', 'price' => 300000, 'qty' => 1],
            ['name' => 'Insect Killer', 'category' => 'lainnya', 'price' => 350000, 'qty' => 4],
            ['name' => 'Alat Ukur Suhu Ruangan', 'category' => 'lainnya', 'price' => 200000, 'qty' => 2],
            ['name' => 'Gantungan Pakaian', 'category' => 'lainnya', 'price' => 175000, 'qty' => 2],
            ['name' => 'Kursi Plastik Senderan Kecil', 'category' => 'lainnya', 'price' => 75000, 'qty' => 12],
            ['name' => 'Kursi Plastik Tinggi', 'category' => 'lainnya', 'price' => 60000, 'qty' => 10],
            ['name' => 'Dispenser', 'category' => 'lainnya', 'price' => 1800000, 'qty' => 2],
            ['name' => 'Serok Biasa 34', 'category' => 'lainnya', 'price' => 80000, 'qty' => 7],
            ['name' => 'Serok Biasa 26', 'category' => 'lainnya', 'price' => 60000, 'qty' => 2],
            ['name' => 'Serok Stainless 32', 'category' => 'lainnya', 'price' => 125000, 'qty' => 2],
            ['name' => 'Pencapit Makanan', 'category' => 'lainnya', 'price' => 25000, 'qty' => 10],
            ['name' => 'Keset', 'category' => 'lainnya', 'price' => 50000, 'qty' => 10],
            ['name' => 'Jerigen 30 L', 'category' => 'lainnya', 'price' => 80000, 'qty' => 10],
            ['name' => 'Kipas Angin Dinding', 'category' => 'lainnya', 'price' => 400000, 'qty' => 3],
            ['name' => 'Bakul Nasi 55 cm', 'category' => 'lainnya', 'price' => 85000, 'qty' => 23],
            ['name' => 'Pemantik Api', 'category' => 'lainnya', 'price' => 20000, 'qty' => 2],
            ['name' => 'Ember besar 48 cm', 'category' => 'lainnya', 'price' => 80000, 'qty' => 5],
            ['name' => 'Keranjang buah besar', 'category' => 'lainnya', 'price' => 80000, 'qty' => 10],
            ['name' => 'Keranjang sayur besar', 'category' => 'lainnya', 'price' => 80000, 'qty' => 10],
            ['name' => 'Alat Pel', 'category' => 'lainnya', 'price' => 225000, 'qty' => 3],
            ['name' => 'Sapu Pengki', 'category' => 'lainnya', 'price' => 200000, 'qty' => 3],
            ['name' => 'Cetakan Nasi', 'category' => 'lainnya', 'price' => 10000, 'qty' => 10],
        ];

        // Create items
        foreach ($items as $itemData) {
            Item::firstOrCreate(
                ['name' => $itemData['name']],
                [
                    'category_id' => $categoryIds[$itemData['category']],
                    'unit' => 'pcs',
                    'quantity' => $itemData['qty'],
                    'price' => $itemData['price'], // Harga satuan dari price list
                    'area' => $itemData['dimensi'] ?? null,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ ' . count($items) . ' items dengan harga satuan dari PRICE LIST seeded successfully!');
        $this->command->info('💰 Total items value: Rp ' . number_format(array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $items)), 0, ',', '.'));
    }
}

