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
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Madura Mart',
                'password' => Hash::make('salma'),
                'role' => 'admin',
                'alamat' => 'Pamekasan, Madura',
                'no_telepon' => '081234567890',
                'foto' => 'default.png',
            ]
        );
    }
}
