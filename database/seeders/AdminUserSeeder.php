<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::query()->where('slug', 'admin')->firstOrFail();

        $adminUser = User::query()->updateOrCreate(
            ['email' => 'admin@restaurant.local'],
            [
                'name' => 'مدير النظام',
                'username' => 'admin',
                'password' => Hash::make('Admin@12345'),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ]
        );

        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
