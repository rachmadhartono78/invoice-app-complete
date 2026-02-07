<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Authority;
use App\Models\AuthorityUser;
use App\Models\Menu;
use App\Models\MenuAuthority;
use App\Models\Actions;
use App\Models\ActionUse;
use Illuminate\Support\Facades\DB;

class FixSuperAdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔧 Starting Super Admin Permission Fix...');

        // 1. Find the User
        $user = User::where('email', 'superadmin@example.com')->first();
        if (!$user) {
            $this->command->error('❌ User superadmin@example.com not found!');
            return;
        }
        $this->command->info("✅ Found User: {$user->name} ({$user->email})");

        // 2. Find the Super Admin Authority
        $authority = Authority::where('name', 'Super Admin')->first();
        if (!$authority) {
            $this->command->error('❌ Authority "Super Admin" not found!');
            return;
        }
        $this->command->info("✅ Found Authority: {$authority->name}");

        // 3. Ensure User has this Authority
        $hasAuthority = AuthorityUser::where('user_id', $user->id)
            ->where('authority_id', $authority->id)
            ->exists();

        if (!$hasAuthority) {
            AuthorityUser::create([
                'user_id' => $user->id,
                'authority_id' => $authority->id,
            ]);
            $this->command->info("   👉 Assigned 'Super Admin' authority to user.");
        } else {
            $this->command->info("   ✅ User already has 'Super Admin' authority.");
        }

        // 4. Get Invoice Menus
        $invoiceParent = Menu::where('name', 'Invoices')->first();
        if (!$invoiceParent) {
            $this->command->error('❌ Invoice parent menu not found! Run InvoiceMenuSeeder first.');
            return;
        }

        $allInvoiceMenus = Menu::where('id', $invoiceParent->id)
            ->orWhere('menu_id', $invoiceParent->id)
            ->get();
            
        $this->command->info("📋 Found {$allInvoiceMenus->count()} invoice-related menus.");

        // 5. Assign Menus to Authority
        $actions = Actions::all();
        if ($actions->isEmpty()) {
             $defaultActions = ['create', 'read', 'update', 'delete', 'export'];
            foreach ($defaultActions as $actionName) {
                Actions::firstOrCreate(['name' => $actionName]);
            }
            $actions = Actions::all();
        }

        foreach ($allInvoiceMenus as $menu) {
            // Check/Create MenuAuthority
            $menuAuth = MenuAuthority::firstOrCreate(
                [
                    'menu_id' => $menu->id,
                    'authority_id' => $authority->id
                ]
            );

            // Grant ALL actions
            foreach ($actions as $action) {
                ActionUse::firstOrCreate(
                    [
                        'menu_authority_id' => $menuAuth->id,
                        'action_id' => $action->id
                    ],
                    ['value' => 1]
                );
            }
            $this->command->info("   ✅ Granted full access to: {$menu->name} ({$menu->url})");
        }

        $this->command->info('🎉 Super Admin permissions fixed successfully!');
    }
}
