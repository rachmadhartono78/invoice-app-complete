<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ActionSeeder::class,
            ApplicationSeeder::class,
            MenuSeeder::class,
            OrganizationSeeder::class,
            AuthoritySeeder::class,
            UserSeeder::class,
        ]);
    }
}
