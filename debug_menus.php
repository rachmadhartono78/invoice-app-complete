<?php

use App\Models\Menu;
use App\Models\MenuAuthority;
use App\Models\Authority;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- CHECKING MENUS ---\n";
$invoices = Menu::where('name', 'Invoices')->get();
echo "Found " . $invoices->count() . " menu(s) named 'Invoices':\n";
foreach ($invoices as $inv) {
    echo "ID: " . $inv->id . " | URL: '" . $inv->url . "' | App ID: " . $inv->application_id . "\n";

    $children = Menu::where('menu_id', $inv->id)->get();
    echo "  Children count: " . $children->count() . "\n";
    foreach ($children as $child) {
        echo "    - " . $child->name . " (" . $child->url . ") [ID: " . $child->id . "]\n";
    }

    // Check Authority
    $auths = MenuAuthority::where('menu_id', $inv->id)->get();
    echo "  Assigned to Authorities: " . $auths->count() . "\n";
    foreach ($auths as $ma) {
        $role = Authority::find($ma->authority_id);
        echo "    - Authority: " . ($role ? $role->name : 'Unknown') . "\n";
    }
}

echo "\n--- CHECKING SUPER ADMIN AUTHORITY ---\n";
$sa = Authority::where('name', 'Super Admin')->first();
if ($sa) {
    $menuIds = MenuAuthority::where('authority_id', $sa->id)->pluck('menu_id')->toArray();
    echo "Super Admin has " . count($menuIds) . " menus assigned.\n";

    // Check if children are assigned
    if (isset($invoices[0])) {
        $childrenObj = Menu::where('menu_id', $invoices[0]->id)->get();
        foreach ($childrenObj as $child) {
            $isAssigned = in_array($child->id, $menuIds);
            echo "    " . $child->name . " assigned? " . ($isAssigned ? "YES" : "NO") . "\n";
        }
    }
}
