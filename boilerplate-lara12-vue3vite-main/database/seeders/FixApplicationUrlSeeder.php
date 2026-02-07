<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;

class FixApplicationUrlSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔧 Fixing Application URL...');

        $app = Application::where('name', 'Main Application')->first();

        if ($app) {
            // Set URL to empty string to remove it from the path
            $app->url = ''; 
            $app->save();
            
            $this->command->info("✅ Updated 'Main Application' URL to empty string.");
        } else {
            $this->command->warn("⚠️ 'Main Application' not found.");
        }
        
        $this->command->info('🎉 Application URL fixed!');
    }
}
