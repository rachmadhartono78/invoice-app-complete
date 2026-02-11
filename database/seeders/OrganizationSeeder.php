<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001',
                'organization_id' => null,
                'code' => 'ORG001',
                'name' => 'Main Organization',
                'type' => 'HQ',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'address' => 'Jl. Main Street No. 1',
                'phone' => '021-12345678',
                'email' => 'contact@mainorg.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'organization_id' => null,
                'code' => 'ORG002',
                'name' => 'Regional Office - North',
                'type' => 'Branch',
                'city' => 'Surabaya',
                'province' => 'East Java',
                'address' => 'Jl. North Branch No. 2',
                'phone' => '031-23456789',
                'email' => 'north@mainorg.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000003',
                'organization_id' => null,
                'code' => 'ORG003',
                'name' => 'Regional Office - South',
                'type' => 'Branch',
                'city' => 'Bandung',
                'province' => 'West Java',
                'address' => 'Jl. South Branch No. 3',
                'phone' => '022-34567890',
                'email' => 'south@mainorg.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Organization::insert($data);
    }
}
