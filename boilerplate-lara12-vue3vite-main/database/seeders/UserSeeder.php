<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use App\Models\Authority;
use App\Models\AuthorityUser;
use App\Models\AuthorityUserOrganization;
use App\Models\OrganizationUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Indonesian locale
        
        // First, create the default admin users
        $this->createDefaultUsers();
        
        // Then create 2000 fake users
        // $this->createFakeUsers($faker);
    }
    
    private function createDefaultUsers()
    {
        $this->command->info('Checking default admin users...');
        
        // Check if admin users already exist
        if (User::whereIn('id', ['1', '2', '3'])->exists()) {
            $this->command->info('Default admin users already exist, skipping...');
            return;
        }
        
        $this->command->info('Creating default admin users...');
        
        $data = [
            [
                'id' => '1',
                'name' => env('SUPERADMIN_NAME', 'Super Admin'),
                'email' => env('SUPERADMIN_EMAIL', 'superadmin@example.com'),
                'password' => Hash::make(env('SUPERADMIN_PASSWORD', 'password')),
                'phone' => '+62812345678901',
                'registration_number' => 'SA-001',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '2',
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '+62812345678902',
                'registration_number' => 'ADM-001',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '3',
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'phone' => '+62812345678903',
                'registration_number' => 'USR-001',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Link users to organizations
        $organizationUser = [
            [
                'user_id' => '1', // Super Admin
                'organization_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Main Organization
            ],
            [
                'user_id' => '2', // Admin User
                'organization_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Main Organization
            ],
            [
                'user_id' => '3', // Regular User
                'organization_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000002', // Regional Office - North
            ],
        ];

        // Assign authorities (roles) to users
        $authorityUser = [
            [
                'id' => 'e18fa99e-8d3c-4b6b-91a3-9c0000000001',
                'user_id' => '1', // Super Admin
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Super Admin Authority
            ],
            [
                'id' => 'e18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'user_id' => '2', // Admin User
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000002', // Admin Authority
            ],
            [
                'id' => 'e18fa99e-8d3c-4b6b-91a3-9c0000000003',
                'user_id' => '3', // Regular User
                'authority_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000003', // User Authority
            ],
        ];

        // Link authority-user to organizations
        $authorityUserOrganization = [
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000001',
                'authority_user_id' => 'e18fa99e-8d3c-4b6b-91a3-9c0000000001',
                'organization_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Main Organization
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'authority_user_id' => 'e18fa99e-8d3c-4b6b-91a3-9c0000000002',
                'organization_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000001', // Main Organization
            ],
            [
                'id' => 'c18fa99e-8d3c-4b6b-91a3-9c0000000003',
                'authority_user_id' => 'e18fa99e-8d3c-4b6b-91a3-9c0000000003',
                'organization_id' => 'f18fa99e-8d3c-4b6b-91a3-9c0000000002', // Regional Office - North
            ],
        ];

        User::insert($data);
        OrganizationUser::insert($organizationUser);
        AuthorityUser::insert($authorityUser);
        AuthorityUserOrganization::insert($authorityUserOrganization);
        
        $this->command->info('Default admin users created successfully!');
    }
    
    private function createFakeUsers($faker)
    {
        // Get existing organizations and authorities
        $organizations = Organization::all();
        $authorities = Authority::all();
        
        if ($organizations->isEmpty()) {
            $this->command->warn('No organizations found. Creating some basic organizations first...');
            // Create some basic organizations if none exist
            $organizations = collect([
                Organization::create([
                    'id' => $faker->uuid(),
                    'name' => 'PT. Teknologi Nusantara',
                    'code' => 'TEKNUS',
                    'type' => 'Pusat',
                    'city' => 'Jakarta',
                    'longitude' => -6.2088,
                    'latitude' => 106.8456,
                ]),
                Organization::create([
                    'id' => $faker->uuid(),
                    'name' => 'PT. Digital Indonesia',
                    'code' => 'DIGIID',
                    'type' => 'Cabang',
                    'city' => 'Bandung',
                    'longitude' => -6.9175,
                    'latitude' => 107.6191,
                ]),
                Organization::create([
                    'id' => $faker->uuid(),
                    'name' => 'PT. Solusi Kreatif',
                    'code' => 'SOLKRE',
                    'type' => 'Regional',
                    'city' => 'Surabaya',
                    'longitude' => -7.2575,
                    'latitude' => 112.7521,
                ]),
            ]);
        }
        
        if ($authorities->isEmpty()) {
            $this->command->warn('No authorities found. Users will be created without authorities.');
        }
        
        $this->command->info('Creating 2000 fake users...');
        
        // Create users in chunks to avoid memory issues
        $chunkSize = 100;
        $totalUsers = 2000;
        $startId = 4; // Start after the 3 admin users
        
        for ($i = 0; $i < $totalUsers; $i += $chunkSize) {
            $currentChunk = min($chunkSize, $totalUsers - $i);
            $users = [];
            
            for ($j = 0; $j < $currentChunk; $j++) {
                $userId = $startId + $i + $j;
                $users[] = [
                    'id' => (string) $userId,
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'phone' => $faker->phoneNumber(),
                    'registration_number' => $faker->optional(0.7)->numerify('REG-####-####'),
                    'email_verified_at' => $faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                    'password' => Hash::make('password'), // Default password
                    'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    'updated_at' => now(),
                ];
            }
            
            // Insert users in batch
            User::insert($users);
            
            $this->command->info("Created " . ($i + $currentChunk) . "/$totalUsers fake users");
        }
        
        $this->command->info('Assigning organizations and authorities to fake users...');
        
        // Get the fake users only (skip admin users)
        $users = User::where('id', '>=', $startId)->get();
        
        foreach ($users as $user) {
            // Assign random organizations (1-3 organizations per user)
            $userOrganizations = $organizations->random(rand(1, min(3, $organizations->count())));
            
            foreach ($userOrganizations as $organization) {
                OrganizationUser::create([
                    'id' => $faker->uuid(),
                    'user_id' => $user->id,
                    'organization_id' => $organization->id,
                ]);
            }
            
            // Assign random authorities if they exist (0-2 authorities per user)
            if ($authorities->isNotEmpty()) {
                $numAuthorities = rand(0, min(2, $authorities->count()));
                
                if ($numAuthorities > 0) {
                    $userAuthorities = $authorities->random($numAuthorities);
                    
                    foreach ($userAuthorities as $authority) {
                        // Create authority-user relationship
                        $authorityUser = AuthorityUser::create([
                            'id' => $faker->uuid(),
                            'user_id' => $user->id,
                            'authority_id' => $authority->id,
                        ]);
                        
                        // Assign organizations to this authority (subset of user's organizations)
                        $authorityOrganizations = $userOrganizations->random(rand(1, $userOrganizations->count()));
                        
                        foreach ($authorityOrganizations as $organization) {
                            AuthorityUserOrganization::create([
                                'id' => $faker->uuid(),
                                'authority_user_id' => $authorityUser->id,
                                'organization_id' => $organization->id,
                            ]);
                        }
                    }
                }
            }
        }
        
        $this->command->info('Successfully created 2000 fake users with organizations and authorities!');
        $this->command->info('Default password for all users: password');
    }
}
