<?php

namespace Database\Seeders;

use App\Models\PaymentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentStatus::create([
            'name_EN' => "Unpaid",
            'name_AR' => "غير مدفوعة"
        ]);
        PaymentStatus::create([
            'name_EN' => 'Paid',
            'name_AR' => 'مدفوعة'
        ]);
    }
}
