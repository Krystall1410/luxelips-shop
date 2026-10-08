<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Quản Trị Viên High-End',
                'email' => 'admin@luxelips.com',
                'phone' => '0901234567',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );
    }
}
