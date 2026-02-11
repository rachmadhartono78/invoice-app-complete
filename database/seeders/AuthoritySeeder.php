<?php

namespace Database\Seeders;

use App\Models\ActionUse;
use App\Models\Authority;
use App\Models\MenuAuthority;
use Illuminate\Database\Seeder;

class AuthoritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define authorities/roles
        $authorities = [
            [
                'id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001',
                'application_id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'name' => 'Super Admin',
                'code' => 'super_admin',
                'description' => 'Full system access with all permissions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'application_id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'name' => 'Admin',
                'code' => 'admin',
                'description' => 'Administrative access to manage users and organizations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000003',
                'application_id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000001',
                'name' => 'User',
                'code' => 'user',
                'description' => 'Regular user with basic access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Link authorities to menus
        $authorityMenus = [
            // Super Admin has access to all menus
            [
                'id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000001',
                'menu_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000001', // Management
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Super Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000002',
                'menu_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000002', // Applications
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Super Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000003',
                'menu_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000003', // Menus
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Super Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000004',
                'menu_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000004', // Authorities
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Super Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000005',
                'menu_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000005', // Organizations
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Super Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000006',
                'menu_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000006', // Users
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Super Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000007',
                'menu_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000007', // Actions
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Super Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Admin has limited access
            [
                'id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000008',
                'menu_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000005', // Organizations
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000002', // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000009',
                'menu_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000006', // Users
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000002', // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Define action permissions for Super Admin (full permissions on all menus)
        $actionAuthorityMenu = [
            // Super Admin - Management menu (all actions)
            [
                'id' => 'd18fa99e-8d3c-4b6b-91a3-9c0000000001',
                'action_id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000001', // Create
                'menu_authority_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000002', // Applications menu
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'd18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'action_id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000002', // Read
                'menu_authority_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000002',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'd18fa99e-8d3c-4b6b-91a3-9c0000000003',
                'action_id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000003', // Update
                'menu_authority_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000002',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'd18fa99e-8d3c-4b6b-91a3-9c0000000004',
                'action_id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000004', // Delete
                'menu_authority_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000002',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Admin - Users menu (limited permissions)
            [
                'id' => 'd18fa99e-8d3c-4b6b-91a3-9c0000000005',
                'action_id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000002', // Read only
                'menu_authority_id' => 'c18fa99c-8d3c-4b6b-91a3-9c0000000009',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Authority::insert($authorities);
        MenuAuthority::insert($authorityMenus);
        ActionUse::insert($actionAuthorityMenu);
    }
}
