<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'code' => 'CUST001',
                'name' => 'PT. Maju Jaya',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'phone' => '+62 21 5551234',
                'email' => 'info@majujaya.com',
                'contact_person' => 'Budi Santoso',
                'contact_phone' => '+62 812 3456 7890',
                'tax_id' => '01.234.567.8-901.000',
                'payment_terms' => 'net_30',
                'credit_limit' => 100000000,
                'is_active' => true,
            ],
            [
                'code' => 'CUST002',
                'name' => 'CV. Berkah Sejahtera',
                'address' => 'Jl. Gatot Subroto No. 456, Jakarta Selatan',
                'phone' => '+62 21 5555678',
                'email' => 'berkah@sejahtera.co.id',
                'contact_person' => 'Siti Nurhaliza',
                'contact_phone' => '+62 813 9876 5432',
                'tax_id' => '02.345.678.9-012.000',
                'payment_terms' => 'net_45',
                'credit_limit' => 50000000,
                'is_active' => true,
            ],
            [
                'code' => 'CUST003',
                'name' => 'PT. Teknologi Indonesia',
                'address' => 'Jl. HR Rasuna Said No. 789, Jakarta Selatan',
                'phone' => '+62 21 5559012',
                'email' => 'contact@tekindo.com',
                'contact_person' => 'Ahmad Wijaya',
                'contact_phone' => '+62 815 2468 1357',
                'tax_id' => '03.456.789.0-123.000',
                'payment_terms' => 'net_30',
                'credit_limit' => 75000000,
                'is_active' => true,
            ],
            [
                'code' => 'CUST004',
                'name' => 'UD. Sumber Rezeki',
                'address' => 'Jl. Merdeka No. 321, Bandung',
                'phone' => '+62 22 4441234',
                'email' => 'sumberrezeki@email.com',
                'contact_person' => 'Rina Kusuma',
                'contact_phone' => '+62 816 1357 2468',
                'tax_id' => '04.567.890.1-234.000',
                'payment_terms' => 'net_14',
                'credit_limit' => 25000000,
                'is_active' => true,
            ],
            [
                'code' => 'CUST005',
                'name' => 'PT. Global Solutions',
                'address' => 'Jl. Thamrin No. 999, Jakarta Pusat',
                'phone' => '+62 21 5553456',
                'email' => 'global@solutions.com',
                'contact_person' => 'David Kurniawan',
                'contact_phone' => '+62 817 9753 8642',
                'tax_id' => '05.678.901.2-345.000',
                'payment_terms' => 'net_60',
                'credit_limit' => 150000000,
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['code' => $customer['code']],
                $customer
            );
        }

        $this->command->info('✅ Customer seeder completed!');
    }
}
