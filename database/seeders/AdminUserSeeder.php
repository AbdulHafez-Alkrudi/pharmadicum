<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'Salah',
            'pharmacy_name' => 'PHARMADICUM',
            'phone_number' => '0999999999',
            'password' => bcrypt('Bellingham, your uncle'),
            'role_id' => 1, // Admin
        ]);
    }
}
