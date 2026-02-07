<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Application;

class InvoiceMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Use existing 'Main Application' or create one without specific URL
        // This avoids double nesting in the URL path
        $application = Application::firstOrCreate(
            ['name' => 'Main Application'],
            []  // Let application URL be null/empty
        );

        // Create Parent Menu: Invoices (Dropdown)
        $parentMenu = Menu::firstOrCreate(
            ['name' => 'Invoices', 'application_id' => $application->id, 'menu_id' => null],
            [
                'url' => 'invoices',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                'order' => 2,
            ]
        );

        // Child Menus
        $childMenus = [
            [
                'name' => 'Quotations',
                'url' => 'quotations',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                'order' => 1,
            ],
            [
                'name' => 'Invoices',
                'url' => 'invoices',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
                'order' => 2,
            ],
            [
                'name' => 'Customers',
                'url' => 'customers',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                'order' => 3,
            ],
            [
                'name' => 'Payments',
                'url' => 'payments',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                'order' => 4,
            ],
            [
                'name' => 'Items',
                'url' => 'items',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
                'order' => 5,
            ],
        ];

        foreach ($childMenus as $menuData) {
            Menu::firstOrCreate(
                [
                    'name' => $menuData['name'],
                    'application_id' => $application->id,
                    'menu_id' => $parentMenu->id
                ],
                [
                    'url' => $menuData['url'],
                    'icon' => $menuData['icon'],
                    'order' => $menuData['order'],
                ]
            );
        }

        $this->command->info('✅ Invoice menu structure created!');
        $this->command->info('📝 Next steps:');
        $this->command->info('   1. Go to Settings > Authorities');
        $this->command->info('   2. Add these menus to appropriate authorities');
        $this->command->info('   3. Assign CRUD actions to each menu');
    }
}
