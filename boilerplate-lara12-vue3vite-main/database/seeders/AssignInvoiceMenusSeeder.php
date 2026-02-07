<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Authority;
use App\Models\Menu;
use App\Models\Actions;
use App\Models\ActionUse;
use App\Models\MenuAuthority;
use Illuminate\Support\Facades\DB;

class AssignInvoiceMenusSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a default authority (you can modify this)
        $authority = Authority::where('name', 'like', '%Admin%')
            ->orWhere('name', 'like', '%Super%')
            ->first();

        if (!$authority) {
            $this->command->warn('⚠️  No Admin/Super authority found!');
            $this->command->info('Please create an authority first via Settings > Authorities');
            $this->command->info('Or specify the authority name in this seeder.');
            return;
        }

        // Get all actions
        $actions = Actions::all();
        if ($actions->isEmpty()) {
            $this->command->warn('⚠️  No actions found! Creating default actions...');
            $defaultActions = ['create', 'read', 'update', 'delete', 'export'];
            foreach ($defaultActions as $actionName) {
                Actions::firstOrCreate(['name' => $actionName]);
            }
            $actions = Actions::all();
        }

        // Get Invoice parent menu and its children
        $invoiceMenu = Menu::where('name', 'Invoices')
            ->whereNull('menu_id')
            ->first();

        if (!$invoiceMenu) {
            $this->command->error('❌ Invoice parent menu not found!');
            $this->command->info('Please run: php artisan db:seed --class=InvoiceMenuSeeder');
            return;
        }

        $childMenus = Menu::where('menu_id', $invoiceMenu->id)->get();
        $allMenus = collect([$invoiceMenu])->merge($childMenus);

        $this->command->info("📋 Assigning menus to authority: {$authority->name}");

        foreach ($allMenus as $menu) {
            // Check if already assigned
            $exists = MenuAuthority::where('menu_id', $menu->id)
                ->where('authority_id', $authority->id)
                ->first();

            if ($exists) {
                $this->command->info("   ⏭️  {$menu->name} - already assigned");
                continue;
            }

            // Insert menu-authority relationship
            $menuAuthority = MenuAuthority::create([
                'menu_id' => $menu->id,
                'authority_id' => $authority->id,
            ]);

            // Insert action permissions for this menu (full CRUD)
            foreach ($actions as $action) {
                ActionUse::create([
                    'menu_authority_id' => $menuAuthority->id,
                    'action_id' => $action->id,
                    'value' => 1, // enabled
                ]);
            }

            $this->command->info("   ✅ {$menu->name} - assigned with full CRUD permissions");
        }

        $this->command->info('');
        $this->command->info('🎉 Done! Invoice menus assigned to authority: ' . $authority->name);
        $this->command->info('🔄 Please logout and login again to see the new menus in sidebar');
    }
}
