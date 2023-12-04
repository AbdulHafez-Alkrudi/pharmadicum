<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderStatus::create([
            'name_EN' => "Pending",
            'name_AR' => "قيد التحضير"
        ]);
        OrderStatus::create([
            'name_EN' => 'Delivered',
            'name_AR' => 'تم الارسال'
        ]);
        OrderStatus::create([
            'name_EN' => "NULL",
            'name_AR' => "مستلمة"
        ]);
    }
}
