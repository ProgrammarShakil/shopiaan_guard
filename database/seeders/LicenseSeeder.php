<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\User;
use Illuminate\Database\Seeder;

class LicenseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test user if not exists
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Create different types of licenses
        $licenses = [
            [
                'user_id' => $user->id,
                'license_key' => License::generateLicenseKey(),
                'status' => 'active',
                'license_type' => 'regular',
                'max_domains' => 1,
                'used_domains' => 0,
                'features' => ['basic_support', 'updates'],
                'expires_at' => now()->addYear(),
                'is_trial' => false,
            ],
            [
                'user_id' => $user->id,
                'license_key' => License::generateLicenseKey(),
                'status' => 'active',
                'license_type' => 'extended',
                'max_domains' => 3,
                'used_domains' => 1,
                'features' => ['basic_support', 'updates', 'priority_support'],
                'expires_at' => now()->addYears(2),
                'is_trial' => false,
            ],
            [
                'user_id' => $user->id,
                'license_key' => License::generateLicenseKey(),
                'status' => 'active',
                'license_type' => 'lifetime',
                'max_domains' => 5,
                'used_domains' => 2,
                'features' => ['basic_support', 'updates', 'priority_support', 'custom_features'],
                'expires_at' => null,
                'is_trial' => false,
            ],
            [
                'user_id' => $user->id,
                'license_key' => License::generateLicenseKey(),
                'status' => 'suspended',
                'license_type' => 'regular',
                'max_domains' => 1,
                'used_domains' => 1,
                'features' => ['basic_support'],
                'expires_at' => now()->addMonths(6),
                'is_trial' => false,
            ],
            [
                'user_id' => $user->id,
                'license_key' => License::generateLicenseKey(),
                'status' => 'expired',
                'license_type' => 'regular',
                'max_domains' => 1,
                'used_domains' => 1,
                'features' => ['basic_support'],
                'expires_at' => now()->subMonth(),
                'is_trial' => false,
            ],
            [
                'user_id' => $user->id,
                'license_key' => License::generateLicenseKey(),
                'status' => 'active',
                'license_type' => 'regular',
                'max_domains' => 1,
                'used_domains' => 0,
                'features' => ['basic_support'],
                'expires_at' => now()->addDays(14),
                'is_trial' => true,
            ],
        ];

        foreach ($licenses as $license) {
            License::create($license);
        }
    }
} 