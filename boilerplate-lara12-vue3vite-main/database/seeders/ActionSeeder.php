<?php

namespace Database\Seeders;

use App\Models\Actions;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000001',
                'name' => 'Create',
                'code' => 'canCreate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'name' => 'Read',
                'code' => 'canRead',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000003',
                'name' => 'Update',
                'code' => 'canUpdate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000004',
                'name' => 'Delete',
                'code' => 'canDelete',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000005',
                'name' => 'Print',
                'code' => 'canPrint',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000006',
                'name' => 'Export',
                'code' => 'canExport',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000007',
                'name' => 'Validate',
                'code' => 'canValidate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000008',
                'name' => 'Verify',
                'code' => 'canVerify',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];

        Actions::insert($data);
    }
}
