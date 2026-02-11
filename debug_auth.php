<?php

use App\Models\Menu;
use App\Models\MenuAuthority;
use App\Models\Authority;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DEBUG AUTHORITY ---\n";

// 1. Get User Authority
$user = User::where('email', 'superadmin@example.com')->first();
echo "User: " . $user->name . "\n";
echo "User Authorities: " . $user->authorities->pluck('name')->implode(', ') . "\n";
$userAuthIds = $user->authorities->pluck('id')->toArray();

// 2. Get Invoices Menu Authority
$parent = Menu::where('name', 'Invoices')->first();
echo "Parent Menu: Invoices (ID: " . $parent->id . ")\n";
$parentAuths = $parent->authorities->pluck('name', 'id')->toArray();
echo "Parent Assigned To: " . implode(', ', $parentAuths) . "\n";

// 3. Get Quotations Menu Authority
$child = Menu::where('name', 'Quotations')->first();
echo "Child Menu: Quotations (ID: " . $child->id . ")\n";
$childAuths = $child->authorities->pluck('name', 'id')->toArray();
echo "Child Assigned To: " . implode(', ', $childAuths) . "\n";

// 4. Check Intersection
$hasParent = array_uintersect(array_keys($parentAuths), $userAuthIds, "strcasecmp");
$hasChild = array_uintersect(array_keys($childAuths), $userAuthIds, "strcasecmp");

echo "User has Parent Access? " . (count($hasParent) > 0 ? "YES" : "NO") . "\n";
echo "User has Child Access? " . (count($hasChild) > 0 ? "YES" : "NO") . "\n";

// 5. Check URL correctness in DB
echo "Parent URL: '" . $parent->url . "'\n";
echo "Child URL: '" . $child->url . "'\n";
