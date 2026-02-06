<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000001',
                'name' => 'Dashboard',
                'url' => 'app',
                'description' => 'Main Application Dashboard',
                'is_active' => '1',
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'name' => 'Settings',
                'url' => 'settings',
                'description' => 'Application Configuration & Management',
                'is_active' => '1',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Application::insert($data);
    }
}
