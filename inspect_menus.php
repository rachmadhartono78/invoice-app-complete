<?php

use App\Models\User;
use App\Models\Menu;
use App\Models\Application;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'superadmin@example.com')->first();
if (!$user) {
    echo "Super Admin not found!\n";
    exit;
}

echo "User: " . $user->name . " (" . $user->email . ")\n";
echo "Authorities: " . $user->authorities->pluck('name')->implode(', ') . "\n";
echo "---------------------------------------------------\n";

$menus = $user->authorities->flatMap(function ($authority) {
    return $authority->menus;
});

// Group by Menu Induk (Parent)
$grouped = [];

foreach ($menus as $menu) {
    $parentName = $menu->menu_induk ? $menu->menu_induk->name : $menu->name;

    if (!isset($grouped[$parentName])) {
        $grouped[$parentName] = [
            'type' => $menu->menu_induk ? 'Parent' : 'Single',
            'url' => $menu->menu_induk ? $menu->menu_induk->url : $menu->url,
            'children' => []
        ];
    }

    if ($menu->menu_induk) {
        $appUrl = $menu->application->url;
        $parentUrl = $menu->menu_induk->url;
        $childUrl = $menu->url;

        // Logic from auth.ts
        $parts = array_filter([$appUrl, $parentUrl, $childUrl], function ($p) {
            return !empty($p); });
        $fullPath = '/app/' . implode('/', $parts);

        $grouped[$parentName]['children'][] = [
            'name' => $menu->name,
            'url' => $menu->url, // Database value
            'full_path' => $fullPath // Calculated value
        ];
    }
}

foreach ($grouped as $name => $data) {
    echo "Menu Group: $name\n";
    if (!empty($data['children'])) {
        foreach ($data['children'] as $child) {
            echo "  - " . str_pad($child['name'], 20) . " | DB Url: " . str_pad($child['url'], 10) . " | Full Path: " . $child['full_path'] . "\n";
        }
    }
    else {
        echo "  (Single Menu) URL: " . $data['url'] . "\n";
    }
    echo "\n";
}
